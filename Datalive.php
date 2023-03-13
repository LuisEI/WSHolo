<?php

/**
 * Manejo de los datos de Auditoria
 */
require 'MySQL_DB_PROD.php';

class Datalive
{
    function __construct()
    {
    }

    /**
     * 
     *
     */
    public static function getMachineData($machine)
    {
        $consulta = "SELECT * FROM live_prod WHERE machine = '$machine'";
        
		try {
            
			$comando = Database::getInstance()->getDb()->prepare($consulta);
            $comando->execute();

            return $comando->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return Database::getInstance()->getDb()->errorInfo();
        }
    }

    /**
     * 
     *
     */
    public static function getTimeMaintenance()
    {
        $consulta = "SELECT 
        TIME_TO_SEC(TIMEDIFF(NOW(),dateStart)) AS inicio,
        TIME_TO_SEC(TIMEDIFF(NOW(),dateFinish)) AS restante, 
        TIME_TO_SEC(TIMEDIFF(dateFinish,dateStart)) AS total
        FROM maintenance;";
        
		try {
            
			$comando = Database::getInstance()->getDb()->prepare($consulta);
            $comando->execute();

            return $comando->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * 
     *
     */
    public static function getLastOrder()
    {
        $consulta = "SELECT n_lote FROM informacion 
        WHERE maquina = 'TPR422' 
        ORDER BY id_auto DESC LIMIT 1";
        
		try {
            
			$comando = Database::getInstance()->getDb()->prepare($consulta);
            $comando->execute();

            return $comando->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo $e;
            return false;
        }
    }

    /**
     * 
     *
     */
    public static function getLastErrors($orden)
    {
        $consulta = "SELECT a.error,n_errores,t_error FROM error AS a
        INNER JOIN reference_06w AS b
        ON a.error = b.error
        WHERE n_lote = '$orden' ORDER BY n_errores DESC LIMIT 10";
        
		try {
            
			$comando = Database::getInstance()->getDb()->prepare($consulta);
            $comando->execute();

            return $comando->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo $e;
            return false;
        }
    }

    /**
     * 
     *
     */
    public static function getTokaiTemp()
    {
        $consulta = "SELECT * FROM tokai_db.tki_temperaturas AS a
        INNER JOIN tokai_db.tki_gases AS b
        ON a.tmp_id_tokai = b.gas_id_tokai
        WHERE a.tmp_tokai_nbr = 'TK-05' ORDER BY tmp_id DESC LIMIT 1";
        
		try {
            
			$comando = Database::getInstance()->getDb()->prepare($consulta);
            $comando->execute();

            return $comando->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo $e;
            return false;
        }
    }
	
	
	/*
	*
	* SE ACTUALIZA EL ESTADO DE LA ORDEN A REVISADO EN EOP
	*
	*/
	public static function check_eop($orden)
    {
        $consulta = "UPDATE ordenes SET STATUS = 1, REVISION_EOP = NOW() WHERE BATCH = ?";

        $sentencia = Database::getInstance()->getDb()->prepare($consulta);

        return $sentencia->execute(
            array($orden)
        );

    }

    /*
	*
	* SE ACTUALIZA EL ESTADO DE LA ORDEN A REVISADO EN PACKING
	*
	*/
    public static function check_pack($orden)
    {
        $consulta = "UPDATE ordenes SET STATUS = 1, REVISION_PC = NOW() WHERE BATCH = ?";

        $sentencia = Database::getInstance()->getDb()->prepare($consulta);

        return $sentencia->execute(
            array($orden)
        );

    }

    /*
	*
	* SE MUEVEN LAS ORDENES AL SIGUIENTE PASO PC
	*
	*/
	public static function move_pc()
    {
        $consulta = "UPDATE ordenes SET STATUS = 0,STEP = 2 WHERE STATUS = 1 AND STEP = 1";

        $sentencia = Database::getInstance()->getDb()->prepare($consulta);

        return $sentencia->execute(
            array($orden)
        );

    }

        /*
	*
	* SE MUEVEN LAS ORDENES AL SIGUIENTE PASO El LISTADO PARA COMPRAR AMID
	*
	*/
	public static function move_list()
    {
        $consulta = "UPDATE ordenes SET STATUS = 0,STEP = 3 WHERE STATUS = 1 AND STEP = 2";

        $sentencia = Database::getInstance()->getDb()->prepare($consulta);

        return $sentencia->execute(
            array($orden)
        );

    }

	/**
     * 
     */
    public static function insertar_encontrado(
        $id_detalle,
		$imagen,
		$nombre_img,
		$id_auditoria,
		$comentario
    )
    {
		$ruta = "img_repo/$nombre_img";
		file_put_contents($ruta,base64_decode($imagen));
		
        $consulta = "INSERT INTO t_encontrado (id_detalle,ruta_imagen,id_auditoria,comentario) VALUES (?,?,?,?)";

        $sentencia = Database::getInstance()->getDb()->prepare($consulta);

        return $sentencia->execute(
            array(
                $id_detalle,
				$nombre_img,
				$id_auditoria,
				$comentario
            )
        );

    }

}

?>