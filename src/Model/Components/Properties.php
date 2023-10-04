<?php

namespace Siktec\Dmm\Model\Components;

use \Siktec\Dmm\Exceptions;
use \Siktec\Dmm\Model\Traits;
use \Siktec\Dmm\Model\Attr;
use \Siktec\Dmm\Model\IBaseModel;
use Stringable;

class Properties
{
    use Traits\ClassAttributesParserTrait;

    public const ATTR_PROPERTY_NAME = "name";

    private ?object $ref        = null;
    private int $total        = 0;
    private array $properties   = [];
    private array $in_out       = [];
    private array $out_in       = [];
    private array $saved        = [];
    private array $generated    = [];
    private array $nested       = [];

    public function __construct(?object $ref = null, bool $parse = true)
    {
        $this->ref = $ref;
        if ($parse) {
            $this->parse($ref);
        }
    }

    public function parse(): int
    {

        $attr_properties = $this->extractClassPropertiesMeta(Attr\Property::class, $this->ref);
        $attr_generated  = $this->extractClassPropertiesMeta(Attr\Generated::class, $this->ref);
        $internal_names  = array_keys($attr_properties);
        $properties = $this->extractClassProperties($internal_names, $this->ref);

        // Naming maps:
        foreach ($attr_properties as $in => $values) {
            $this->in_out[$in] = $values[self::ATTR_PROPERTY_NAME] ?: $in;
        }

        $this->total        = count($properties);
        $this->properties   = $properties;
        $this->out_in       = array_flip($this->in_out);
        $this->generated    = array_keys($attr_generated);
        $this->parseNested();
        $this->saved        = array_diff(array_keys($this->in_out), $this->generated);

        return $this->total;
    }

    private function parseNested(): void
    {

        foreach ($this->properties as &$property) {
            //Only one type is allowed:
            if (count($property["type"]) !== 1) {
                throw new Exceptions\ModelDeclarationException(
                    [get_class($this->ref), implode(",", $property["type"])],
                    152
                );
            }

            // Truncate the array to a single value:
            $property["type"] = $property["type"][0];

            // check if the property is a model:
            if (is_a($property["type"], IBaseModel::class, true)) {
                $this->nested[] = $property["name"];
                $property["nested"] = true;
            } else {
                $property["nested"] = false;
            }
        }
    }

    public function total(): int
    {
        return $this->total;
    }

    public function names(bool $external_keys = false): array
    {
        return $external_keys ? $this->out_in : $this->in_out;
    }

    public function translate(string|array $name, bool $to_external): string|array|null
    {
        if (is_array($name)) {
            $translated = [];
            foreach ($name as $key => $value) {
                if ($trans_key = $this->translate($key, $to_external)) {
                    $translated[$trans_key] = $value;
                }
            }
            return $translated;
        }
        return $to_external ?
            $this->in_out[$name] ?? null :
            $this->out_in[$name] ?? null;
    }

    public function toExternal(string|array $name): string|array|null
    {
        return $this->translate($name, true);
    }

    public function toInternal(string|array $name): string|array|null
    {
        return $this->translate($name, false);
    }

    public function isSaved(string $name, bool $external = false): bool
    {
        $name = $external ? $this->toInternal($name) : $name;
        return in_array($name, $this->saved);
    }

    public function isGenerated(string $name, bool $external = false): bool
    {
        $name = $external ? $this->toInternal($name) : $name;
        return in_array($name, $this->generated);
    }

    public function isNested(string $name, bool $external = false): bool
    {
        $name = $external ? $this->toInternal($name) : $name;
        return $this->properties[$name]["nested"];
    }

    public function getSaved(): array
    {
        return $this->saved;
    }

    public function getGenerated(): array
    {
        return $this->generated;
    }

    public function getNested(): array
    {
        return $this->nested;
    }

    public function values(
        bool $external = false,
        bool $generated = true,
        bool $nested = true
    ): array {
        $values = [];
        foreach ($this->properties as $in_name => $property) {
            $is_generated = $this->isGenerated($in_name);
            $is_nested    = $this->isNested($in_name);
            $is_array     = $property["type"] === "array";

            // Skip generated and nested properties if not requested:
            if ((!$generated && $is_generated) || (!$nested && $is_nested)) {
                continue;
            }

            // The name to use:
            $name = $external ? $this->toExternal($in_name) : $in_name;

            //Make sure the property is initialized or set null:
            $value = isset($this->ref->{$in_name}) ? $this->ref->{$in_name} : null;

            // early set if the value is null:
            if (is_null($value)) {
                $values[$name] = null;
                continue;
            }

            //Process a nested model:
            if ($is_nested) {
                if ($nested) {
                    $values[$name] = $value->_properties->values(
                        external    : $external,
                        generated   : $generated,
                        nested      : $nested
                    );
                }
                // skip if the property is nested but nested is not requested:
                continue;
            }

            //process an object (only if it implements toArray or __toString or __toJsonString):
            if (is_object($value)) {
                if (method_exists($value, "toArray")) {
                    $values[$name] = $value->toArray();
                } elseif ($value instanceof Stringable) {
                    $values[$name] = (string) $value;
                }
                // skip if the object is not a stringable or does not implement toArray:
                continue;
            }

            //Simple value:
            $values[$name] = $value;
        }
        return $values;
    }

    public function filter(array $data, bool $external = false): array
    {
        return array_intersect_key(
            $external ? $this->translate($data, false) : $data,
            $this->in_out
        );
    }

    public function initNested(string $internal_name, array|object|null $data, bool $external = false): bool
    {

        // Check if the property is nested which also means it is a model and it exists:
        if (!$this->isNested($internal_name)) {
            throw new Exceptions\ModelDeclarationException(
                [get_class($this->ref), $internal_name, "Nested"],
                153
            );
        }

        $definition = $this->properties[$internal_name];
        $is_nullable = $definition["nullable"] ?? false;

        // handle null data:
        if (is_null($data)) {
            if (!$is_nullable) {
                throw new Exceptions\ModelDeclarationException(
                    [
                        get_class($this->ref),
                        $internal_name,
                        $definition["type"],
                        "NULL"
                    ],
                    154
                );
            }
            $this->ref->{$internal_name} = null;
            return true;
        }

        // handle object data:
        if (is_object($data)) {
            if (!$data instanceof $definition["type"]) {
                throw new Exceptions\ModelDeclarationException(
                    [
                        get_class($this->ref),
                        $internal_name,
                        $definition["type"],
                        get_class($data)
                    ],
                    154
                );
            }
            $this->ref->{$internal_name} = $data;
            return true;
        }

        // handle array data:
        if (is_array($data)) {
            if (!isset($this->ref->{$internal_name})) {
                $this->ref->{$internal_name} = new $definition["type"]();
            }
            return $this->ref->{$internal_name}->fromArray($data, $external);
        }

        // handle other data types:
        throw new Exceptions\ModelDeclarationException(
            [
                get_class($this->ref),
                $internal_name,
                $definition["type"],
                gettype($data)
            ],
            154
        );
    }

    public function __toString()
    {
        $str = [];
        $i = 0;
        foreach ($this->properties as $internal => $property) {
            $external = $this->toExternal($internal);
            $str[] = "\n\t\t" . (++$i) .
                            ") [{$property["type"]}]" .
                            ": {$internal} => {$external}" .
                            ($this->isSaved($internal) ? " (saved)" : "") .
                            ($this->isGenerated($internal) ? " (generated)" : "") .
                            ($this->isNested($internal) ? " (nested)" : "");
        }
        return implode("", $str);
    }
}
