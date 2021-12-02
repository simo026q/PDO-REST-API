<?php

require_once("../src/response.php");
require_once("../src/database.php");

use simo026q\Response\Error;
use simo026q\Database\Database;

header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');

// Database login
$GLOBALS["database"] = array(
    "host" => "localhost",
    "user" => "root",
    "pass" => "",
    "name" => "Nordicracing"
);

// Tables allowed to be shown
static $publicTables = array("event", "teams", "results");

// If the table parameter is set (e.g. getTable?table=customers)
if (isset($_GET['table'])) {
    // If the table is whitelisted
    if (in_array($_GET['table'], $publicTables)) {

        // Create a new database object
        $database = new Database(
            $GLOBALS["database"]["host"], 
            $GLOBALS["database"]["user"], 
            $GLOBALS["database"]["pass"], 
            $GLOBALS["database"]["name"],
        );

        // Execute database query
        $response = $database->query("SELECT * FROM $_GET[table] WHERE disabled=0");

        // Close the connect after use
        $database->disconnect();

        // Echo the json response
        echo $response->json();

    } else {
        // Throw a 404 error
        Error::e404("Table does not exist!");
    }
} else {
    // Throw a 400 error
    Error::e400("No table specified!");
}
