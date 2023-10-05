<?php

namespace Siktec\Dmm\Exceptions;

use \Exception;

class ModelDeclarationException extends DmmException
{
    protected int $DEFAULT_CODE    = 150;
    protected array $ERROR_MESSAGE = [
        150 => ["An error occurred while declaring model '%s'", 1],
        151 => ["Model '%s' is missing the '%s' attribute", 2],
        152 => ["Model '%s' has a property with multiple types (%s) which is not supported yet", 2],
        153 => ["Model '%s' has no property named '%s' of type '%s'", 3],
        154 => ["Model '%s' can't assign property '%s' of type '%s' with type '%s'", 4],
        155 => ["Model '%s' has invalid attribute (%s) values - %s.", 3],
    ];

    /**
     * ModelDeclarationException constructor.
     *
     * @param string|array $model the name of the model
     * @param int|null $code the error code (default 150)
     * @param Exception|null $previous the previous exception
     *
     * @return self
     */
    public function __construct(
        string|array $model,
        ?int $code = null,
        ?Exception $previous = null
    ) {
        parent::__construct(
            is_array($model) ? $model : [$model],
            $code,
            $previous
        );
    }
}
