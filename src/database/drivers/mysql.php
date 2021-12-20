<?php

namespace simo026q\Database\Drivers;

use simo026q\Database\Database;
use simo026q\Response\Response;
use simo026q\Response\Error;

/**
 * @author simo026q
 * @copyright Copyright (c) 2021 simo026q
 * @license MIT License
 */
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
    function query($query, $convertType = true): Response
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
     * @return string DSN String
     */
    protected function dsn(): string
    {
        return "mysql:host=$this->host;port=$this->port;dbname=$this->database";
    }

    /**
     * Convert the $value to the correct php type
     * @param string $type
     */
    private static function getPdoValue($value, $type): string
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