<?php

namespace Siktec\Dmm\Model\Traits;

use \ReflectionClass;
use \Siktec\Dmm\Internal\Std;

trait ClassAttributesParserTrait
{
    private function extractClassMeta(object|string $attribute_class, ?object $of = null): array
    {
        $of = $of ?? $this;
        $target = new ReflectionClass($of);
        $attributes = $target->getAttributes($attribute_class);
        $meta = [];
        foreach ($attributes as $attribute) {
            $meta = Std::objectToArray($attribute->newInstance());
        }

        return $meta;
    }

    private function extractClassPropertiesMeta(
        object|string $attribute_class,
        ?object $of = null
    ): array {
        $of = $of ?? $this;
        $target = new ReflectionClass($of);
        $meta = [];
        foreach ($target->getProperties() as $property) {
            if ($attributes = $property->getAttributes($attribute_class)) {
                foreach ($attributes as $attribute) {
                    $meta[$property->getName()] = Std::objectToArray($attribute->newInstance());
                }
            }
        }
        return $meta;
    }

    private function extractClassProperties(array $properties = [], ?object $of = null): array
    {
        $of = $of ?? $this;
        $target = new ReflectionClass($of);
        $meta = [];

        foreach ($target->getProperties() as $property) {
            $name = $property->getName();

            // Skip properties that are not in the list:
            if (!in_array($name, $properties)) {
                continue;
            }

            $meta[$name] = [
                "name"       => $name,
                "default"    => $property->isDefault() ? $property->getDefaultValue() : null,
                "type"       => [],
                "nullable"   => ($property->hasType() && $property->getType()->allowsNull()),
            ];

            // Get the property type if it exists
            if ($type = $property->getType()) {
                if ($type instanceof \ReflectionNamedType) {
                    $meta[$name]["type"] = [ $type->getName() ];
                } elseif ($type instanceof \ReflectionUnionType) {
                    foreach ($type->getTypes() as $union_type) {
                        $meta[$name]["type"][] = $union_type->getName();
                    }
                } else {
                    // This is probably a Intersection type
                    // 'ReflectionIntersectionType'
                    // Which we don't support
                    ;
                }
            }
        }

        return $meta;
    }
}
