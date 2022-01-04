<?php

namespace simo026q\Api;

use Exception;
use PDOException;
use simo026q\Database\Drivers\MySQL;
use simo026q\Response\Response;

/**
 * @author simo026q
 * @copyright Copyright (c) 2021 simo026q
 * @license MIT License
 */
class API
{
    protected MySQL $database;
    protected string $table;

    // https://restfulapi.net/http-methods/
    public const REQUEST_GET = "GET",
        REQUEST_POST = "POST",
        REQUEST_PUT = "PUT",
        REQUEST_DELETE = "DELETE",
        REQUEST_OTHER = "?";

    function __construct(string $table, MySQL $database)
    {
        $this->table = $table;
        $this->database = $database;
    }

    // HTTP GET http://www.appdomain.com/users
    // HTTP GET http://www.appdomain.com/users?size=20&page=5
    // HTTP GET http://www.appdomain.com/users/123
    // HTTP GET http://www.appdomain.com/users/123/address
    // SELECT
    public function get($query): Response
    {
        if (strpos(strtoupper($query), "SELECT") === false)
            return new Response("Could not GET because the query did not contain a SELECT statement", 500);

        if (self::getRequestMethod() != self::REQUEST_GET)
            return new Response("Could not send GET request because the request was a " . $_SERVER["REQUEST_METHOD"], 400);

        try {
            $content = $this->database->fetch($query);

            if (count($content) > 0) {
                $code = 200;
                $message = "Returned " . count($content) . " rows";
            } else {
                $code = 204;
                $message = "No rows retuned";
            }

            return new Response($message, $code, $content);
        } catch (PDOException $err) {
            return new Response($err->getMessage(), 400);
        }
    }

    // INSERT
    protected function post($query)
    {
        if (strpos(strtoupper($query), "INSERT") === false)
            return new Response("Could not POST because the query did not contain a INSERT statement", 500);

        if (self::getRequestMethod() != self::REQUEST_POST)
            return new Response("Could not send POST request because the request was a " . self::getRequestMethod(), 400);
    }

    // UPDATE
    protected function put($query)
    {
        if (strpos(strtoupper($query), "UPDATE") === false)
            return new Response("Could not PUT because the query did not contain a UPDATE statement", 500);

        if (self::getRequestMethod() != self::REQUEST_PUT)
            return new Response("Could not send PUT request because the request was a " . self::getRequestMethod(), 400);
    }

    // DELETE
    protected function delete($query)
    {
        if (strpos(strtoupper($query), "DELETE") === false)
            return new Response("Could not DELETE because the query did not contain a DELETE statement", 500);

        if (self::getRequestMethod() != self::REQUEST_DELETE)
            return new Response("Could not send DELETE request because the request was a " . self::getRequestMethod(), 400);
    }

    public static function getRequestMethod()
    {
        switch ($_SERVER["REQUEST_METHOD"]) {
            case "GET":
                $request = self::REQUEST_GET;
                break;
            case "POST":
                $request = self::REQUEST_POST;
                break;
            case "PUT":
                $request = self::REQUEST_PUT;
                break;
            case "DELETE":
                $request = self::REQUEST_DELETE;
                break;
            default:
                $request = self::REQUEST_OTHER;
                break;
        }
        return $request;
    }
}

class UrlParameters {
    private static $parameters;
    private static $table;
    private static $filter;

    private static function getParameters() 
    {
        if (isset(self::$parameters)) return self::$parameters;

        $_parameters = explode('/', strtok(getenv('PATH_INFO'), '?'));
        $parameters = array_splice($_parameters, 1);

        self::$parameters = $parameters;
        return $parameters;
    }

    public static function getTable() {
        if (isset(self::$table)) return self::$table;
        $params = self::getParameters();
        $table = $params[0];

        if (isset($table) && !empty($table)) self::$table = $table;
        return $table;
    }

    public static function getFilter() {
        if (isset(self::$filter)) return self::$filter;
        $params = self::getParameters();
        $filter = $params[1];

        if (isset($filter) && !empty($filter)) self::$filter = $filter;
        return $filter;
    }
}