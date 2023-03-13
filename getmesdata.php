<?php
/**
 * Obtiene todas las ordenes
 */
require 'Datalive.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET') {

    $res = Datalive::getLastOrder($machine);;
    $order =  $res[0]["n_lote"];
    $db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = 10.0.50.7)(PORT = 1521)))(CONNECT_DATA=(SID=REPSALPROD)))" ;

    if($conn = oci_connect("ELS_MES_REPORTING", "fwreports2018", $db))
    {
        $sql = "SELECT A.BATCH,A.MES_STEP_NAME,A.EQP,A.ENTERED_BY,A.QTY_PCS,A.SCRAPPED_PCS
        FROM MANUFACT_HIST.FACT_STEP A
        INNER JOIN (SELECT TRANSACTION_ID, BATCH
                    FROM MANUFACT_HIST.FACT_STEP
                    WHERE BATCH = '$order' AND MES_STEP_NAME = 'S_ASO_TapeReel_LF') B ON B.BATCH = A.BATCH AND A.TRANSACTION_ID < B.TRANSACTION_ID
        WHERE ROWNUM <= 12
        ORDER BY A.TRANSACTION_ID DESC";

        $stid = oci_parse($conn, $sql);
        $r = oci_execute($stid);
        $data = [];
        $data['estado'] = oci_fetch_all($stid, $res, null, null, OCI_FETCHSTATEMENT_BY_ROW);
    }
    else
    {
        $err = OCIError();
        print "Connection failed." . $err[text];
    }

    OCILogoff($conn);

    if($data['estado']){
        print json_encode($res);
    } else {
        print 'Error';
    }

}
