<?php 

//nombre de proyecto
define('APPNAME','ireg');

//Directorio Proyecto
define('PROJECTPATH',dirname(__DIR__) . '/' . APPNAME );

//Directorio app
define('APPPATH', PROJECTPATH . '/App' );
//echo PROJECTPATH;
//controlador que cargara por defecto
define('CONTROLADOR_DEFECTO','home');

// autoload con namespaces
function autoload_classes($nombre_clase){
    $nombre_archivo = PROJECTPATH . '/' .str_replace('\\','/',$nombre_clase) . '.php';
    if(is_file($nombre_archivo)){
        include_once $nombre_archivo;
    }
    //echo $nombre_archivo;
}

//Registramos el autoload autoload_classes
spl_autoload_register('autoload_classes');

include "Core/App.php";
//instanciamos el app
$app = new \Core\App;

//lanzamos la aplicacion 
$app->iniciar();