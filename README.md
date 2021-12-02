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

### Response
A JSON response looks like this:
```
{
  "content": [ ... ], // 4 items
  "message": "Returned 4 rows",
  "status": 200
}
```
