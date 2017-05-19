<?php
namespace App\Controladores;
defined("APPPATH") OR die("Acceso denegado..");

use Core\Vista;
use Core\App;
use Core\Auth;
use Core\Helper;
use Core\DataBase;

class Analista
{

    public function index(){

        Auth::auth();
        Vista::set('titulo','Analista | Home');
        Vista::render('analista.index');
    }


    public function InformacionDistribuidor(){
        //$app = new App();
        //var_dump($app->getParametros());
        //echo "hola: " + $id;
        Auth::auth();
        Vista::set('titulo',"Analista | Datos de Agente");
        Vista::render("analista.informacion_agente");
    }

}