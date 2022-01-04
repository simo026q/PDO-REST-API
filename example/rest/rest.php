<?php 

require_once("../../src/response.php");
require_once("../../src/api.php");
require_once("../../src/database/database.php");
require_once("../../src/database/drivers/mysql.php");

use simo026q\Api\API;
use simo026q\Api\UrlParameters;
use simo026q\Response\Response;
use simo026q\Database\Drivers\MySQL;

// JSON Content type
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Origin: *");

// Database login
$hostname = "localhost";
$username = "root";
$password = "";
$dbname   = "nordicracing";

// Create a new database object
$database = new MySQL(
    $hostname, 
    $username, 
    $password, 
    $dbname
);

$table = UrlParameters::getTable();
$filter = UrlParameters::getFilter();
$primary = $database->getPrimaryKey($table);

$whereStr = (!empty($primary) && !empty($filter)) ? " WHERE $primary=$filter" : "";

try {
    $api = new API(UrlParameters::getTable(), $database);

    $rsp = $api->get("SELECT * FROM $table$whereStr");
    
    echo $rsp->json();
}
catch (PDOException $err) {
    echo $err->getMessage();
}