<?php
/*
 * @author Vitor Fernandes Cazelatto
 * abstract class Connect to database
 */

abstract class Connect {
    /* construct method */
    private function __construct() {
        
    }

    /* clone method */
    private function __clone() {
        
    }

    /* destruct method */
    public function __destruct() {
        $this->disconnect();
        foreach ($this as $key => $value) {
            unset($this->$key);
        }
    }

    private static $dbtype = "mysql";
    private static $port = "3306";
    private static $host = "db";
    private static $user = "user";
    private static $password = "test";
    private static $db = "wirecard";

    private function getDBType() {
        return self::$dbtype;
    }

    private function getHost() {
        return self::$host;
    }

    private function getPort() {
        return self::$port;
    }

    private function getUser() {
        return self::$user;
    }

    private function getPassword() {
        return self::$password;
    }

    private function getDB() {
        return self::$db;
    }
    
    /* connect to database */
    private function connect() {
        try {
            $this->conexao = new PDO($this->getDBType() . ":host=" . $this->getHost() . ";port=" . $this->getPort() . ";dbname=" . $this->getDB(), $this->getUser(), $this->getPassword());
        } catch (PDOException $i) {
            //se houver exceção, exibe
            die("Erro: <code>" . $i->getMessage() . "</code>");
        }

        return ($this->conexao);
    }

    private function disconnect() {
        $this->conexao = null;
    }

    /* select method 
    @param string $sql => SQL Query
    @param array $params [optional] => Params query
    */
    public function selectDB($sql, $params = null, $class = null) {
        $query = $this->connect()->prepare($sql);
        $query->execute($params);

        if (isset($class)) {
            $rs = $query->fetchAll(PDO::FETCH_CLASS, $class) or die(print_r($query->errorInfo(), true));
        } else {
            //$rs = $query->fetchAll(PDO::FETCH_OBJ) or die(print_r($query->errorInfo(), true));
            $rs = $query->fetchAll(PDO::FETCH_OBJ);
        }
        self::__destruct();
        return $rs;
    }

    /* insert method 
    @param string $sql => SQL Query
    @param array $params [optional] => Params query
    */
    public function insertDB($sql, $params = null) {
        $conexao = $this->connect();
        $query = $conexao->prepare($sql);
        $query->execute($params);
        $rs = $conexao->lastInsertId() or die(print_r($query->errorInfo(), true));
        self::__destruct();
        return $rs;
    }

   /* update method 
    @param string $sql => SQL Query
    @param array $params [optional] => Params query
    */
    public function updateDB($sql, $params = null) {
        $query = $this->connect()->prepare($sql);
        if (!$query->execute($params)) {
            return false;
        }

        $rs = $query->rowCount();
        self::__destruct();
        return $rs;
    }

   /* delete method 
    @param string $sql => SQL Query
    @param array $params [optional] => Params query
    */
    public function deleteDB($sql, $params = null) {
        $query = $this->connect()->prepare($sql);
        $query->execute($params);
        $rs = $query->rowCount();
        self::__destruct();
        return $rs;
    }

}
