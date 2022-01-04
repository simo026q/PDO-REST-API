<?php

namespace simo026q\Database\Drivers;

use PDO;
use PDOException;
use simo026q\Database\Database;
//use simo026q\Response\Response;
//use simo026q\Response\Error;

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
    /*final function queryOld($query): Response
    {
        try {
            $convertType = true;
            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            $content = [];

            // If any rows is returned
            if ($stmt->rowCount() > 0) {

                // Get all column types
                if ($convertType) {
                    foreach (range(0, $stmt->columnCount() - 1) as $column_index) {
                        $columnMeta = $stmt->getColumnMeta($column_index);
                        $meta[$columnMeta["name"]] = $columnMeta["native_type"];
                    }
                }

                $rsp = new Response(0, "", $meta);
                echo $rsp->json();

                // Get all data
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
                    $rowData = array();
                    foreach ($row as $key => $val) {
                        // Convert to the correct type ($convertType if true)
                        if (isset($meta[$key]) && $convertType) {
                            $rowData[$key] = self::convertDatatype($val, $meta[$key]);
                        } else {
                            $rowData[$key] = $val;
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
        } catch (PDOException $err) {
            error_log($err->getMessage());
            return Error::get(500);
        }
    }*/

    /**
     * @return string DSN String
     */
    final protected function dsn(): string
    {
        return "mysql:host=$this->host;port=$this->port;dbname=$this->database";
    }
}
