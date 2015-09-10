<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'password');
define('DB_DATABASE', 'databasename');
$connection = mysql_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD) or die(mysql_error());
$database = mysql_select_db(DB_DATABASE) or die(mysql_error());
$setnames = "SET NAMES 'utf8'";
mysql_query($setnames, $connection);

define("GOOGLE_API_KEY", "YOUR_APIKEY"); // Place your Google API Key
?>