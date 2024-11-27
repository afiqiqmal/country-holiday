<?php

namespace Holiday\Exception;

class CountryNotSupportedException extends \InvalidArgumentException
{
    /**
     * Create a new CountryNotSupportedException.
     *
     * @param string $message Error message
     * @param int $code Error code (optional)
     * @param \Throwable|null $previous Previous exception (optional)
     */
    public function __construct(
        string $message = "",
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, 400, $previous);
    }
}