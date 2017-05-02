<?php
namespace App\Controladores;
defined("APPPATH") OR die("Acceso denegado..");

use Core\Vista;
use Core\App;
use Core\Auth;
use Core\Helper;
use Core\DataBase;

class Agente
{
    public function index(){
        Auth::auth();
        $req = new App;
        //echo $req->get_tipo_peticion();;
        Vista::set('titulo','Agente | Archivos Cargados');
        Vista::render('agente.index');
    }

    public function cargainformacion(){
        Auth::auth();

        //Vista::set("errores",["error 1", "error 2"]);
        Vista::set("titulo",'Agente | Carga de información comercial');
        Vista::render('agente.cargarInformacion');
    }

    public function cargar(){
        Helper::peticion("POST");
        Auth::auth();
        $temp_dir = 'temp/';
        $errores = [];
        $estado = true;
        $file_req = $_FILES["informacion_comercial"];
        //var_dump($file_req);
        $file_name = explode("." , $file_req["name"])[0];
        $ext = explode(".",$file_req["name"])[1];
        $dist = substr($file_name,0,1);
        $campania = substr($file_name,1,1);
        $anio = substr($file_name,2,2);
        $mes = substr($file_name,4,1);
        $tabla = substr($file_name,5,strlen($file_name)-4);
        //echo "dist: {$dist} campa: {$campania} año: {$anio} mes: {$mes} tabla: {$tabla}  ext: {$ext} \n - ";
        if(strtolower($ext) != "txt"){
            array_push($errores, 'Extencion no soportada "'.$ext.'"');
            $estado = false;
        }
        $con = new DataBase();
        $sql = "select COUNT(1) as contador from tblDistribuidor INNER JOIN tblCuenta ON tblDistribuidor.idDistribuidor = tblCuenta.idDistribuidor where tblCuenta.nombreUsuario = '" . USUARIO ."' and tblDistribuidor.codigo = '{$dist}'";
        $con->query($sql);
        if($con->get_result()[0]["contador"] == 0){
            array_push($errores, 'Indentificación de distribuidor no corresponde a su usuario');
            $estado = false;
        }
        if($campania != "C"){
            array_push($errores,"Código de campaña no es valido");
            $estado = false;
        }
        if($anio > (idate('y'))){
            array_push($errores, "No se puede cargar un archivo de un año superior");
            $estado = false;
        }
        $meses = [1,2,3,4,5,6,7,8,9,"O","N","D"];
        if(!in_array($mes,$meses)){
            array_push($errores,"Identificador de mes es invalido");
            $estado = false;
        }
        if($estado){
           // echo "Nombre archivo Correcto";
            //var_dump($file_req);
            if(copy($file_req["tmp_name"], $temp_dir . $file_req["name"])){
                $file = fopen($temp_dir . $file_req["name"], "r");
                $cols = explode("\t",fgets($file));
                if(count($cols) != 26){
                    array_push($errores, "Columnas de encabezado deben de ser 26 no ". count($cols));
                    $estado = false;
                }else{
                    //procesar columnas de encabezado 
                    $encabezados = ["IDUsuario", "Tarifa", "TipoRegistro",
                                   "NroMedidor1","TipoMedidor1","FechaColocacion1",
                                   "NroMedidor2","TipoMedidor2","FechaColocacion2",
                                   "NroMedidor3","TipoMedidor3","FechaColocacion3",
                                   "Nombre","Calle","Numero","Piso","Unidad","Telefono",
                                   "CodigoPostal","Departamento","Municipio","Aldea",
                                   "Canton","Caserio","Potencia","PlanFacturacion"
                                  ];
                    
                    foreach($cols as $col){
                        if(!in_array(trim($col),$encabezados)){
                            array_push($errores,"Columna no valida: {$col}");
                            echo "Columna no valida: {$col}";
                            $estado = false;
                        }
                    }
                    if($estado){
                        // Si las columnas de encabezado estan correctas
                        // se procesara las filas del archivo
                        // realizar una transaccion para dar rollback a la operacion
                        
                        while(! feof($file)){
                            var_dump(explode("\t",fgets($file)));
                            break;
                        }
                        
                    }
                }
                //var_dump($errores);

                fclose($file);
                unlink($temp_dir . $file_req["name"]);
            }else
                echo "Error al almacenar el archivo";
            die();
        }



        //$req = new App;
        //$data = $req->get_files()['archivo_datos'];

        /*if(copy($data['tmp_name'], $temp_dir . $data['name'])){
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
            echo "Error al copiar archivo";*/

        Vista::set('errores',$errores);
        Vista::set('titulo','Vista de archivo');
        //Vista::set('header',$header);
        //Vista::set('registros',$registros);
        Vista::render('agente.cargarInformacion');
        //echo $req->get_tipo_peticion();
    }

    public function salir(){
        //Helper::peticion("POST");
        echo "salir";
        Auth::finalizar();
    }


}