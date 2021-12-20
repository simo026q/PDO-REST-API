<?php

namespace simo026q\Database;

use simo026q\Response\Response;

/**
 * @author simo026q
 * @copyright Copyright (c) 2021 simo026q
 * @license MIT License
 */
abstract class Database
{
    protected $host, $database, $username, $password, $connection, $port, $driver;

    // https://www.php.net/manual/en/pdo.drivers.php
    public const PDO_CUSTOM = 0,
        PDO_CUBRID = 1, // "cubrid:dbname=$this->database;host=$this->host;port=$this->port"
        PDO_FIREBIRD = 2,
        PDO_MYSQL = 3, // "mysql:host=$this->host;port=$this->port;dbname=$this->database"
        PDO_OCI = 4, // "oci:dbname=//$this->host:$this->port/$this->database"
        PDO_ODBC = 5, // "odbc:DRIVER={IBM DB2 ODBC DRIVER};HOSTNAME=$this->host;PORT=$this->port;DATABASE=$this->database;PROTOCOL=TCPIP;UID=$this->username;PWD=$this->password;"
        PDO_PGSQL = 6, // "pgsql:host=$this->host;port=$this->port;dbname=$this->database;user=$this->username;password=$this->password"
        PDO_SQLITE = 7,
        PDO_SQLSRV = 8; // "sqlsrv:Server=$this->host,$this->port;Database=$this->database"

    /**
     * Connect to database
     */
    function connect($dsn = "")
    {
        try {
            $dsnStr = (empty($dsn)) ? $this->dsn() : $dsn;
            $this->connection = new \PDO($dsnStr, $this->username, $this->password);
            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $err) {
            return $err->getMessage();
        }
    }

    /**
     * Close connection from database
     */
    function disconnect()
    {
        $this->connection = null;
    }

    /**
     * @return bool If the connection is open
     */
    function isConnected()
    {
        return $this->connection != null;
    }

    /**
     * Execute query
     * @param string $query Query string
     * @param bool $convertType Convert column values
     * @return Response Response object
     */
    abstract function query($query, $convertType): Response;

    /**
     * @return string DSN String
     */
    abstract protected function dsn(): string;
}