<?php
namespace App\Controladores;
defined("APPPATH") OR die("Acceso denegado..");
 

use Core\Vista;
use Core\DataBase;

class Home
{
    public function index(){
        Vista::set('titulo','Inicio');
        Vista::render('home.index');
    }
    
    public function saludo($nombre)
    {
        $con = DataBase::instancia();
        Vista::set('titulo','Saludo');
        Vista::set('nombre',$nombre);
        Vista::render('home.index');
    }
    
    
}