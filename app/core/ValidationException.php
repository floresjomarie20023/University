<?php

namespace Core;

class ValidationException extends \Exception
{
    private array $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;
        parent::__construct('Validation failed');
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
