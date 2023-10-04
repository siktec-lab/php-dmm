<?php

namespace Siktec\PhpRedis\Model\Components;

class State {

    private ?object $ref     = null;
    private bool  $loaded   = false;
    private array $validation = [];

    public function __construct(?object $ref = null)
    {
        $this->ref = $ref;
    }
    public function reset() : void
    {
        // Set state of self:
        $this->loaded   = false;
        $this->validation = [];

        // Loop through nested properties:
        foreach ($this->ref->_properties->getNested() as $nest) {
            if (isset($this->ref->{$nest})) {
                $this->ref->{$nest}->_state->reset();
            }
        }

    }

    public function loaded(bool $status) : void
    {
        $this->loaded = $status;
    }

    public function isLoaded(bool $nested = true, bool $allow_null = true) : bool
    {
        // Early return if not loaded: 
        if (!$nested || !$this->loaded) {
            return $this->loaded;
        }

        // Loop through nested properties:
        foreach ($this->ref->_properties->getNested() as $nest) {
            // Null or not intialized:
            if (!$allow_null && is_null($this->ref->{$nest})) {
                return false;
            }

            // Null and allowed:
            if (is_null($this->ref->{$nest})) {
                continue;
            }

            // Not initialized:
            if (!isset($this->ref->{$nest})) {
                return false;
            }

            // Not loaded:
            if (!$this->ref->{$nest}->_state->isLoaded(true, $allow_null)) {
                return false;
            }
        }
        return true;
    }

    final public function hasValidation(bool $nested = true) : bool
    {
        return !empty($this->validation($nested));
    }

    final public function invalid(string $property, string $message) : void
    {
        if (!array_key_exists($property, $this->validation)) {
            $this->validation[$property] = [];
        }
        $this->validation[$property][] = $message;
        // Change state to not loaded:
        $this->loaded = false;
    }

    final public function resetValidation(bool $nested = true) : void
    {
        $this->validation = [];
        foreach ($this->ref->_properties->getNested() as $nest) {
            if (isset($this->ref->{$nest})) {
                $this->ref->{$nest}->_state->resetValidation();
            }
        }
    }

    final public function validation() : array
    {   
        $validation = $this->validation;
        foreach ($this->ref->_properties->getNested() as $nest) {
            if (isset($this->ref->{$nest})) {
                $nest_validation = $this->ref->{$nest}->_state->validation();
                foreach ($nest_validation as $property => $errors) {
                    $key = $nest.".".$property;
                    if (!array_key_exists($key, $validation)) {
                        $validation[$key] = [];
                    }
                    $validation[$key] = $errors;
                }
            }
        }
        return $validation;
    }

    final public function validationFor(string $property) : array
    {
        return $this->validation[$property] ?? [];
    }

    final public function validationMessages() : array
    {
        $messages = [];
        foreach ($this->validation as $property => $errors) {
            foreach ($errors as $error) {
                $messages[] = "$property: $error";
            }
        }
        return $messages;
    }
}
    