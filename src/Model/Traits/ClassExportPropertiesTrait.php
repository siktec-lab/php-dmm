<?php 

namespace Siktec\PhpRedis\Model\Traits;

use ReflectionClass;
use Siktec\PhpRedis\Internal\Std;
use Siktec\PhpRedis\Model\IBaseModel;
use Siktec\PhpRedis\Model\Structure;


trait ClassExportPropertiesTrait {

    private function propToArray(mixed $value, bool $external = true, bool $generated = true) : mixed
    {
        switch (gettype($value)) {
            case 'object':
                return $value instanceof IntModel ?
                            $value->toArray($external, $generated) :
                            Std::objectToArray($value);
            case 'string':
            case 'int':
            case 'float':
            case 'bool':
            case 'NULL':
                return $value;
            case 'array':
                return array_map(function($item) use ($external, $generated) {
                    return $this->propToArray($item, $external, $generated);
                }, $value);
            default:
                throw new \Exception('Invalid type: ' . gettype($value));
        }
    }

    public function toArray(bool $external = true, bool $generated = true) : array
    {
        if ($this->isLoaded()) {
            return [];
        }
        
        $export = [];

        foreach ($this->properties() as $in_name => $ex_name) {

            if (!$generated && in_array($in_name, $this->generated)) {
                continue;
            }
            $property = $this->{$in_name};
            $export[
                $external ? $ex_name : $in_name
            ] = $this->propToArray($property, $external, $generated);
        }

        return $export;
    }

    public function toJson(bool $external = true, bool $generated = true) : string
    {
        return json_encode($this->toArray($external, $generated));
    }

    // JsonSerializable
    public function jsonSerialize() : array
    {
        return $this->toArray(true, true);
    }

    public function fromArray(array $data, bool $external = true) : bool
    {
        $data = array_intersect_key(
            $external ? $this->translate($data, false) : $data,
            $this->map["in_out"]
        );
        $this->loaded = $this->load($data);
        return $this->loaded;
    }

    // unserialize from json
    public function fromJson(string $json, bool $external = true) : bool
    {
        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid json: ' . json_last_error_msg());
        }
        return $this->fromArray($data, $external);
    }

}