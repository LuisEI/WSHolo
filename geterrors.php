<?php
/**
 * Obtiene todas los errores
 */

require 'Datalive.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET') {
    
    $datos = Datalive::getLastOrder();

    if ($datos) {
        $orden = $datos[0]['n_lote'];

        $errores = Datalive::getLastErrors($orden);
        
        print json_encode($errores);


    } else {
        $error = array();
        $error[0] = 'Error';
        print json_encode($error);
    }

}
