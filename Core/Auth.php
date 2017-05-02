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
        if (!isset($_SESSION['usuario'])) {
            //$con = DataBase::instancia();
            $sql = "SELECT * FROM TBLCUENTA WHERE nombreUsuario = '{$usuario}' and contrasena = '{$password}' ";
            $con = new DataBase();
            $con->query($sql);
            $users = $con->get_result();
            $con->terminar();
            unset($con);
            //var_dump($con);
            // die("fin");

            //echo $sql;
            //var_dump($sql);
            //$stm = $con->prepare($sql);
            //$stm->execute();
            //$res = $con->get_all($stm->get_result());
            //$stm->close();
            //$con->terminar();
            //var_dump($users);
            $data;
            if(count($users) > 0){
                $_SESSION['usuario'] = $users[0]["nombreUsuario"];
                $data = ["estado" => true,"user" => self::tipo_cuenta()];

                //echo self::tipo_cuenta();
                //self::auth();
            }else{
                //echo "no coincide";
                $data = ["estado" => false,"user" => "none"];

            }
            //var_dump($data);
            //var_dump($res);
            //die();
            //$_SESSION['usuario'] = 0;
        } else{
            //echo "ya definida sesion";
            //echo "Ya se ha iniciado sesion";
            //self::auth();
            //self::finalizar();

            //echo self::tipo_cuenta();
            $data = ["estado" => true,"user" => self::tipo_cuenta()];
            //return true;
            //unset($_SESSION["usuario"]);
        }
        return $data;

        //var_dump($_SESSION);
    }


    public static function tipo_cuenta(){

        $con = new DataBase();
        $sql = "select cta.nombreUsuario, cta.nombres as usuario, cta.estadoPass as estado_pass, tpcta.descripcion as tipo_cuenta from tblcuenta as cta INNER JOIN tblTipoCuenta as tpcta ON cta.idTipoCuenta = tpcta.idTipoCuenta WHERE cta.nombreUsuario = '{$_SESSION['usuario']}'";

        $con->query($sql);
        $users = $con->get_result();
        $con->terminar();
        unset($con);
        $user = $users[0];
        //echo $sql;
        //$stm = $con->prepare($sql);
        //$stm->execute();
        //$user = $con->get_all($stm->get_result())[0];
        //$stm->close();
        //$con->terminar();   
        //$usuario = $res[0];
        //var_dump($users);
        define('NOMBRE_USUARIO', $user["usuario"]);
        define("USUARIO",$user["nombreUsuario"]);
        return strtolower($user['tipo_cuenta']);
    }


    public static function auth(){
        session_start();
       // $controlador = explode("/",$_GET['url'])[0];
        $app = new App();
        //var_dump($controlador);
        if(isset($_SESSION['usuario'])){
            //define('')
            // si tenemos sesion iniciada redirigir a la vista del tipo de usuario que le corresponde 

            //if(!($ubicacion == $user['tipo_cuenta']))
            //Helper::ir('login');


            $con = new DataBase();
            $sql = "select count(1) as ct from tblCuenta where nombreUsuario = '{$_SESSION['usuario']}'";
            $con->query($sql);
            if($con->get_result()[0]["ct"] <= 0){
                unset($_SESSION['usuario']);  
                session_destroy();
                self::auth();
            }else{
                //var_dump(self::tipo_cuenta());
                $cuenta = self::tipo_cuenta();
                $sql = "SELECT COUNT(1) as ct from tblCuenta WHERE nombreUsuario = '{$_SESSION['usuario']}' and estadoPass = 0";
                $con->query($sql);
                //echo $sql;die();
                if($con->get_result()[0]["ct"] >= 1){
                    if($app->getMetodo() != "cambiocontrasena" and $app->getMetodo() != 'guardarnuevacontrasena'){
                        Helper::ir("login/cambiocontrasena");
                    }
                }else{
                    //var_dump($cuenta);
                    if($cuenta != $app->getControlador()){
                        Helper::ir($cuenta);
                    }
                }

            }
            $con->terminar();
            unset($con);
            //var_dump(NOMBRE_USUARIO);

            //var_dump($ubicacion);
            //var_dump($var);
            //var_dump(strtoupper($ubicacion));
            //var_dump($user['tipo_cuenta']);

        }else{
            if($app->getControlador() != "login")
                Helper::ir("login");
            // Helper::ir("login");
        }
    }

    public static function finalizar(){
        session_start();
        if(isset($_SESSION['usuario'])){
            unset($_SESSION['usuario']);
            session_destroy();
            self::auth();
        }else
            echo "no hay usuario";
    }

}