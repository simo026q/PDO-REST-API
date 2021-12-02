# PDO REST API
Open PDO REST API

# Docs

## Step 1
Download the repository and add it to your project

## Step 2
### Include
Start by including the files.
```
require_once("./src/response.php");
require_once("./src/database.php");
```

### Use
#### Required
```
use simo026q\Database\Database;
```

#### Optional
```
use simo026q\Response\Error;
use simo026q\Response\Response;
```

### Header
You should use these headers.
```
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
```

## Step 3
### Getting content
```
// XAMPP Default
$host = "localhost";
$user = "root";
$password = "";
// Change to your database name
$database = "webshop";

// Select table
$table = "customers"

// Create a new database object
$database = new Database($host, $user, $password, $database);

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
```
// content: null
// message: 
// status: 200
$rsp = new Response();
```

#### Status parameter
```
// content: null
// message: 
// status: 404
$rsp = new Response(404);
```

#### Status parameter
```
// content: null
// message: Table does not exist!
// status: 404
$rsp = new Response(404, "Table does not exist!");
```

#### Content parameter
```
// content: [ ... ]
// message: OK
// status: 200
$rsp = new Response(200, "OK", array( ... ));
```

### Functions
#### Raw
##### Usage
```
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
```
header('Content-Type: application/json; charset=UTF-8');

$rsp = new Response(200, "Returned 4 rows", array( ... ));

echo $rsp->json();
```
##### Result
```
{
  "content": [ ... ], // 4 items
  "message": "Returned 4 rows",
  "status": 200
}
```

## Error
### Functions
#### Throw
```
Error::throw(404, "Some error");
```

#### Throw code (e.g. e404())
```
Error::e404("Custom message");
Error::e400();
```

#### Get message
```
$messsage = Error::getMessage(404); // Return "Not found"
```
