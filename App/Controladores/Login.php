<?php
namespace App\Controladores;
defined("APPPATH") OR die("Acceso denegado..");

use Core\Vista;

class Login
{
    public function index(){
        Vista::set('titulo', 'Login');
        Vista::render('login.index');
    }   

}