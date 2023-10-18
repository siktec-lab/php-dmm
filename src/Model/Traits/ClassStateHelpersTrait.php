<?php

namespace Siktec\Dmm\Model\Traits;

use Siktec\Dmm\Internal\Std;

trait ClassStateHelpersTrait
{

    /**
     * Returns true if the model is loaded and valid
     *
     * @param bool $nested if true the nested properties will be validated recursively
     * @param bool $allow_null if true null values will be considered valid if they are not required
     * 
     * @return bool 
     */
    final public function isValid(bool $nested = true, bool $allow_null = true): bool
    {
        return $this->state->isValid($nested, $allow_null);
    }

    /**
     * Returns validation errors
     *
     * @return array
    */
    final public function validation(): array
    {
        return $this->state->validation();
    }

    /**
     * Resets the model state to its initial state
     * Will also reset the state of all nested properties
     * @return void
     */
    public function reset(): void
    {
        $this->state->reset();
    }
}
