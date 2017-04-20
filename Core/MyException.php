<?php
namespace Core;

defined('APPPATH') or die('Acceso denegado...');

class MyException{
    
    public static function Mensaje($ex){
        echo "<br>Exepcion producida: <strong>" . $ex->getMessage() . "</strong><br> Archivo: <strong>" . $ex->getFile() . " Linea: " . $ex->getLine() . "</strong><br> Codigo error: <strong>" . $ex->getCode() . "</strong>";
        die("<hr>Finalizado con error...");
    }
    /* propiedades heredadas */
}
