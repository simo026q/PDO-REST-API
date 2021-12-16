<?php 

namespace simo026q\Api;

use Exception;
use simo026q\Database\Database;
use simo026q\Response\Response;
use simo026q\Response\Error;

/**
 * @author simo026q
 * @copyright Copyright (c) 2021 simo026q
 * @license MIT License
 */
abstract class API {
    private $params, $rqdParams;
    protected $database;

    abstract function __construct();

    function addParam($name, $required) {
        if ($required) {
            array_push($rqdParams, $name);
        }
        else {
            array_push($params, $name);
        }
    }

    function getParam($name) : string {
        if ($this->paramExist($name)) {
            return (isset($_GET[$name])) ? $_GET[$name] : "";
        }
        else {
            throw new Exception("Parameter does not exist");
        }
    }

    function paramExist($name) : bool {
        return in_array($name, $this->params) || in_array($name, $this->rqdParams);
    }
}