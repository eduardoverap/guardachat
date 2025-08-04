<?php

namespace App\Models;

use PDO;

abstract class Base
{
  protected ?PDO $conn;

  public function __construct()
  {
    $this->conn = require DB_CONN;
  }

  public function getConnection(): PDO
  {
    return $this->conn;
  }

  public function __destruct()
  {
    $this->conn = null;
  }
}
