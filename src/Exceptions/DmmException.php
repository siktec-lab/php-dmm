<?php 

namespace Siktec\Dmm\Exceptions;

use \Exception;

abstract Class DmmException extends Exception {

    protected int $DEFAULT_CODE          = 130;
    protected string $DEFAULT_PARAM      = 'UNKNOWN';
    protected string $DEFAULT_ADDITIONAL = 'NONE';
    protected array $ERROR_MESSAGE = [
        130 => [ "An error occurred", 0 ],
        131 => [ "Serialization error with method %s", 1 ],
    ];


    /**
     * DmmException constructor.
     * 
     * @param array $data nothind specific for this exception
     * @param ?int $code the error code (default 130)
     * @param Exception|null $previous the previous exception
     * 
     * @return self
     */
    public function __construct(
        array $data,
        ?int $code = null, 
        ?Exception $previous = null
    ) {
        $code = $code ?? $this->DEFAULT_CODE;
        // call the parent constructor with the prepared message
        parent::__construct(
            $this->prepare_message($data, $code, $previous),
            $code,
            null
        );
    }
    
    /**
     * Prepare the message to be used in the exception
     * 
     * @param array $data the data to use in the message
     * @param int $code the error code
     * @param Exception|null $prev the previous exception
     * 
     * @return string
     */
    private function prepare_message(array $data, int $code, ?Exception $prev = null) : string
    {
        
        $message  = $this->ERROR_MESSAGE[$code] ?? $this->ERROR_MESSAGE[$this->DEFAULT_CODE];
        $template = $message[0];
        $params   = intval($message[1]);


        // splice the data array to match the number of parameters

        return sprintf(
            $template,
            ...array_pad(array_splice($data, 0, $params), $params, $this->DEFAULT_PARAM)
        )." ".$this->additional($prev);
    }

    /**
     * Add additional information to the message
     * depending on the previous exception
     * 
     * @param Exception|null $previous
     * 
     * @return string
     */
    private function additional(?Exception $previous = null) : string
    {
        return $previous ? 
            " - Additional: (" . $previous->getCode() . ") " .$previous->getMessage() : 
            " - Additional: " . $this->DEFAULT_ADDITIONAL;
    }
}