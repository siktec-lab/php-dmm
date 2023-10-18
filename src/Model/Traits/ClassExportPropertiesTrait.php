<?php

namespace Siktec\Dmm\Model\Traits;

use Siktec\Dmm\Internal\Std;

trait ClassExportPropertiesTrait
{
    /**
     * Exports the data from the model to an array
     *
     * @param bool $external if true the returned data will be in the external format
     * @param bool $generated if true the returned data will include generated properties
     * @param bool $nested if true the returned data will include nested properties recursively
     *
     * @return array the data in the external or internal format
     */
    public function toArray(bool $external = true, bool $generated = true, bool $nested = true): array
    {
        return $this->_properties->values($external, $generated, $nested);
    }

    /**
     * Exports the data from the model to a JSON string
     *
     * @param bool $external if true the returned data will be in the external format
     * @param bool $generated if true the returned data will include generated properties
     * @param bool $nested if true the returned data will include nested properties recursively
     * @param bool $pretty if true the returned JSON string will be pretty formated (indented) 
     *
     * @return string the data in the external or internal format
     */
    public function toJson(
        bool $external = true,
        bool $generated = true,
        bool $nested = true,
        bool $pretty = false
    ): string
    {
        [, $str] = Std::safeJsonEncode(
            data : $this->toArray($external, $generated, $nested),
            throw : true,
            pretty : $pretty
        );
        return $str;
    }
}
