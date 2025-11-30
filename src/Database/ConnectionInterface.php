<?php
namespace App\Database;

use PDO;

interface ConnectionInterface {
    public static function getConnection(): PDO;
}
