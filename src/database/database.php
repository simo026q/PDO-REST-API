<?php

namespace simo026q\Database;

use PDO;
use PDOException;
use PDOStatement;

/**
 * @author simo026q
 * @copyright Copyright (c) 2021 simo026q
 * @license MIT License
 */
abstract class Database
{
    protected string $host, $database, $username, $password;
    protected int $port, $driver;
    protected PDO $connection;

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
     * @return string|void Return an error message
     */
    function connect($dsn = "")
    {
        try {
            $dsnStr = (empty($dsn)) ? $this->dsn() : $dsn;
            $this->connection = new PDO($dsnStr, $this->username, $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $err) {
            return $err->getMessage();
        }
    }

    /**
     * Close connection from database
     */
    function disconnect(): void
    {
        $this->connection = null;
    }

    /**
     * If the database is connected
     */
    function isConnected(): bool
    {
        return $this->connection != null;
    }

    /**
     * Execute query
     * @param string $query Query string
     */
    final function query($query)
    {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $err) {
            error_log($err->getMessage());
            return false;
        }
    }

    final function getPrimaryKey($table)
    {
        $query = "SELECT * FROM $table LIMIT 1";

        if ($stmt = $this->query($query)) {
            $primary = "";

            foreach (range(0, $stmt->columnCount() - 1) as $column_index) {
                $columnMeta = $stmt->getColumnMeta($column_index);
                if (in_array("primary_key", $columnMeta["flags"])) {
                    $primary = $columnMeta["name"];
                    continue;
                }
            }

            return $primary;
        }

        return false;
    }

    final function fetch($query, $convertDatatype = true): array
    {
        $convertDatatype = ($this->driver == self::PDO_MYSQL || $this->driver == self::PDO_PGSQL || $this->driver == self::PDO_SQLITE) ? $convertDatatype : false;
        $content = [];

        $stmt = $this->query($query);

        // If any rows is returned
        if ($stmt->rowCount() > 0) {

            // Get all column types
            if ($convertDatatype) {
                foreach (range(0, $stmt->columnCount() - 1) as $column_index) {
                    $columnMeta = $stmt->getColumnMeta($column_index);
                    $meta[$columnMeta["name"]] = $columnMeta["native_type"];
                }
            }

            // Get all data
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
                $rowData = array();
                foreach ($row as $key => $val) {
                    // Convert to the correct type ($convertType if true)
                    if (isset($meta[$key]) && $convertDatatype) {
                        $rowData[$key] = self::convertDatatype($val, $meta[$key]);
                    } else {
                        $rowData[$key] = $val;
                    }
                }
                array_push($content, $rowData);
            }
        }

        return $content;
    }

    /**
     * @return string DSN String
     */
    abstract protected function dsn(): string;

    /**
     * Convert the $value to the correct php type - Works for PDO_MYSQL, PDO_PGSQL & PDO_SQLITE only
     * @param string $type
     */
    final protected static function convertDatatype($value, $type)
    {
        switch ($type) {
            case "TINY":
                $returnVal = (int)$value;
                break;
            case "SHORT":
                $returnVal = (int)$value;
                break;
            case "LONG":
                $returnVal = (int)$value;
                break;
            case "LONGLONG":
                $returnVal = (int)$value;
                break;
            case "INT24":
                $returnVal = (int)$value;
                break;
            case "FLOAT":
                $returnVal = (float)$value;
                break;
            case "DOUBLE":
                $returnVal = (float)$value;
                break;
            case "NEWDECIMAL":
                $returnVal = (float)$value;
                break;
            default:
                $returnVal = $value;
                break;
        }
        return $returnVal;
    }
}
