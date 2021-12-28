<?php

namespace simo026q\Response;

/**
 * @author simo026q
 * @copyright Copyright (c) 2021 simo026q
 * @license MIT License
 */
class Response
{
    protected $content;
    protected string $message;
    protected int $status;

    /**
     * @param array $content JSON Array
     * @param bool $setHttpCode Auto set http code from $status
     */
    function __construct($status = 200, $message = "", $content = null, $setHttpCode = true)
    {
        $this->content = $content;
        $this->message = $message;
        $this->status = $status;

        if ($setHttpCode) http_response_code($this->status);
    }

    /**
     * @return array Raw response
     */
    function raw(): array
    {
        return [
            "content" => $this->content, 
            "message" => $this->message, 
            "status" => $this->status
        ];
    }

    /**
     * @return string JSON response
     */
    function json(): string
    {
        $json = json_encode($this->raw());
        return $json;
    }
}

class Error extends Response {
    /**
     * Return en error
     * @param int $status
     * @param string $message
     */
    static function get($status, $message = ""): Response
    {
        if (empty($message)) $message = self::getMessage($status);
        return new Response($status, $message);
    }
    
    /**
     * Throw error (echo JSON response)
     * @param int $status
     * @param string $message
     */
    static function throw($status, $message = ""): void 
    {
        $err = self::get($status, $message);
        echo $err->json();
    }

    /**
     * Get a error message by status code
     * @param int $status Status code
     * @return string Error message
     */
    static function getMessage($status): string
    {
        switch ($status) {
            case 100: $message = "Continue"; break;
            case 101: $message = "Switching Protocols"; break;
            case 200: $message = "OK"; break;
            case 201: $message = "Created"; break;
            case 202: $message = "Accepted"; break;
            case 203: $message = "Non-Authoritative Information"; break;
            case 204: $message = "No Content"; break;
            case 205: $message = "Reset Content"; break;
            case 206: $message = "Partial Content"; break;
            case 300: $message = "Multiple Choices"; break;
            case 301: $message = "Moved Permanently"; break;
            case 302: $message = "Moved Temporarily"; break;
            case 303: $message = "See Other"; break;
            case 304: $message = "Not Modified"; break;
            case 305: $message = "Use Proxy"; break;
            case 400: $message = "Bad Request"; break;
            case 401: $message = "Unauthorized"; break;
            case 402: $message = "Payment Required"; break;
            case 403: $message = "Forbidden"; break;
            case 404: $message = "Not Found"; break;
            case 405: $message = "Method Not Allowed"; break;
            case 406: $message = "Not Acceptable"; break;
            case 407: $message = "Proxy Authentication Required"; break;
            case 408: $message = "Request Time-out"; break;
            case 409: $message = "Conflict"; break;
            case 410: $message = "Gone"; break;
            case 411: $message = "Length Required"; break;
            case 412: $message = "Precondition Failed"; break;
            case 413: $message = "Request Entity Too Large"; break;
            case 414: $message = "Request-URI Too Large"; break;
            case 415: $message = "Unsupported Media Type"; break;
            case 500: $message = "Internal Server Error"; break;
            case 501: $message = "Not Implemented"; break;
            case 502: $message = "Bad Gateway"; break;
            case 503: $message = "Service Unavailable"; break;
            case 504: $message = "Gateway Time-out"; break;
            case 505: $message = "HTTP Version not supported"; break;
            default:
                $message = "";
                break;
        }
        return $message;
    }
}