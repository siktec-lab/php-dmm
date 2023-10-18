<?php

namespace Siktec\Dmm\Model;

interface IBaseModel
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
    public function toArray(bool $external, bool $generated, bool $nested): array;

    /**
     * Loads the data from an array into the model
     *
     * @param array $data the data to load
     * @param bool $external if true the data will be considered to be in the external format
     *
     * @return bool true if the data was loaded successfully and the model is valid
     */
    public function fromArray(array $data, bool $external): bool;

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
    public function toJson(bool $external, bool $generated, bool $nested, bool $pretty): string;

    /**
     * Loads the data from a JSON string into the model
     *
     * @param string $data the data to load
     * @param bool $external if true the data will be considered to be in the external format
     * 
     * @return bool true if the data was loaded successfully and the model is valid
     */
    public function fromJson(string $data, bool $external): bool;

    /**
     * Returns true if the model is loaded and valid
     *
     * @param bool $nested if true the nested properties will be validated recursively
     * @param bool $allow_null if true null values will be considered valid if they are not required
     * 
     * @return bool 
     */
    public function isValid(bool $nested, bool $allow_null): bool;

    /**
     * Returns validation errors
     *
     * @return array
    */
    public function validation(): array;
}
