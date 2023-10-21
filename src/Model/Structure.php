<?php

namespace Siktec\Dmm\Model;

use \Siktec\Dmm\Exceptions;
use \Siktec\Dmm\Model\Traits;
use Siktec\Dmm\Internal\Std;
use Siktec\Dmm\Model\Components;

abstract class Structure implements IBaseModel
{

    /**
     * Helper and implementation Traits:
     */
    use Traits\ClassExportPropertiesTrait;
    use Traits\ClassStateHelpersTrait;

    /**
     * Internal components:
     */
    public Components\Storage $_storage;
    public Components\Properties $_properties;
    public Components\State $state;
    public Components\Dump $_dump;

    /**
     * @param array $values initial values for the model
     */
    public function __construct(array $values)
    {

        $this->_storage    = new Components\Storage($this, true, true);

        $this->_properties = new Components\Properties($this, true);

        $this->state = new Components\State($this);

        $this->_dump = new Components\Dump($this);

        // And at least one property:
        if (!$this->_properties->total()) {
            throw new Exceptions\ModelDeclarationException(
                [get_class($this), "Property"],
                151
            );
        }

        // A primary key is required:
        if (!$this->_properties->getPrimary()) {
            throw new Exceptions\ModelDeclarationException(
                [get_class($this)],
                157
            );
        }
        
        // Load the data into the model if any is provided:
        $filtered = $this->_properties->filter($values);

        if (!Std::arrayIsNulls($filtered)) {
            $this->fromArray($filtered, false);
        }
    }

    /**
     * Loads the data into the model
     * Its up to the model to decide how to load the data
     * This is a good place to validate the data sanitize and transform it
     * Also to genetarte any extra data that might be needed.
     * load always expects the data to be in the internal format [internal_property => value]
     *
     * @param array $data => the data to load [internal_property => value]
     *
     * @return bool
     */
    abstract protected function load(array $data): void;


    /**
     * @inheritDoc
     */
    final public function fromArray(array $data, bool $external = true): bool
    {

        $this->state->reset();

        // Always translate the data to internal format:
        if ($external) {
            $data = $this->_properties->translate($data, false);
        }

        // Filter the data to only the properties that are in the model:
        $filtered = $this->_properties->filter($data);

        //Only saved:
        $saved  = [];
        $nested = [];

        foreach ($filtered as $key => $value) {
            if ($this->_properties->isSaved($key)) {
                if ($this->_properties->isNested($key)) {
                    $nested[$key] = $value;
                } else {
                    $saved[$key] = $value;
                }
            }
        }

        // Load the saved properties:
        $this->state->loaded(true);
        $this->load($saved);

        // The nested properties:
        foreach ($nested as $key => $value) {
            if (isset($this->{$key})) {
                $this->{$key}->fromArray($value, false);
            } else {
                $this->_properties->initNested($key, $value, false);
            }
        }

        return $this->state->isValid();
    }

    /**
     * @inheritDoc
     */
    final public function fromJson(string $data, bool $external = true): bool
    {
        return $this->fromArray(
            data : json_decode($data, true),
            external : $external
        );
    }

    final public function jsonSerialize(): mixed
    {
        return $this->toArray(true, true, true);
    }
}
