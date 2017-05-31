<?php
namespace Core;

defined('APPPATH') or die('Acceso denegado...');

use \Core\MyException;
use \Core\DataBase;
use \Core\Helper;
use \Core\App;


class Auth{

    public static function iniciar($usuario, $password){
        session_start();
        if (!isset($_SESSION['usuario'])) 
        {
            $sql = "SELECT * FROM TBLCUENTA WHERE nombreUsuario = '{$usuario}' and contrasena = '{$password}' ";
            $con = new DataBase();

            if($con->query($sql))
            {
                $users = $con->get_result();
                $con->terminar();
                unset($con);
                $data;
                if(count($users) > 0)
                {
                    $_SESSION['usuario'] = $users[0]["nombreUsuario"];
                    $data = ["estado" => true,"user" => self::tipo_cuenta()];
                }else
                    $data = ["estado" => false,"user" => "none"];  
            }
            else
                $data = ["estado" => false, "user" => "none","message" => "A ocurrido un error..."];
        } 
        else
            $data = ["estado" => true,"user" => self::tipo_cuenta()];
        return $data;
    }
    

    public static function tipo_cuenta(){

        $con = new DataBase();
        $sql = "select cta.nombreUsuario, cta.nombres as usuario, cta.estadoPass as estado_pass, tpcta.descripcion as tipo_cuenta from tblcuenta as cta INNER JOIN tblTipoCuenta as tpcta ON cta.idTipoCuenta = tpcta.idTipoCuenta WHERE cta.nombreUsuario = '{$_SESSION['usuario']}'";
        $con->query($sql);
        $users = $con->get_result();
        $con->terminar();
        unset($con);
        $user = $users[0];
        //if(!defined('NOMBRE_USUARIO'))
        define('NOMBRE_USUARIO', $user["usuario"]);
        //if(!defined('USUARIO'))
        define("USUARIO",$user["nombreUsuario"]);
        return strtolower($user['tipo_cuenta']);
    }


    public static function auth()
    {
        session_start();
        $app = new App();
        if(isset($_SESSION['usuario']))
        {
            $con = new DataBase();
            $sql = "select count(1) as ct from tblCuenta where nombreUsuario = '{$_SESSION['usuario']}'";
            $con->query($sql);
            if($con->get_result()[0]["ct"] <= 0)
            {
                unset($_SESSION['usuario']);  
                session_destroy();
                self::auth();
            }
            else
            {
                $cuenta = self::tipo_cuenta();
                $sql = "SELECT COUNT(1) as ct from tblCuenta WHERE nombreUsuario = '{$_SESSION['usuario']}' and estadoPass = 0";
                $con->query($sql);
                if($con->get_result()[0]["ct"] >= 1)
                {
                    if($app->getMetodo() != "cambiocontrasena" and $app->getMetodo() != 'guardarnuevacontrasena')
                        Helper::ir("login/cambiocontrasena");
                }
                else
                {   
                    if($cuenta != $app->getControlador())
                        Helper::ir($cuenta);
                }
            }
            $con->terminar();
            unset($con);
        }
        else{
            if($app->getControlador() != "login")
                Helper::ir("login");
            else if($app->getMetodo() == "salir" && $app->getControlador() == "login")
                Helper::ir("login");
        }
    }


    public static function finalizar(){
        session_start();
        if(isset($_SESSION['usuario'])){
            unset($_SESSION['usuario']);
            session_destroy();
            self::auth();
        }else{
            echo "no hay usuario";
            self::auth();
        }
    }

}