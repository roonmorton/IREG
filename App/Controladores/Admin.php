<?php
namespace App\Controladores;
defined("APPPATH") OR die("Acceso denegado..");
 

use Core\Vista;
use Core\DataBase;

class Admin
{
    public function index(){
        Vista::set('titulo','Administrador');
        Vista::render('admin.home.index');
    }
    
    public function agentes()
    {
        Vista::set('titulo','Administrador');
        Vista::render('admin.agente.index');
    }
    
    public function cuentas(){
        echo "soy vista de cuenta";
    }
    
    public function salir(){
        echo "salir";
    }
    
    
}