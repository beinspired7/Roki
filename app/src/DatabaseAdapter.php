<?php

class DatabaseAdapter
{
    private $conn;

    public function __construct()
    {
        $config = require __DIR__ . '/config/database.php';
        $host = $config['host'];
        $user = $config['user'];
        $password = $config['password'];
        $database = $config['database'];
        $this->conn = new PDO("mysql:host={$host};dbname={$database}", $user, $password);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    /**
     * @return PDO
     */
    public function getConn()
    {
        return $this->conn;
    }

    public function insert($tabelName, $propeties)
    {
        $attributes = array_keys($propeties);
        $values = [];
        $attributes = '`' . implode("`, `", $attributes) . '`';
        foreach (array_values($propeties) as $value) {
            if (is_null($value)) {
                $values[] = 'NULL';
                continue;
            }
            $values[] = "'" . $value . "'";
        }
        $values = implode(", ", $values);
        $sql = "INSERT INTO `{$tabelName}` ({$attributes}) VALUES ({$values})";
        if ($this->conn->query($sql)) {
            return $this->conn->lastInsertId();
        } else {
            return false;
        }
    }
    public function login($tabelname, $name, $password){
        $selectlogin = $this->conn->prepare("SELECT * FROM `{$tabelname}` WHERE `name` = ? and `password` = ?");

        $selectlogin->execute([$name, $password]);
        $login = $selectlogin->fetch();
        return $login;
    }

    public function update($tablename, $id, $properties)
    {
        $pdoupdate = [];
        foreach ($properties as $attribute => $value) {
            $pdoupdate[] = "`{$attribute}` = '{$value}'";
        }
        $update = implode(',', $pdoupdate);
        $pdoUpdatequrey = " UPDATE `{$tablename}` SET {$update} WHERE `id` = '{$id}'";
        if ($this->conn->query($pdoUpdatequrey)) {
            return true;
        } else {
            return false;
        }
    }

    public function select($tabelname, $id)
    {
        $selectquery = $this->conn->prepare("SELECT * FROM `{$tabelname}` WHERE id=:id");

        $selectquery->execute(['id' => $id]);
        $user = $selectquery->fetch();
        return $user;
    }
    public function delete($tabelname, $id)
    {
        $pdodelete = $this->conn->prepare("DELETE FROM `{$tabelname}` WHERE id=?")->execute([$id]);
        return $pdodelete;
    }

    public function selectAll($tabelname)
    {
        $pdoselectall = $this->conn->query("SELECT * FROM `{$tabelname}`")->fetchAll();
        return $pdoselectall;
    }
    public function queryAll($sql){
        $pdoselectall = $this->conn->query($sql)->fetchAll();
        return $pdoselectall;
    }
    public function queryOne($sql){
        $queryone = $this->conn->query($sql)->fetch();
        return $queryone;
    }
}
