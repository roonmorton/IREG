<?php
namespace App\Controladores;
defined("APPPATH") OR die("Acceso denegado..");

use Core\Vista;
use Core\Auth;
use Core\Helper;
use Core\DataBase;
//$error = [];

class Login
{
    public function index(){

        //Auth::iniciar('admin','xx');

        //var_dump($_GET);
        //die();
        //var_dump($error);
                //Auth::finalizar();
//echo"askdj";
        Auth::auth();
        Vista::set('titulo', 'Login');
        Vista::render('login.index');
    }  

    public function iniciar(){
        Helper::peticion('POST');
        if(isset($_POST['usuario']) and isset($_POST['password'])){
            $usuario = $_POST['usuario'];
            $pass = $_POST['password'];
            //echo "iniciar";
            //$json = file_get_contents("php://input");
            //echo var_dump(json_decode($json));
            //echo json_encode(var_dump());
            //print json_encode(["val"=>true ]);
            echo json_encode(Auth::iniciar($usuario,$pass));
        }else{
            echo json_encode(["msg" => "Acceso denegado..."]);
        }
    }
    
    public function cambiocontrasena(){
        Auth::auth();
        //Auth::authChPass();

        Vista::set('titulo','Login | Cambio contraseña');
        Vista::render('login.reestablecercontrasena');
        
    }
    
    public function guardarNuevaContrasena(){
        Helper::peticion("POST");
        Auth::auth();
        $pass = $_POST["newpass"];
        $con = new DataBase();
        
        $sql = "update tblCuenta set contrasena = '{$pass}', estadoPass = 1 where nombreUsuario = '{$_SESSION['usuario']}'";
        if($con->query($sql)){
            Auth::auth();
            AUth::finalizar();
        }
        $con->terminar();
        unset($con);
    }
    
     public function salir(){
        //Helper::peticion("POST");
        echo "salir";
        Auth::finalizar();
    }

}