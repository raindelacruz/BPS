<?php

namespace App\Models;

use Bootstrap\Database;
use PDO;

abstract class BaseModel
{
    protected ?PDO $db = null;

    protected function connection(): PDO
    {
        if (!$this->db instanceof PDO) {
            $this->db = Database::connection();
        }

        return $this->db;
    }
}
