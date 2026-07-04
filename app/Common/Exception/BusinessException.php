<?php

namespace App\Common\Exception;

use Exception;

class BusinessException extends Exception
{
    public function __construct(string $message, int $code = 400)
    {
        parent::__construct($message, $code);
    }
}
