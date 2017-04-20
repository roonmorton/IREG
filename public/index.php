<?php 

//nombre de proyecto
define('APPNAME','ireg');

//Directorio Proyecto
define('PROJECTPATH',dirname(__DIR__));

//Directorio app
define('APPPATH', PROJECTPATH . '/App' );

//controlador que cargara por defecto
define('CONTROLADOR_DEFECTO','home');

// autoload con namespaces
function autoload_classes($nombre_clase){
    $nombre_archivo = PROJECTPATH . '/' .str_replace('\\','/',$nombre_clase) . '.php';
    if(is_file($nombre_archivo)){
        include_once $nombre_archivo;
    }
}

//Registramos el autoload autoload_classes
spl_autoload_register('autoload_classes');

//instanciamos el app
$app = new \Core\App;

//lanzamos la aplicacion 
$app->iniciar();