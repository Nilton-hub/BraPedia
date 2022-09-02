<?php

namespace src\core\Exception;

use Throwable;
use DomainException;

class EmailException extends DomainException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
