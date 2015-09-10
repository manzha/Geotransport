<?php
require_once "dbconfig.php";
if (isset($_POST['deviceID'])) {
//    $timezone = "America/Monterrey";
//    date_default_timezone_set($timezone);
    //SUR = 1
    // NORTE =2
    $id = $_POST['deviceID'];
    $ruta = $_POST['ruta'];
    $direccion = $_POST['direccion'];
    $parada = $_POST['parada'];
    echo $ruta;
    $result= mysql_query("SELECT id FROM rutas WHERE nombre='".$ruta."'");
    $row = mysql_fetch_array($result);
    $ruta = $row['id'];
    $result= mysql_query("SELECT id FROM paradas WHERE ruta=$ruta AND nombre='".$parada."'");
    $row = mysql_fetch_array($result);
    $parada = $row['id'];
    $longreg = strlen($id) * strlen($ruta) * strlen($direccion) * strlen($parada);
    if ($longreg > 0) {
//        $date = new DateTime();
//        $date->modify("+" . $tiempo . " minutes");
//        $tiempo = $date->format('Y-m-d H:i:s');
        $result= mysql_query("INSERT INTO notificacion (idMovil, ruta, parada,direccion) 
        VALUES ('" . $id . "'," . $ruta . "," . $parada . ",'" . $direccion . "')");
        if($result){
            echo "Correcto";
        }
//        mysql_close($link);
    } else {
        echo "Error";
    }
}else{
    echo "ERRROOOOOR!!";
}
?>