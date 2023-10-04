<?php

namespace Siktec\Dmm\Exceptions;

use \Exception;

class ConnectionException extends DmmException
{
    protected int $DEFAULT_CODE    = 140;
    protected array $ERROR_MESSAGE = [
        140 => ["An error occurred while connecting to Redis (connection name '%s')", 1],
        141 => ["Unknown connection name '%s' was requested",                         1]
    ];

    /**
     * ConnectionException constructor.
     *
     * @param string|array $name
     * @param int|null $code the error code (default 140)
     * @param Exception|null $previous the previous exception
     *
     * @return self
     */
    public function __construct(
        string|array $name,
        ?int         $code     = null,
        ?Exception   $previous = null
    ) {
        parent::__construct(
            is_array($name) ? $name : [$name],
            $code,
            $previous
        );
    }
}
