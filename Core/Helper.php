<?php namespace Core;

defined('APPPATH') or die('Acceso denegado...');

class Helper{
    
    public static function peticion($peticion){
        
        if($_SERVER['REQUEST_METHOD'] != strtoupper($peticion))
            die('Acceso denegado');
    }
    /* propiedades heredadas */
    
    public static function ir($url){
        header("Location: /" . APPNAME ."/$url");
    }
}

?>