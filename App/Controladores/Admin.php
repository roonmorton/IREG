<?php
namespace App\Controladores;
defined("APPPATH") OR die("Acceso denegado..");


use Core\Vista;
use Core\DataBase;
use Core\Helper;
use Core\Auth;

class Admin
{
    public function index(){
        Auth::auth();
        Vista::set('titulo','Admin | Home');
        Vista::render('admin.home.index');
    }

    public function agentes()
    {
        Auth::auth();
        $con = new DataBase();
        $sql = "select * from tblPais";
        $con->query($sql);
        $paises = $con->get_result();
        $sql = "select dis.nombre, dis.idDistribuidor, dis.codigo, pa.nombre as pais from tblDistribuidor as dis join tblPais as pa ON pa.idPais = dis.idPais";
        $con->query($sql);
        $distribuidores = $con->get_result();
        $con->terminar();
        unset($con);

        Vista::set('paises',$paises);
        Vista::set('distribuidores',$distribuidores);
        Vista::set('titulo','Admin | Agentes');
        Vista::render('admin.agente.index');
    }





    public function cuentas(){
        Auth::auth();
        $con = new DataBase();
        $sql = "select cta.idCuenta, cta.nombres, cta.nombreUsuario, cta.estadoPass, tcta.descripcion as tipoCuenta,  dist.nombre as distribuidor from tblCuenta as cta INNER JOIN tblTipoCuenta as tcta on cta.idTipoCuenta = tcta.idTipoCuenta LEFT JOIN tblDistribuidor as dist on dist.idDistribuidor = cta.idDistribuidor";
        $con->query($sql);
        $cuentas = $con->get_result();
        Vista::set('cuentas',$cuentas);
        $sql = "select * from tblTipoCuenta";
        $con->query($sql);
        $tipoCuentas = $con->get_result();
        Vista::set('tipoCuentas',$tipoCuentas);
        $sql = "select * from tblDistribuidor";
        $con->query($sql);
        $distribuidores = $con->get_result();
        Vista::set('distribuidores',$distribuidores);
        $con->terminar();
        unset($con);
        Vista::set('titulo','Admin | Cuentas');
        Vista::render('admin.cuenta.index');
    }

    public function salir(){
        //Helper::peticion("POST");
        echo "salir";
        Auth::finalizar();
    }



    //metodo para guardar agente
    public function guardarAgente(){
        Helper::peticion('post');
        Auth::auth();
        $agente = $_POST['nombre_agente'];
        $identificador = $_POST['identificador'];
        $idPais = $_POST['pais'];
        $idDistribuidor = $_POST["id"];
        $con = new DataBase();
        $sql = "select * from tblDistribuidor where idDistribuidor = {$idDistribuidor}";
        $con->query($sql);
        if(count($con->get_result()) > 0)
            $sql = "UPDATE TBLDISTRIBUIDOR SET nombre = '{$agente}', codigo = '{$identificador}', idPais = {$idPais} WHERE idDistribuidor = {$idDistribuidor}";
        else
            $sql = "insert into tblDistribuidor(nombre,codigo,idPais) values('$agente','$identificador','$idPais')";

        $con->query($sql);
        $con->terminar();
        unset($con);
        Helper::ir('admin/agentes');
    }

    public function eliminarAgente(){
        Helper::peticion('post');
        Auth::auth();
        $idDistribuidor = $_POST["id"];
        $con = new DataBase();
        $sql = "select count(1)  as ct from tblDistribuidor, tblCuenta where tblCuenta.idDistribuidor = tblDistribuidor.idDistribuidor";
        $con->query($sql);
        //var_dump($con->get_result());
        if($con->get_result()[0]["ct"] >= 1){
            echo '<script> alert("No se puede eliminar Agente poque otros datos dependen de el...") </script>';
        }else{

            $sql = "delete from tblDistribuidor where idDistribuidor = {$idDistribuidor}";
            $con->query($sql);
        }
        $con->terminar();
        unset($con);
        Helper::ir('admin/agentes');
    }

    //Metodos para cuentas

    public function guardarCuenta(){
        Helper::peticion("post");
        Auth::auth();
        $idCuenta = $_POST["idCuenta"];
        $nombres = $_POST["nombres"];
        $username = $_POST["username"];
        $idTipoCuenta = ($_POST["tipoCuenta"] == "") ? null : $_POST["tipoCuenta"];
        $idDistribuidor = $_POST["distribuidor"];
        $idDistribuidor = ($idDistribuidor == "") ? "null" : $idDistribuidor;
        //$idDistribuidor = null;
        
        
        $con = new DataBase();
        $sql = "select * from tblCuenta where idCuenta = {$idCuenta}";
        $con->query($sql);
        if(count($con->get_result()) > 0){
            $sql = "update tblCuenta set nombres = '{$nombres}', nombreUsuario = '{$username}', idDistribuidor = {$idDistribuidor},idTipoCuenta = {$idTipoCuenta} where idCuenta = {$idCuenta}";
        }else
            $sql = "insert into tblCuenta(nombres,nombreUsuario,idDistribuidor,idtipoCuenta) values('{$nombres}','{$username}',{$idDistribuidor},'{$idTipoCuenta}')";
        echo $sql;
        //die();
        $con->query($sql);
        unset($con);
        Helper::ir("admin/cuentas");
    }

    public function eliminarCuenta(){
        Helper::peticion("POST");
        Auth::auth();

        $idCuenta = $_POST["idCuenta"];
        $con = new DataBase();

        $sql = "select count(1) as ct from tblCuenta, tblArchivo where tblCuenta.idCuenta = tblArchivo.idCuenta";
        $con->query($sql);

        if($con->get_result()[0]["ct"] >= 1){
            echo '<script> alert("No se puede eliminar Agente poque otros datos dependen de el...") </script>';
            die();
        }else{
            $sql = "delete from tblCuenta where idCuenta = {$idCuenta}";
            $con->query($sql);
        }
        $con->terminar();
        unset($con);

        Helper::ir("admin/cuentas");
        //var_dump($idCuenta);
    }

    public function reestablecerPass(){
        Helper::peticion("POST");
        Auth::auth();

        $idCuenta = $_POST["idCuenta"];
        $con = new DataBase();

        $sql = "UPDATE tblCuenta set estadoPass = 0, contrasena = 'xx' where idCuenta = {$idCuenta}";
        if($con->query($sql)){
            $con->terminar();
            unset($con);
            Helper::ir("admin/cuentas");
        }
    }


}