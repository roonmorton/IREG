<?php
namespace App\Controladores;
defined("APPPATH") OR die("Acceso denegado..");


use Core\Vista;
use Core\DataBase;
use Core\Helper;

class Admin
{
    public function index(){
        Vista::set('titulo','Admin | Home');
        Vista::render('admin.home.index');
    }

    public function agentes()
    {
        $con = DataBase::instancia();
        $sql = "select * from tblPais";
        $stm = $con->prepare($sql);
        $stm->execute();
        $res = $con->get_all($stm->get_result());

        Vista::set('paises',$res);

        $sql = "select dis.nombre, dis.idDistribuidor, dis.codigo, pa.nombre as pais from tblDistribuidor as dis join tblPais as pa ON pa.idPais = dis.idPais";
        $stm = $con->prepare($sql);
        $stm->execute();
        $res = $con->get_all($stm->get_result());
        Vista::set('distribuidores',$res);
        $stm->close();
        $con->terminar();

        Vista::set('titulo','Admin | Agentes');
        Vista::render('admin.agente.index');
    }





    public function cuentas(){
        
        $con = DataBase::instancia();
        $sql = "select cta.idCuenta, cta.nombres, cta.nombreUsuario, cta.estadoPass, tcta.descripcion as tipoCuenta,  dist.nombre as distribuidor from tblCuenta as cta INNER JOIN tblTipoCuenta as tcta on cta.idTipoCuenta = tcta.idTipoCuenta LEFT JOIN tblDistribuidor as dist on dist.idDistribuidor = cta.idDistribuidor";
        $stm = $con->prepare($sql);
        $stm->execute();
        
        $res = $con->get_all($stm->get_result());
        Vista::set('cuentas',$res);
        
        $sql = "select * from tblTipoCuenta";
        $stm = $con->prepare($sql);
        $stm->execute();
        Vista::set('tipoCuentas',$con->get_all($stm->get_result()));
        
        $sql = "select * from tblDistribuidor";
        $stm = $con->prepare($sql);
        $stm->execute();
        Vista::set('distribuidores',$con->get_all($stm->get_result()));
        
        $stm->close();
        $con->terminar();
        
        Vista::set('titulo','Admin | Cuentas');
        Vista::render('admin.cuenta.index');
    }

    public function salir(){
        echo "salir";
    }



    //metodo para guardar agente
    public function guardarAgente(){
        Helper::peticion('post');
        $agente = $_POST['nombre_agente'];
        $identificador = $_POST['identificador'];
        $idPais = $_POST['pais'];
        $idDistribuidor = $_POST["id"];

        $con = DataBase::instancia();

        $sql = "select * from tblDistribuidor where idDistribuidor = {$idDistribuidor}";
        $stm = $con->prepare($sql);
        $stm->execute();
        if(count($con->get_all($stm->get_result())) > 0){
            $sql = "UPDATE TBLDISTRIBUIDOR SET nombre = '{$agente}', codigo = '{$identificador}', idPais = {$idPais} WHERE idDistribuidor = {$idDistribuidor}";
        }else{
            $sql = "insert into tblDistribuidor(nombre,codigo,idPais) values('$agente','$identificador','$idPais')";
        }

        $stm = $con->prepare($sql);
        $stm->execute();
        $stm->close();
        $con->terminar();
        Helper::ir('admin/agentes');
    }

    public function eliminarAgente(){
        Helper::peticion('post');
        $idDistribuidor = $_POST["id"];
        
        $con = DataBase::instancia();
        $sql = "delete from tblDistribuidor where idDistribuidor = {$idDistribuidor}";
        $stm = $con->prepare($sql);
        $stm->execute();
        $stm->close();
        $con->terminar();
        
        Helper::ir('admin/agentes');
    }


}