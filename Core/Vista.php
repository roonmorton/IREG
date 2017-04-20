<?php 
namespace Core;

defined('APPPATH') or die('Acceso denegado...');

class Vista{

    protected static $data = [];

    const DIRECTORIO_VISTAS = 'App/Vistas/';

    const EXTENCION_VISTAS = '.phtml';


    public static function render($vista){

        $dir_vista = str_replace('.','/',$vista);
        $template = self::DIRECTORIO_VISTAS . $dir_vista . self::EXTENCION_VISTAS;
        if(!file_exists($template)){
            echo 'Vista no encontrada: ' . PROJECTPATH.$template;
            exit;
        }
        self::myInclude($template);
    }

    public static function myInclude($template){
        ob_start();
        extract(self::$data);
        include($template);
        $str = ob_get_contents();
        ob_end_clean();
        echo $str;
    }
    public static function set($nombre, $valor){
        self::$data[$nombre] = $valor;
    }

    public static function parcial($vista){
        self::renderizar(self::rutaParcial($vista));
    }

    private static function rutaParcial($dir){
        return self::DIRECTORIO_VISTAS . 'parciales/' . str_replace('.','/', $dir) . '.phtml';    
    }

    private static function renderizar($pathVista){
        if(is_readable($pathVista)){
            self::myInclude($pathVista);

        }else{
            echo 'No se encontro vista a cargar: ' . str_replace('.','/',$pathVista) . '.phtml' ;
            exit;
        }
    }
}
