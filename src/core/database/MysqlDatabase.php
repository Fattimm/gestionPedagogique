<?php

namespace Src\Core\Database;

use PDO;

interface DatabaseInterface {
    public function getPDO();
}

class MysqlDatabase implements DatabaseInterface {
    private $pdo;

    public function __construct($dsn, $user, $password) {
        $this->pdo = new PDO($dsn, $user, $password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getPDO() {
        return $this->pdo;
    }
}
