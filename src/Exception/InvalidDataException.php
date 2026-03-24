<?php

namespace App\Exception;

use \Exception;

class InvalidDataException extends Exception
{
    private $errors;

    public function __construct(array $errors, $message = 'Validation errors', $code = 422)
    {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}