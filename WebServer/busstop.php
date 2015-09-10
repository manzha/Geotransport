<?php

//Archivo que calcula la parada mas cercana a cada autobus
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;
require("dbconfig.php");
$query = sprintf("SELECT * FROM autobus WHERE activo=1");
$result = mysql_query($query);

if (!$result) {
    die('Invalid query: ' . mysql_error());
}
while ($row = @mysql_fetch_assoc($result)) {
    $distanciaMin = PHP_INT_MAX;
    $parada = -1;
//Si la parada actual es -1, se calcula la distancia contra todas las paradas, y se obtiene la parada con distancia minima.
    if ($row['parada'] == -1) {
        $queryParada = sprintf("SELECT * FROM paradas WHERE id IN (SELECT parada FROM ruta_parada WHERE ruta = 1)");
        $resultParada = mysql_query($queryParada);
        while ($rowParada = @mysql_fetch_array($resultParada)) {
//for ($i = 0; $i < count($stopNombres); $i++) {
//            echo $x . "\t(" . $x_value[0] . ", " . $x_value[1] . ")";
//            echo "<br>";
//$distancia = pow(($row['latitud'] - $stopCoord[$i][0]), 2) + pow(($row['longitud'] - $stopCoord[$i][1]), 2);
            $distancia = distanciaGeodesica($row['latitud'], $row['longitud'], $rowParada['lat'], $rowParada['lng']);
            if ($distanciaMin > $distancia) {
                $distanciaMin = $distancia; //Distancia minima
                $parada = $rowParada['id']; //Parada con distancia minima
            }
        }
    } else {
//Si ya tiene una parada, se verifica solo con las paradas que estan por delante de la actual, dependiendo la direccion.
//Si tiene paradaUltima, se verifica solo con la parada proxima... (ya se sabe el sentido)
//Si no, se busca con las adyacentes
//        if ($row['paradaUltima'] == -1) {
        $queryParada = "SELECT * FROM paradas WHERE id IN (SELECT parada FROM ruta_parada WHERE ruta = 1) AND id>=" . ($row['parada'] - 1) . " AND id<=" . ($row['parada'] + 1);
//        } else {
//            if ($row['paradaUltima'] > $row['parada']) {
//                
//            } else {
//                if ($row['paradaUltima'] <= $row['parada']) {
//                    
//                }
//            }
        $resultParada = mysql_query($queryParada);
        while ($rowParada = @mysql_fetch_array($resultParada)) {
            $distancia = distanciaGeodesica($row['latitud'], $row['longitud'], $rowParada['lat'], $rowParada['lng']);
            if ($distanciaMin > $distancia) {
                $distanciaMin = $distancia;
                $parada = $rowParada['id'];
            }
        }
    }
    //DIFERENTES PARADAS
    if (!($parada == $row['parada'])) {
//        $direccion = "";
//        if ($parada > $row['parada']) { //Si la parada avanzo es que va direccion Sur (La parada mas al Norte es la 1, y al sur es la 30)
//            $direccion = "Sur";
//        } else {
//            $direccion = "Norte";
//        }
//Si la parada es diferente a la anterior, entonces probablemente esta Antes de la parada.
        $statusParada = "Antes de";
        $query2 = 'UPDATE autobus SET paradaUltima=' . $row['parada'] . ' WHERE id="' . $row['id'] . '"';
        $result2 = mysql_query($query2);
    } else {
//Si la parada es la misma, pero la distancia es mayo a la anterior, entonces se encuentra despues de la parada.
        if ($distanciaMin > $row['distancia']) {
            $statusParada = "Despues de";
        }
    }
//Si la distancia esta a menos de 10 metros, entonces esta "En" la parada
    if ($distanciaMin * 1000 < 10) {
        $statusParada = "En";
    }
    $query2 = 'UPDATE autobus SET parada=' . $parada . ', statusParada="' . $statusParada . '" WHERE id="' . $row['id'] . '"';
    $result2 = mysql_query($query2);
}

$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$finish = $time;
$total_time = round(($finish - $start), 4);
echo 'Terminado en ' . $total_time . ' segundos.';

//Funcion para calcular la distancia en kilometros
function distanciaGeodesica($lat1, $long1, $lat2, $long2) {

    $degtorad = 0.01745329;
    $radtodeg = 57.29577951;

    $dlong = ($long1 - $long2);
    $dvalue = (sin($lat1 * $degtorad) * sin($lat2 * $degtorad)) + (cos($lat1 * $degtorad) * cos($lat2 * $degtorad) * cos($dlong * $degtorad));

    $dd = acos($dvalue) * $radtodeg;

    $km = ($dd * 111.302);

    return $km;
}

?>