<?php
namespace App\Controladores;
defined("APPPATH") OR die("Acceso denegado..");

use Core\Vista;
use Core\App;

class Carga
{
    public function index(){
        $req = new App;
        //echo $req->get_tipo_peticion();;
        Vista::set('titulo','Carga Archivo');
        Vista::render('carga.index');
    }

    public function cargar(){
        $temp_dir = '../temp/';
        $req = new App;
        $data = $req->get_files()['archivo_datos'];

        if(copy($data['tmp_name'], $temp_dir . $data['name'])){
            //echo "Archivo cargado";
            $file = fopen($temp_dir . $data['name'],'r');
            
            $header = explode("\t",fgets($file));
            //var_dump($header);
            $registros = [];
            while(!feof($file)){
                $registros[] = explode("\t",fgets($file));
            }
            fclose($file);
            unlink($temp_dir . $data['name']);
        }else
            echo "Error al copiar archivo";

        Vista::set('titulo','Vista de archivo');
        Vista::set('header',$header);
        Vista::set('registros',$registros);
        Vista::render('carga.vista');
        //echo $req->get_tipo_peticion();
    }


}