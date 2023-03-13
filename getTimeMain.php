<?php
/**
 * Obtiene todas las ordenes
 */

require 'Datalive.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET') {
    
    $datos = Datalive::getTimeMaintenance();

    if ($datos) {
        print json_encode($datos);
    } else {
        $error = array();
        $error[0] = 'Error';
        print json_encode($error);
    }

}
