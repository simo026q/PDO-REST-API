<?php

namespace simo026q\Response;

use Exception;
use Throwable;

/**
 * @author simo026q
 * @copyright Copyright (c) 2021 simo026q
 * @license MIT License
 */
class Response extends Exception
{
    protected $content;

    /**
     * @param array $content JSON Array
     * @param bool $setHttpCode Auto set http code from $code
     */
    public function __construct(string $message = "", int $code = 200, array $content = null, bool $setHttpCode = true, ?Throwable $previous = null) {
        $this->message = $message;
        $this->code = $code;
        $this->content = $content;
        $this->previous = $previous;

        if ($setHttpCode) http_response_code($this->code);
    }

    /**
     * @return array Get content
     */
    final public function getContent(): array {
        return $this->content;
    }

    /**
     * @return array Raw response
     */
    public function raw(): array
    {
        return [
            "content" => $this->content,
            "message" => $this->message,
            "status" => $this->code
        ];
    }

    /**
     * @return string JSON response
     */
    public function json(): string
    {
        return json_encode($this->raw());
    }

    /**
     * Get a error message by status code
     * @param int $code Status code
     * @return string Error message
     */
    public static function getDefaultMessage($code): string
    {
        switch ($code) {
            case 100:
                $message = "Continue";
                break;
            case 101:
                $message = "Switching Protocols";
                break;
            case 200:
                $message = "OK";
                break;
            case 201:
                $message = "Created";
                break;
            case 202:
                $message = "Accepted";
                break;
            case 203:
                $message = "Non-Authoritative Information";
                break;
            case 204:
                $message = "No Content";
                break;
            case 205:
                $message = "Reset Content";
                break;
            case 206:
                $message = "Partial Content";
                break;
            case 300:
                $message = "Multiple Choices";
                break;
            case 301:
                $message = "Moved Permanently";
                break;
            case 302:
                $message = "Moved Temporarily";
                break;
            case 303:
                $message = "See Other";
                break;
            case 304:
                $message = "Not Modified";
                break;
            case 305:
                $message = "Use Proxy";
                break;
            case 400:
                $message = "Bad Request";
                break;
            case 401:
                $message = "Unauthorized";
                break;
            case 402:
                $message = "Payment Required";
                break;
            case 403:
                $message = "Forbidden";
                break;
            case 404:
                $message = "Not Found";
                break;
            case 405:
                $message = "Method Not Allowed";
                break;
            case 406:
                $message = "Not Acceptable";
                break;
            case 407:
                $message = "Proxy Authentication Required";
                break;
            case 408:
                $message = "Request Time-out";
                break;
            case 409:
                $message = "Conflict";
                break;
            case 410:
                $message = "Gone";
                break;
            case 411:
                $message = "Length Required";
                break;
            case 412:
                $message = "Precondition Failed";
                break;
            case 413:
                $message = "Request Entity Too Large";
                break;
            case 414:
                $message = "Request-URI Too Large";
                break;
            case 415:
                $message = "Unsupported Media Type";
                break;
            case 500:
                $message = "Internal Server Error";
                break;
            case 501:
                $message = "Not Implemented";
                break;
            case 502:
                $message = "Bad Gateway";
                break;
            case 503:
                $message = "Service Unavailable";
                break;
            case 504:
                $message = "Gateway Time-out";
                break;
            case 505:
                $message = "HTTP Version not supported";
                break;
            default:
                $message = "";
                break;
        }
        return $message;
    }
}

class Error extends Response
{
    /**
     * Return en error
     * @param int $code
     * @param string $message
     */
    static function get($code, $message = ""): Response
    {
        if (empty($message)) $message = self::getDefaultMessage($code);
        return new Response($code, $message);
    }

    /**
     * Throw error (echo JSON response)
     * @param int $code
     * @param string $message
     */
    static function throw($code, $message = ""): void
    {
        $err = self::get($code, $message);
        echo $err->json();
    }
}
