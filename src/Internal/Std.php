<?php 

namespace Siktec\Dmm\Internal;

use \Siktec\Dmm\Exceptions;

class Std
{
    /**
     * will convert an object to an array
     * only public properties will be converted
     * any value that is not a string, int, float, bool, array or null 
     * will be filtered out.
     * 
     * @param object $object
     * 
     * @return array
     */
    public static function objectToArray(object $object): array
    {
        return array_filter(
            (array) $object, 
            function($value, $key) {
                return !str_starts_with($key, "\0") && 
                    in_array(
                        gettype($value), 
                        ['string', 'int', 'float', 'bool', 'array', 'NULL']
                    );
            }, 
            ARRAY_FILTER_USE_BOTH
        );
    }

    /**
     * will check if an array contains only null values
     * 
     * @param array $array
     * 
     * @return bool
     */
    public static function arrayIsNulls(array $array) : bool
    {
        return count(array_filter($array, fn($value) => $value !== null)) === 0;
    }

    public static function intersect_keys($keys, $assoc) : array
    {
        return array_intersect_key($assoc, array_flip($keys));
    }

    public static function safeJsonEncode(mixed $data, bool $throw = false, bool $pretty = false) : array 
    {
        
        $json = json_encode($data, $pretty ? JSON_PRETTY_PRINT : 0);

        if ($json === false && $throw) {
            throw new Exceptions\DmmException(
                ["JSON", json_last_error_msg()], 
                131
            );
        }
        
        return [
            $json !== false,
            $json !== false ? $json : json_last_error_msg()
        ];
    }
    
}