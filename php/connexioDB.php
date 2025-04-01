<?php
    //!AÑADIR NOMBRE DE BASE DE DATOS
    $cadena_connexio = 'mysql:dbname=projecte_php;host=localhost:3335';
    $usuari = 'root';
    $passwd = '';
    try{
        $db = new PDO($cadena_connexio, $usuari, $passwd, 
            array(PDO::ATTR_PERSISTENT => true));
    }catch(PDOException $e){
        echo 'Error amb la BDs: ' . $e->getMessage();
    }
?>