<?php namespace Qualia\Exceptions;

class ValidationException extends RequestException
{
    /**
     * @var array
     */
    private $errors;

    public function __construct(
        $message = "",
        $code = 0,
        $errors = array()
    )
    {
        parent::__construct($message, $code, null);
        $this->errors = $errors;
    }

    /**
     * Returns validation errors
     *
     * @return array
     */
    public function errors()
    {
        return $this->errors;
    }
}