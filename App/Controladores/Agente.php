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
        //$req = new App;
        //echo $req->get_tipo_peticion();;
        //echo "hola";
        $con = new DataBase();
        $sql = 'select tblarchivo.idArchivo, tblarchivo.fecha as fechaCarga, concat((CASE tblarchivo.mes WHEN "1" THEN "Enero" WHEN "2" THEN "Febrero" WHEN "3" THEN "Marzo" WHEN "4" THEN "Abril" WHEN "5" THEN "Mayo" WHEN "6" THEN "Junio" WHEN "7" THEN "Julio" WHEN "8" THEN "Agosto" WHEN "9" THEN "Septiembre" WHEN "O" THEN "Octubre" WHEN "N" THEN "Noviembre" WHEN "D" THEN "Diciembre" END), \'-\',tblarchivo.anio) as FECHA, CONCAT(tbldistribuidor.codigo,tblarchivo.calidadServicio,tblarchivo.anio,tblarchivo.mes, tblarchivo.nombreTabla) AS nombreArchivo from tblarchivo INNER JOIN tblcuenta ON tblarchivo.idCuenta = tblcuenta.idCuenta INNER JOIN tbldistribuidor ON tbldistribuidor.idDistribuidor = tblcuenta.idDistribuidor WHERE (tbldistribuidor.idDistribuidor) = (SELECT d.idDistribuidor FROM tbldistribuidor as d INNER JOIN tblcuenta as c on d.idDistribuidor = c.idDistribuidor where c.nombreUsuario = \'' .USUARIO .'\') Order by tblArchivo.idArchivo DESC';  
        
        //echo $sql;
        $archivos = [];
        if($con->query($sql)){
            $archivos = $con->get_result();
        }

        Vista::set("archivos",$archivos);
        Vista::set('titulo','Agente | Archivos Cargados');
        Vista::render('agente.index');
    }

    public function cargainformacion(){
        Auth::auth();

        //echo date("m");
        //Vista::set("errores",["error 1", "error 2"]);
        Vista::set("titulo",'Agente | Carga de información comercial');
        Vista::render('agente.cargarInformacion');
    }

    public function cargar(){
        Helper::peticion("POST"); //Aceptar peticiones POST
        Auth::auth(); // Verificar si hay usuario Logeado
        //echo  "hola 1";
        $tiempo_inicial = microtime(true);
        $temp_dir = 'temp/'; // Directorio temporal
        $errores = []; // Si existen errores
        $info = [];
        $estado = true; // estado del proceso
        $file_req = $_FILES["informacion_comercial"]; // Archivo en peticion
        //var_dump($file_req);
        $file_name = explode("." , $file_req["name"])[0]; //nombre archivo
        //echo $file_name;
        
        $ext = explode(".",$file_req["name"])[1]; //extencion de archivo
        
        $dist = substr($file_name,0,1); //identificador distribuidor
        $campania = substr($file_name,1,1); //Campaña
        $anio = substr($file_name,2,2); // año que corresponde la informacion
        $mes = substr($file_name,4,1); // mes que corresponde la informacion
        $tabla = substr($file_name,5,strlen($file_name)-4); // nombre del archivo
        //echo "dist: {$dist} campa: {$campania} año: {$anio} mes: {$mes} tabla: {$tabla}  ext: {$ext} \n - ";

        Vista::set("titulo","Agente | Visor de proceso");
        Vista::render("agente.informacion");

        $con = new DataBase();
        $sql = "select tblDistribuidor.nombre as distribuidor from tblCuenta inner join tblDistribuidor on tblCuenta.idDistribuidor = tblDistribuidor.idDistribuidor where tblcuenta.nombreUsuario = '".USUARIO."'";

        if($con->query($sql)) self::mensaje("Información",$con->get_result()[0]["distribuidor"]);
        else echo "error: " + $con->error();


        self::mensaje('Información','Extencion archivo: ' . $ext);
        if(trim(strtolower($ext)) != strtolower("txt"))
        {
            self::mensaje('Error','Extencion no soportada ');
            $estado = false;
        }
        $sql = "select COUNT(1) as contador from tblDistribuidor INNER JOIN tblCuenta ON tblDistribuidor.idDistribuidor = tblCuenta.idDistribuidor where tblCuenta.nombreUsuario = '" . USUARIO ."' and tblDistribuidor.codigo = '{$dist}'";

        unset($dist);
        unset($ext);

        $con->query($sql);
        if($con->get_result()[0]["contador"] == 0)
        {
            self::mensaje('Error','Archivo identificado para un agente diferente ');
            $estado = false;
        }
        if($campania != "C")
        {
            self::mensaje('Error','Código de campaña no es valido');
            $estado = false;
        }
        if($anio != (idate('y')))
        {
            self::mensaje('Error','No se puede cargar un archivo de un año distinto al actual');
            $estado = false;
        }

        //if($nDia > 10)

        $meses = [1,2,3,4,5,6,7,8,9,"O","N","D"];
        if(!in_array($mes,$meses))
        {
            self::mensaje('Error','Identificador de mes es invalido');
            $estado = false;
        }

        $nDia = date("j");
        $nMes = date("m");
        /*if($nDia > 10){
            self::mensaje('Error','Solo puede cargar información los primeros 10 dias de cada mes');
            $estado = false;
        }*/

        if(($nMes-1) != $mes){
            self::mensaje('Error','Solo se pueden cargar archivos de un mes anterior al actual');
            $estado = false;
        }

        //comprobar si archivo con el mismo nombre ya se ha cargado

        $sql = "select count(1) as archivos from tblArchivo inner join tblCuenta ON tblArchivo.idCuenta = tblCuenta.idCuenta INNER JOIN tblDistribuidor ON tblDistribuidor.idDistribuidor = tblCuenta.idDistribuidor where mes = {$mes} and anio = {$anio} and nombreTabla = '{$tabla}' and tblDistribuidor.idDistribuidor = (select dd.idDistribuidor from tblCuenta as cc inner join tblDistribuidor  as dd on dd.idDistribuidor = cc.idDistribuidor where cc.nombreUsuario = '" .USUARIO . "')";
        //echo $sql;//die();
        //die();
        if($con->query($sql)){
            //var_dump($con->get_result());
            if($con->get_result()[0]["archivos"] >= 1 ){
                self::mensaje('Error','Archivo ya se ha cargado...');
                $estado = false; 
            }
        }else{
            $estado = false;
        }
        
        //echo $sql;die();
        if($estado)
        {
            $file_name = getdate()[0] . $file_name . ".txt";
            if(copy($file_req["tmp_name"], $temp_dir . $file_name))
            {// Si se almacena el archivo se procesa
                $file = fopen($temp_dir . $file_name, "r"); //Abrir archivo
                $cols = explode("\t",fgets($file)); //obtener la fila del encabezado
               
                if(count($cols) != 26)
                {//si las columnas no son 26 se lanza error
                    self::mensaje('Error','Formato del archivo debe contener 26 columnas '  . count($cols));
                    $estado = false;
                }
                else
                {
                    //procesar columnas de encabezado 
                    $encabezados = [
                        "idusuario", // 0 vaciar espacios
                        "tarifa", // 1  vaciar espacios
                        "tiporegistro", // 2 vaciar espacios
                        "nromedidor1", // 3 vaciar espacios
                        "tipomedidor1", // 4 --
                        "fechacolocacion1", // 5
                        "nromedidor2", // 6 vaciar espacios
                        "tipomedidor2", // 7 --
                        "fechacolocacion2", // 8
                        "nromedidor3", // 9 vaciar espacios
                        "tipomedidor3", // 10 --
                        "fechacolocacion3", // 11
                        "nombre", // 12 //quitar comillas
                        "calle", // 13
                        "numero", //14
                        "piso", // 15
                        "unidad", // 16
                        "telefono", // 17
                        "codigopostal", // 18 //vaciar espacios
                        "departamento", // 19 
                        "municipio",// 20
                        "aldea", // 21 
                        "canton",// 22
                        "caserio", // 23
                        "potencia", // 24 vaciar espacios
                        "planfacturacion" // 25 vaciar espacios
                    ];



                    foreach($cols as $col){
                        if(! in_array(trim(strtolower($col)),$encabezados)){
                            self::mensaje('Error','Columna no valida: ' . trim(strtolower($col)));
                            $estado = false;
                        }
                    }

                    if($estado){
                        self::mensaje('Información',"Encabezado procesado correctamente");

                        // Si las columnas de encabezado estan correctas
                        // se procesara las filas del archivo
                        // realizar una transaccion para deshacer los cambios por si surje un error;
                        $con->autocommit(false);
                        try{
                            $idArchivo = 0;
                            set_time_limit(600);
                            $count = 0;
                            self::mensaje('Información',"Procesando registros");
                            //die();
                            while(! feof($file))
                            {// Procesar registro por registro del archivo, hasta llegar a su fin
                                $reg = explode("\t",fgets($file));//Obtener un registro del archivo, como un array separado por tabulacion

                                $count++;
                                /*if($count >= 16190){
                                    var_dump(count($reg));
                                }*/

                                if(count($reg) == 26 ){ // si el registro contiene 26 posiciones
                                    $sql = "SET @msg = ''";
                                    $sql1 = "SET @registro = ''";
                                    if($con->query($sql) and $con->query($sql1)){ //Si setea las variables pasamos a operar

                                        $sql = "call prueba('{$_SESSION['usuario']}', '".str_replace("'","\'",$reg[19])."', '".str_replace("'","\'",$reg[20])."','" . 
                                            str_replace("'","\'",str_replace(" ","",$reg[18])) . 
                                            "','".str_replace("'","\'",$reg[21])."','".str_replace("'","\'",$reg[22])."', '".str_replace("'","\'",$reg[23])."', '" . 
                                            str_replace(" ","",$reg[0]) .
                                            "','" . str_replace('"',"",str_replace("'","\'",$reg[12])) . "','" . 
                                            str_replace("'","\'",$reg[13]).
                                            "','".str_replace("'","\'",$reg[14]) ."','".str_replace("'","\'",$reg[15]).
                                            "','".str_replace("'","\'",$reg[16])."','".str_replace("'","\'",$reg[17])."', '" . 
                                            str_replace(" ","",$reg[24]) . "', '" . 
                                            str_replace("'","\'",str_replace(" ","",$reg[25])) . "', '" . 
                                            str_replace(" ","",$reg[1]) . "', '" . 
                                            str_replace(" ","",$reg[3]) . 
                                            "', '".str_replace("'","\'",$reg[4])."' , '{$reg[5]}' , '" . 
                                            str_replace(" ","",$reg[6]) . 
                                            "' , '".str_replace("'","\'",$reg[7])."','{$reg[8]}' ,'" . 
                                            str_replace(" ","",$reg[9]) . 
                                            "', '".str_replace("'","\'",$reg[10])."','{$reg[11]}','{$campania}','{$anio}','{$mes}','{$tabla}','" .
                                            str_replace(" ","",$reg[2]) . "',@msg, @registro,'{$idArchivo}')";

                                        if($con->query($sql)){//si el procedimiento de almacenar los datos es correcto
                                            //obtener la variable de registro
                                            $sql = " SELECT @registro as registro";
                                            if($con->query($sql)){
                                                $idArchivo = $con->get_result()[0]["registro"];
                                            }else{

                                                $estado = false;
                                                break;
                                            }

                                        }else{

                                            $estado = false;
                                            break;
                                        }
                                    }else{
                                        $estado = false;
                                        break;
                                    }
                                }

                            }//end while


                            self::mensaje('Información',"Registros procesados: " . $count);
                            if($estado){

                                $con->commit();
                                $con->autocommit(true);

                                echo '<script> $("#botonera").append("'."<a class='ui button blue' href='/ireg/agente/'>Finalizar</a>".'")</script>';
                                self::mensaje('Información',"Tiempo transcurrido: " . round((microtime(true)- ( $tiempo_inicial)) , '2'));
                                $con->terminar();
                            }else{

                                echo '<script> $("#botonera").append("'."<a class='ui button red' href='/ireg/agente/cargainformacion'>Finalizar</a>".'")</script>';
                                self::mensaje('Error',"Ha ocurrido un error codigo: " . $con->codigoError() . $con->error());
                                $con->rollback();
                                $con->autocommit(true);
                                self::mensaje('Información',"Tiempo transcurrido: " . round((microtime(true)- ( $tiempo_inicial)) , '2'));
                                $con->terminar();
                            }
                        }catch(\Exception $ex){
                            echo '<script> $("#botonera").append("'."<a class='ui button red' href='/ireg/agente/cargainformacion'>Finalizar</a>".'")</script>';
                            self::mensaje('Error',"Ha ocurrido un error codigo: " . $con->codigoError());
                            $con->rollback();
                            $con->autocommit(true);
                            self::mensaje('Información',"Tiempo transcurrido: " . round((microtime(true)- ( $tiempo_inicial)) , '2'));
                            $con->terminar();
                        }
                    }else{
                        echo '<script> $("#botonera").append("'."<a class='ui button red' href='/ireg/agente/cargainformacion'>Finalizar</a>".'")</script>';
                        self::mensaje('Información',"Tiempo transcurrido: " . round((microtime(true)- ( $tiempo_inicial)) , '2'));
                        $con->terminar();
                    }
                }

                fclose($file);
                unlink($temp_dir . $file_name);
            }else
                echo "Error al almacenar el archivo";
            //die();
        }else{
            echo '<script> $("#botonera").append("'."<a class='ui button red' href='/ireg/agente/cargainformacion'>Cargar Información</a>".'")</script>';
            self::mensaje('Información',"Tiempo transcurrido: " . round((microtime(true)- ( $tiempo_inicial)) , '2'));
            $con->terminar();
        }
        unset($con);

    }

    public function salir(){
        //Helper::peticion("POST");
        echo "salir";
        Auth::finalizar();
    }


    public static function mensaje($titulo, $mensaje){
        echo '<script> $("#mensajes").append("' ."<div class='item'><i class='comment outline icon'></i><div class='content'><h3 class='header ". ($titulo == 'Error'? 'red' : 'blue') . " ui'>{$titulo}</h3><div class='description'>{$mensaje}</div></div></div>" .'") </script>';
    }

}