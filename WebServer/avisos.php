<?php

//Archivo que se tiene que ejecutar cada X tiempo para mandar avisos a los usuarios registrados
//Se conecta a la BD
require_once "dbconfig.php";
//Se establece la zona horaria
$timezone = "America/Monterrey";
date_default_timezone_set($timezone);
//Se busca entre todos los usuarios registrados
$query = "SELECT * FROM notificacion";
$result = mysql_query($query);
while ($row = mysql_fetch_array($result)) {
    $parada = $row['parada'];
    $direccion = $row['direccion'];
    $totalParadasQuery = "SELECT * FROM ruta_parada WHERE ruta=1";
    $resultTotalParadas = mysql_query($totalParadasQuery);
    $totalParadas= mysql_num_rows($resultTotalParadas); //Total de paradas...
    
    if($direccion==1){ //SUR
        $resta=2; //Que venga dos paradas más al norte
        $parada+=$resta;
        if($parada>$totalParadas){
            $parada = $totalParadas - ($parada%$totalParadas);
        }
        $query2 = 'SELECT * FROM autobus WHERE activo=1 AND (parada=' . $paradaRevisada . ') AND parada<ultimaParada';
    }else{//NORTE
        $resta=-2; //Que venga 2 paradas más al sur..
        $parada+=$resta;
        if($parada<1){
            $parada = 2- $parada;
        }
        $query2 = 'SELECT * FROM autobus WHERE activo=1 AND (parada=' . $paradaRevisada . ') AND parada>ultimaParada';
    }
    //Selecciona los autobuses activos donde su parada mas cercana o su parada anterior es 4 paradas antes. Ademas la direccion es la correcta.
    
    $result2 = mysql_query($query2);
    //Si hay mas de un resultado
    if (mysql_num_rows($result2) != 0) {
        //Se recorren lso camiones y en caso de no haber enviado un email anteriormente se envia el email
        while ($row2 = mysql_fetch_array($result2)) {
            $query3 = 'SELECT * FROM avisos WHERE idUsuario=' . $row ['id'] . ' AND  idAutobus="' . $row2['id'] . '"';
            $result3 = mysql_query($query3);
            //Si no se ha enviado el aviso antes...
            if (mysql_num_rows($result3) == 0) {
                $cantAvisos = $row['avisos'];
                //Si ya expiro el registro, se borra
                if ($cantAvisos == 0) {
                    $queryRegistro = sprintf('DELETE FROM registro WHERE id =' . $row['id']);
                    $resultRegistro = mysql_query($queryRegistro);
                    $queryAviso = sprintf('DELETE FROM avisos WHERE idUsuario =' . $row['id']);
                    $resultAviso = mysql_query($queryAviso);
                } else {
                    //Si no, se decrementa la cantidad de avisos
                    $cantAvisos = $cantAvisos - 1;
                    if ($row['avisos'] > 0) {
                        $queryRegistro = sprintf('UPDATE registro SET avisos=' . ( $cantAvisos) . ' WHERE id=' . $row['id']);
                        $resultRegistro = mysql_query($queryRegistro);
                    }
                    //Se escribe que ya se envio aviso para ESE bus
                    $queryAviso = sprintf('INSERT INTO avisos (idUsuario,idAutobus) VALUES ("' . $row ['id'] . '","' . $row2['id'] . '")');
                    $resultAviso = mysql_query($queryAviso);
                    //SE ENVIA EL AVISO
                    //ENVIAR EMAIL!!!!
                    $queryParada = sprintf("SELECT * FROM paradas WHERE id=" . $row2['parada']);
                    $resultParada = mysql_query($queryParada);
                    $rowParada = mysql_fetch_array($resultParada);
                    $paradaActual = $rowParada['nombre'];
                    $queryParada = sprintf("SELECT * FROM paradas WHERE id=" . $row['parada']);
                    $resultParada = mysql_query($queryParada);
                    $rowParada = mysql_fetch_array($resultParada);
                    $paradaSolicitada = $rowParada['nombre'];

                    //SEND NOTIFICACION
                    $url = 'https://android.googleapis.com/gcm/send';


                    // Message to be sent
                    $message = "Su camion esta por llegar...";
                    echo $message;

                    $registrationid = $row['idMovil'];
                    $fields = array(
                        'registration_ids' => array($registrationid),
                        'data' => array("message" => $message),
                    );

                    $headers = array(
                        'Authorization: key=' . GOOGLE_API_KEY,
                        'Content-Type: application/json'
                    );

// Open connection
                    $ch = curl_init();

// Set the url, number of POST vars, POST data
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
//curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
// Execute post
                    $result = curl_exec($ch);
                    if ($result) {
                        echo "sI";
                    } else {
                        echo "no";
                    }

// Close connection
                    curl_close($ch);

                    echo $result;
                }
            }
        }
    }
}
?>