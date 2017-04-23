<?php
namespace App\Controladores;
defined("APPPATH") OR die("Acceso denegado..");

use Core\Vista;
use Core\Auth;
use Core\Helper;
//$error = [];

class Login
{
    public function index(){
        
        //Auth::iniciar('admin','xx');
        
        //var_dump($_GET);
        //die();
        //var_dump($error);
        Vista::set('titulo', 'Login');
        Vista::render('login.index');
    }  
    
    public function iniciar(){
        Helper::peticion('POST');
        
        //$usuario = $_POST['usuario'];
        //$pass = $_POST['contrasena'];
        //echo "iniciar";
        print json_encode(["val"=>true ]);
        /*if(!Auth::iniciar($usuario,$pass)){
            echo "un error";
            $error = ["error"];
            Helper::ir('admin/');
        }*/
    }

}