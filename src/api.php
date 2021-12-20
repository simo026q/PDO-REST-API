<?php 

namespace simo026q\Api;

use Exception;

// TESTING
// DO NOT USE

$params = [
    "optional" => [],
    "required" => ["table"]
];

/**
 * @author simo026q
 * @copyright Copyright (c) 2021 simo026q
 * @license MIT License
 */
abstract class API {
    private $params;

    abstract function __construct();

    protected function validateParams()
    {
        if (isset($this->params["required"])) {
            
        }

        if (isset($this->params["optional"])) return self::validateParam($this->params["optional"]);
        return false;
    }

    private static function validateParam($param): bool
    {
        return true;
    }
}