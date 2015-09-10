<?php

//Archivo que genera un archivo json para mostrar los autobuses en el MAPA
require("dbconfig.php");

// Select all the rows in the markers table

$query = "SELECT * FROM autobus WHERE activo=1";
$result = mysql_query($query);
if (!$result) {
    die('Invalid query: ' . mysql_error());
}

// Iterate through the rows, adding XML nodes for each
$buses = array();
while ($row = @mysql_fetch_assoc($result)) {
    array_push($buses, $row);
}

echo json_encode($buses);
?>