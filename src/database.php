<?php

namespace simo026q\Database;

use simo026q\Response\Response;

/**
 * @author simo026q
 * @copyright Copyright (c) 2021 simo026q
 * @license MIT License
 */
class Database
{
    private $host, $database, $username, $password, $connection, $port;

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
        if ($autoconnect) return $this->connect();
    }

    /**
     * Connect to database
     */
    function connect()
    {
        try {
            $this->connection = new \PDO($this->dsn(), $this->username, $this->password);
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
    function isConnected() {
        return ($this->connection != null);
    }

    /**
     * Execute query
     * @param string $query Query string
     * @return Response Response object
     */
    function query($query)
    {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            $content = array();

            if ($stmt->rowCount() > 0) {
                // Get all column types
                foreach (range(0, $stmt->columnCount() - 1) as $column_index) {
                    $rowMeta = $stmt->getColumnMeta($column_index);
                    $meta[$rowMeta["name"]] = $rowMeta["native_type"];
                }

                // Get all data
                while ($row = $stmt->fetch(\PDO::FETCH_ASSOC, \PDO::FETCH_ORI_NEXT)) {
                    $rowData = array();
                    foreach ($row as $key => $val) {
                        if ($key != "disabled") {
                            // Convert to the correct type
                            if (isset($meta[$key])) {
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
            return new Response(500);
        }
    }

    /**
     * @return string DSN String
     */
    private function dsn()
    {
        return "mysql:host=$this->host;dbname=$this->database";
    }

    /**
     * Convert the $value to the correct php type
     * @param string $type
     */
    private static function getPdoValue($value, $type) {
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