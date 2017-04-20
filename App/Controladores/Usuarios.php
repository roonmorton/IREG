<?php
namespace App\Controladores;
defined("APPPATH") OR die("Acceso denegado..");


use Core\Vista;
use Core\DataBase;

class Usuarios
{

    public function index()
    {
        $con = DataBase::instancia();
        $sql = "select * from usuario";
        $stm = $con->prepare($sql);
        $stm->execute();
        $res = $con->get_all($stm->get_result());
        $stm->close();
        $con->terminar();
        
        //var_dump(get_class_methods($stm->get_result()));
        //var_dump($con->get_all($stm));
        //$usuario = $con->get_all();
        
        Vista::set('usuarios', $res);
        Vista::set('titulo','Listado de Usuarios');
        Vista::render('usuarios.index');
    }
    
    public function crear(){
        Vista::set('titulo','Crear Nuevo usuario');
        Vista::render('usuarios.create');
    }
    
    public function guardar($nombre, $telefono){
        
    }

}