<?php

include "dbconfig.php";

$query = "SELECT * FROM paradas";
$result = mysql_query($query);

while($row = mysql_fetch_array($result)){
    echo htmlspecialchars('<item>'.$row['nombre'].'</item>')."<br>";
}