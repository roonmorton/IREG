<?php
namespace Core;

defined('APPPATH') or die('Acceso denegado...');

use \Core\MyException;
use \Core\DataBase;
use \Core\Helper;


class Auth{

    public static function iniciar($usuario, $password){

        session_start();
        if (!isset($_SESSION['usuario'])) {
            $con = DataBase::instancia();
            $sql = "SELECT * FROM TBLCUENTA WHERE nombreUsuario = '{$usuario}' and contrasena = '{$password}' ";
            //var_dump($sql);
            $stm = $con->prepare($sql);
            $stm->execute();
            $res = $con->get_all($stm->get_result());
            $stm->close();
            $con->terminar();

            $estado = false;
            $data = [];
            if(count($res) > 0){
                $_SESSION['usuario'] = $res[0]["nombreUsuario"];
                self::auth();
            }else{
                return false;
            }
            //var_dump($data);
            //var_dump($res);
            //die();
            //$_SESSION['usuario'] = 0;
        } else{
            //echo "ya definida sesion";
            echo "Ya se ha iniciado sesion";
            self::auth();
            return true;
            //unset($_SESSION["usuario"]);
        }
        //var_dump($_SESSION);
    }


    public static function auth(){
        session_start();
        if(isset($_SESSION['usuario'])){
            //define('')
            // si tenemos sesion iniciada redirigir a la vista del tipo de usuario que le corresponde 
            $con = DataBase::instancia();
            $sql = "select cta.nombres as usuario, cta.estadoPass as estado_pass, tpcta.descripcion as tipo_cuenta from tblcuenta as cta INNER JOIN tblTipoCuenta as tpcta ON cta.idTipoCuenta = tpcta.idTipoCuenta WHERE cta.nombreUsuario = '{$_SESSION['usuario']}'";
            $stm = $con->prepare($sql);
            $stm->execute();
            $user = $con->get_all($stm->get_result())[0];
            $stm->close();
            $con->terminar();   
            //$usuario = $res[0];
            //var_dump($user);
            
            define('NOMBRE_USUARIO', $user["usuario"]);
            //var_dump(NOMBRE_USUARIO);
            $ubicacion = explode("/",$_GET['url']);
            //var_dump($var);
            var_dump(strtoupper($ubicacion));
            var_dump($user['tipo_cuenta']);
            
            //if(!($ubicacion == $user['tipo_cuenta']))
               //Helper::ir('login');
            
            
            
        }else{
            Helper::ir("login");
        }
    }

    public static function finalizar(){
        session_start();
        if(isset($_SESSION['usuario'])){
            unset($_SESSION['usuario']);
            session_destroy();
            self::auth();
        }
    }

}