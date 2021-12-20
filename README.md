# Docs
Note: We currently ONLY support MySQL.

## Step 1
Download the repository and add it to your project

## Step 2
### Include
Start by including the files.
```php
require_once("./api/response.php");
require_once("./api/database/database.php");
require_once("./api/database/drivers/mysql.php");
```

### Use
#### Required
```php
use simo026q\Database\Drivers\MySQL;
```

#### Optional
```php
use simo026q\Response\Error;
use simo026q\Response\Response;
```

### Header
You should use these headers.
```php
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
```

## Step 3
### Getting content
```php
// XAMPP Default
$hostname = "localhost";
$username = "root";
$password = "";

// Change to your database name
$dbname   = "webshop";

// Select table
$table    = "customers";

// Create a new database object
$database = new MySQL($host, $user, $password, $dbname);

// Execute database query
$response = $database->query("SELECT * FROM $table");

// Close the connect after use
$database->disconnect();

// Echo the json response
echo $response->json();
```

## Response
### Constructor
#### No parameter
```php
// content: null
// message: 
// status: 200
$rsp = new Response();
```

#### Status parameter
```php
// content: null
// message: 
// status: 404
$rsp = new Response(404);
```

#### Status parameter
```php
// content: null
// message: Table does not exist!
// status: 404
$rsp = new Response(404, "Table does not exist!");
```

#### Content parameter
```php
// content: [ ... ]
// message: OK
// status: 200
$rsp = new Response(200, "OK", array( ... ));
```

### Functions
#### Raw
##### Usage
```php
$rsp = new Response(200, "Returned 4 rows", array( ... ));

var_dump($rsp->raw());
```
##### Result
```
array(3) { 
  ["content"] => array(4) { ... }
  ["message"] => string(15) "Returned 4 rows" 
  ["status"] => int(200) 
}
```
#### JSON
##### Usage
```php
header('Content-Type: application/json; charset=UTF-8');

$rsp = new Response(200, "Returned 4 rows", array( ... ));

echo $rsp->json();
```
##### Result
```json
{
  "content": [ ... ], // 4 items
  "message": "Returned 4 rows",
  "status": 200
}
```

## Error
### Functions
#### Throw
```php
Error::throw(404, "Some error");
```

#### Get message
```php
$messsage = Error::getMessage(404); // Return "Not found"
```