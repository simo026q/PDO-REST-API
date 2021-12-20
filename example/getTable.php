<?php

require_once("../src/response.php");
require_once("../src/database/database.php");
require_once("../src/database/drivers/mysql.php");

use simo026q\Response\Error;
use simo026q\Database\Drivers\MySQL;

header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');

// Database login
$hostname = "localhost";
$username = "root";
$password = "";
$database = "nordicracing";

// Tables allowed to be shown
static $publicTables = array("event", "teams", "results");

// If the table parameter is set (e.g. getTable?table=customers)
if (isset($_GET['table'])) {
    // If the table is whitelisted
    if (in_array($_GET['table'], $publicTables)) {

        // Create a new database object
        $database = new MySQL(
            $hostname, 
            $username, 
            $password, 
            $database
        );

        // Execute database query
        $response = $database->query("SELECT * FROM $_GET[table] WHERE disabled=0");

        // Close the connect after use
        $database->disconnect();

        // Echo the json response
        echo $response->json();

    } else {
        // Throw a 404 error
        Error::throw(404, "Table does not exist!");
    }
} else {
    // Throw a 400 error
    Error::throw(400, "No table specified!");
}
