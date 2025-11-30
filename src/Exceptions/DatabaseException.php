<?php
namespace App\Exceptions;

use Exception;

class DatabaseException extends Exception {
    public function __construct(string $message = "Database error occurred") {
        parent::__construct($message);
    }
}
