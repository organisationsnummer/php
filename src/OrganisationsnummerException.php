<?php

namespace Organisationsnummer;

use Exception;
use Throwable;

class OrganisationsnummerException extends Exception {
    public function __construct(string $message = 'Invalid Swedish organization number', int $code = 0, ?Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
