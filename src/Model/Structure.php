<?php 

namespace Siktec\Dmm\Model;

use \Siktec\Dmm\Exceptions;
use Siktec\Dmm\Internal\Std;
use Siktec\Dmm\Model\Components;

abstract class Structure implements IBaseModel {

    public Components\Storage $_storage;
    public Components\Properties $_properties;
    public Components\State $_state;
    public Components\Dump $_dump;


    public function __construct(array $values) 
    {
        
        $this->_storage    = new Components\Storage($this, true, true);

        $this->_properties = new Components\Properties($this, true);

        $this->_state = new Components\State($this);

        $this->_dump = new Components\Dump($this);

        // And at least one property:
        if (!$this->_properties->total()) {
            throw new Exceptions\ModelDeclarationException(
                [get_class($this), "Property"], 
                151
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
    abstract protected function load(array $data) : void;

    public function reset() : void
    {
        $this->_state->reset();
    }

    final public function toArray(bool $external = true, bool $generated = true, bool $nested = true): array
    {
        return $this->_properties->values($external, $generated, $nested);
    }

    final public function toJson(
        bool $external  = true, 
        bool $generated = true, 
        bool $nested    = true, 
        bool $pretty    = false
    ) : string
    {
        [, $str] = Std::safeJsonEncode(
            data : $this->toArray($external, $generated, $nested),
            throw : true,
            pretty : $pretty
        );
        return $str;
    }

    final public function fromArray(array $data, bool $external = true) : bool {
        
        $this->reset();

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
        $this->_state->loaded(true);
        $this->load($saved);

        // The nested properties:
        foreach ($nested as $key => $value) {
            if (isset($this->{$key})) {
                $this->{$key}->fromArray($value, false);
            } else {
                $this->_properties->initNested($key, $value, false);
            }
        }

        return $this->_state->isLoaded(); // TODO: change to isValid later
    }

    final public function fromJson(string $data, bool $external) : bool {
        return $this->fromArray(
            data : json_decode($data, true),
            external : $external
        );
    }
}