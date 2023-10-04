<?php

namespace Siktec\Dmm\Model\Components;

class Dump
{
    private ?object $ref     = null;

    public function __construct(?object $ref = null)
    {
        $this->ref = $ref;
    }

    public function composition(): string
    {
        $str = "Model: " . get_class($this->ref);
        $str .= "\n\tStorage: " . $this->ref->_storage;
        $str .= "\n\tProperties: " . $this->ref->_properties;
        return $str;
    }

    public function values(): string
    {
        $values = $this->ref->_properties->values(
            external : false,
            generated : true,
            nested : true
        );

        $str = "Model: " . get_class($this->ref);

        foreach ($values as $key => $value) {
            $str .= "\n\t" . $key . ": " . $this->valueToStr($value, 1);
        }

        return $str;
    }

    private function valueToStr(mixed $value, int $ident = 0): string
    {
        $str = "";

        // First level indentation:
        if (is_array($value)) {
            $str .= "[";
            foreach ($value as $key => $val) {
                $str .= "\n" . str_repeat("\t", $ident + 1) . $key . ": " . $this->valueToStr($val, $ident + 1);
            }
            $str .= "\n" . str_repeat("\t", $ident) . "]";
        } elseif (is_null($value)) {
            $str .= "null";
        } elseif (is_bool($value)) {
            $str .= $value ? "TRUE" : "FALSE";
        } elseif (is_object($value)) {
            $str .= get_class($value);
        } else {
            $str .= $value;
        }

        return $str;
    }
}
