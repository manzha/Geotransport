<?php
//Archivo que recibe datos del celular
//Se conecta al a BD
require('dbconfig.php');
if (isset($_POST['id'])) {
    //Recibe los datos ID,latitud,longitud y activo.
    $id = $_POST['id'];
    $latitud = $_POST['latitud'];
    $longitud = $_POST['longitud'];
    //Si manda un 1 de activo
    if ($_POST['activo'] == 1) {
        $query = 'SELECT id FROM autobus WHERE id="' . $id . '"';
        $result = mysql_query($query);
        $parada = "100";
        //Si el autobus existe, actualiza los datos, si no, inserta un nuevo autobus
        if ((mysql_num_rows($result)) != 0) {
            $query = 'UPDATE autobus SET latitud = ' . $latitud . ',longitud=' . $longitud . ', activo=1 WHERE id="' . $id . '"';
            $result = mysql_query($query);
            // echo 'update ';
        } else {
            $query = 'INSERT INTO autobus (id, latitud, longitud, parada, activo,ruta) '
                    . 'VALUES ("' . $id . '",' . $latitud . ',' . $longitud . ',1,1,1)';
            $result = mysql_query($query);
            echo mysql_error();
        }
        //Regresa lo siguiente al telefono.
        if ($result) {
            echo "Latitud: " . $latitud . "\n\n";
            echo "Longitud: " . $longitud;
        } else {
            echo "No funciono";
            echo $id;
        }
    } else {
        //Si manda un activo=0, entonces actualiza el estado del autobus, diciendo que esta inactivo.
        $query = 'UPDATE autobus SET latitud = ' . $latitud . ',longitud=' . $longitud . ', parada=-1, activo=0 WHERE id="' . $id . '"';;
        $result = mysql_query($query);
        if ($result) {
            echo "Desactivado correctamente";
        } else {
            echo "No se desactivo correctamente";
        }
    }
}
?>

