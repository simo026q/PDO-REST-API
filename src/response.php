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
    function raw()
    {
        return array("content" => $this->content, "message" => $this->message, "status" => $this->status);
    }

    /**
     * @return string JSON response
     */
    function json()
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
    static function get($status, $message = "") {
        if (empty($message)) $message = self::getMessage($status);
        return new Response($status, $message);
    }
    
    /**
     * Throw error (echo JSON response)
     * @param int $status
     * @param string $message
     */
    static function throw($status, $message = "") {
        $err = self::get($status, $message);
        echo $err->json();
    }

    /**
     * Throw a 404 error
     * @param string $message Custom error message
     */
    static function e404($message = "") {
        self::throw(404, $message);
    }
    
    /**
     * Throw a 400 error
     * @param string $message Custom error message
     */
    static function e400($message = "") {
        self::throw(400, $message);
    }

    /**
     * Get a error message by status code
     * @param int $status Status code
     * @return string Error message
     */
    static function getMessage($status) {
        switch ($status) {
            case 100: $text = 'Continue'; break;
            case 101: $text = 'Switching Protocols'; break;
            case 200: $text = 'OK'; break;
            case 201: $text = 'Created'; break;
            case 202: $text = 'Accepted'; break;
            case 203: $text = 'Non-Authoritative Information'; break;
            case 204: $text = 'No Content'; break;
            case 205: $text = 'Reset Content'; break;
            case 206: $text = 'Partial Content'; break;
            case 300: $text = 'Multiple Choices'; break;
            case 301: $text = 'Moved Permanently'; break;
            case 302: $text = 'Moved Temporarily'; break;
            case 303: $text = 'See Other'; break;
            case 304: $text = 'Not Modified'; break;
            case 305: $text = 'Use Proxy'; break;
            case 400: $text = 'Bad Request'; break;
            case 401: $text = 'Unauthorized'; break;
            case 402: $text = 'Payment Required'; break;
            case 403: $text = 'Forbidden'; break;
            case 404: $text = 'Not Found'; break;
            case 405: $text = 'Method Not Allowed'; break;
            case 406: $text = 'Not Acceptable'; break;
            case 407: $text = 'Proxy Authentication Required'; break;
            case 408: $text = 'Request Time-out'; break;
            case 409: $text = 'Conflict'; break;
            case 410: $text = 'Gone'; break;
            case 411: $text = 'Length Required'; break;
            case 412: $text = 'Precondition Failed'; break;
            case 413: $text = 'Request Entity Too Large'; break;
            case 414: $text = 'Request-URI Too Large'; break;
            case 415: $text = 'Unsupported Media Type'; break;
            case 500: $text = 'Internal Server Error'; break;
            case 501: $text = 'Not Implemented'; break;
            case 502: $text = 'Bad Gateway'; break;
            case 503: $text = 'Service Unavailable'; break;
            case 504: $text = 'Gateway Time-out'; break;
            case 505: $text = 'HTTP Version not supported'; break;
            default:
                $text = '';
                break;
        }
        return $text;
    }
}