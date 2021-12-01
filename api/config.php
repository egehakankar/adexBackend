<?php
header('Access-Control-Allow-Origin: *');
$db = new mysqli("localhost", "root", "", "adexdatabase");
if (!$db) die("database connection error");
?>

<?php
header('Access-Control-Allow-Origin: *');
$mysqli = new mysqli("localhost", "root", "", "adexdatabase") or die("database connection error");
?>

<?php
header('Access-Control-Allow-Origin: *');
/* Database credentials. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'adexdatabase');
 
/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>