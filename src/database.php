<?php

namespace simo026q\Database;

use simo026q\Response\Response;
use simo026q\Response\Error;

/**
 * @author simo026q
 * @copyright Copyright (c) 2021 simo026q
 * @license MIT License
 */
abstract class Database
{
    protected $host, $database, $username, $password, $connection, $port, $driver;

    // https://www.php.net/manual/en/pdo.drivers.php
    // Full support:
    // MySQL
    //
    // Partial support:
    // Cubrid
    //
    // Planned support:
    // Firebird, OCI, ODBC, SQLite, SQLSRV
    public const PDO_CUSTOM = 0,
        PDO_CUBRID = 1,
        PDO_FIREBIRD = 2,
        PDO_MYSQL = 3,
        PDO_OCI = 4,
        PDO_ODBC = 5,
        PDO_PGSQL = 6,
        PDO_SQLITE = 7,
        PDO_SQLSRV = 8;

    abstract function query($query, $convertType = true): Response;

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
        return ($this->connection != null);
    }

    /**
     * @return string DSN String
     */
    protected function dsn()
    {
        switch ($this->driver) {
            case self::PDO_CUBRID:
                return $this->cubridDsn();
                break;
            case self::PDO_FIREBIRD:
                return $this->firebirdDsn();
                break;
            case self::PDO_MYSQL:
                return $this->mysqlDsn();
                break;
            case self::PDO_OCI:
                return $this->ociDsn();
                break;
            case self::PDO_ODBC:
                return $this->odbcDsn();
                break;
            case self::PDO_PGSQL:
                return $this->pgsqlDsn();
                break;
            case self::PDO_SQLITE:
                return $this->sqliteDsn();
                break;
            case self::PDO_SQLSRV:
                return $this->sqlsrvDsn();
                break;
        }
    }

    private function cubridDsn()
    {
        return "cubrid:dbname=$this->database;host=$this->host;port=$this->port";
    }
    // https://www.php.net/manual/en/ref.pdo-firebird.php
    private function firebirdDsn()
    {
        return "?";
    }
    private function mysqlDsn()
    {
        return "mysql:host=$this->host;dbname=$this->database";
    }
    private function ociDsn()
    {
        return "oci:dbname=//$this->host:$this->port/$this->database";
    }
    private function odbcDsn()
    {
        return "odbc:DRIVER={IBM DB2 ODBC DRIVER};HOSTNAME=$this->host;PORT=$this->port;DATABASE=$this->database;PROTOCOL=TCPIP;UID=$this->username;PWD=$this->password;";
    }
    private function pgsqlDsn()
    {
        return "pgsql:host=$this->host;port=$this->port;dbname=$this->database;user=$this->username;password=$this->password";
    }
    // https://www.php.net/manual/en/ref.pdo-sqlite.php
    private function sqliteDsn()
    {
        return "?";
    }
    private function sqlsrvDsn()
    {
        return "sqlsrv:Server=$this->host,$this->port;Database=$this->database";
    }
}

class MySQL extends Database
{
    /**
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $database
     * @param int $port [optional]
     * @param bool $autoconnect Auto connect to database
     */
    function __construct($host, $username, $password, $database, $port = 3306, $autoconnect = true)
    {
        $this->host = $host;
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
        $this->port = $port;
        $this->driver = self::PDO_MYSQL;
        if ($autoconnect) return $this->connect();
    }

    /**
     * Execute query
     * @param string $query Query string
     * @param bool $convertType Convert column values
     * @return Response Response object
     */
    function query($query, $convertType = true) : Response
    {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            $content = array();

            if ($stmt->rowCount() > 0) {

                // Get all column types
                if ($convertType) {
                    foreach (range(0, $stmt->columnCount() - 1) as $column_index) {
                        $rowMeta = $stmt->getColumnMeta($column_index);
                        $meta[$rowMeta["name"]] = $rowMeta["native_type"];
                    }
                }

                // Get all data
                while ($row = $stmt->fetch(\PDO::FETCH_ASSOC, \PDO::FETCH_ORI_NEXT)) {
                    $rowData = array();
                    foreach ($row as $key => $val) {
                        if ($key != "disabled") {
                            // Convert to the correct type ($convertType if true)
                            if (isset($meta[$key]) && $convertType) {
                                $rowData[$key] = self::getPdoValue($val, $meta[$key]);
                            } else {
                                $rowData[$key] = $val;
                            }
                        }
                    }
                    array_push($content, $rowData);
                }

                $status = 200;
                $message = "Returned " . count($content) . " rows";
            } else {
                $status = 204;
                $message = "No rows retuned";
            }

            return new Response($status, $message, $content);
        } catch (\PDOException $err) {
            error_log($err->getMessage());
            return Error::get(500);
        }
    }

    /**
     * Convert the $value to the correct php type
     * @param string $type
     */
    private static function getPdoValue($value, $type)
    {
        switch ($type) {
            case "LONG":
                $returnVal = (int)$value;
                break;
            case "LONGLONG":
                $returnVal = (int)$value;
                break;
            case "TINY":
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
