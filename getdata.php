<?php
/**
 * Obtiene todas las ordenes
 */

require 'Datalive.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET') {
    
    $machine = $_REQUEST['machine'];
    $datos = Datalive::getMachineData($machine);

    if ($datos) {
        print json_encode($datos);
    } else {
        $error = array();
        $error[0] = 'Error';
        print json_encode($error);
    }

}
