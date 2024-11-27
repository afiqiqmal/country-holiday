<?php
namespace Holiday\Exception;

use Throwable;

class RegionException extends \InvalidArgumentException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, 400, $previous);
    }
}