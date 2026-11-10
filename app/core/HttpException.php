<?php

namespace Core;

class HttpException extends \Exception
{
    public function __construct(int $statusCode, string $message = '')
    {
        $messages = [
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
        ];
        parent::__construct($message ?: ($messages[$statusCode] ?? 'Error'), $statusCode);
    }

    public function getStatusCode(): int
    {
        return $this->getCode();
    }
}
