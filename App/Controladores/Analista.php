<?php
namespace App\Controladores;
defined("APPPATH") OR die("Acceso denegado..");

use Core\Vista;
use Core\App;
use Core\Auth;
use Core\Helper;
use Core\DataBase;

class Analista
{

    public function index(){


        Auth::auth();

        $con = new DataBase();

        $sql = "select tbldistribuidor.nombre as nombre, tbldistribuidor.codigo, tblpais.nombre as pais from tbldistribuidor inner join tblpais on tbldistribuidor.idPais = tblpais.idPais";
        $distribuidores = [];
        if($con->query($sql)){
            $distribuidores = $con->get_result();
        }
        $con->terminar();

        Vista::set("distribuidores",$distribuidores);
        Vista::set('titulo','Analista | Inicio');
        Vista::render('analista.index');
    }


    public function InformacionDistribuidor($codigo){
        //$app = new App();
        //var_dump($app->getParametros());
        //echo "hola: " + $id;
        Auth::auth();
        if(isset($codigo)){
            $con = new DataBase();
            $sql = 'SELECT a.idArchivo, a.fecha, a.anio, (CASE a.mes WHEN "1" THEN "Enero" WHEN "2" THEN "Febrero" WHEN "3" THEN "Marzo" WHEN "4" THEN "Abril" WHEN "5" THEN "Mayo" WHEN "6" THEN "Junio" WHEN "7" THEN "Julio" WHEN "8" THEN "Agosto" WHEN "9" THEN "Septiembre" WHEN "O" THEN "Octubre" WHEN "N" THEN "Noviembre" WHEN "D" THEN "Diciembre" END) as mes,c.nombreusuario as cuenta, d.nombre as distribuidor from tblarchivo as a inner join tblcuenta as c on a.idCuenta = c.idCuenta inner join tbldistribuidor as d on d.idDistribuidor = c.idDistribuidor where d.codigo = \''.$codigo.'\'';
            //echo $sql;
            $archivos = [];
            if($con->query($sql)){
                $archivos = $con->get_result();
            }else{
                echo "error: " . $con->error();
            }


            Vista::set("archivos",$archivos);
            Vista::set('titulo',"Analista | Datos de Agente");
            Vista::render("analista.informacion_agente");
        }else{
            Helper::ir("analista");
        }
    }

    public function descargarinformacion($id){
        Auth::auth();

        if(isset($id)){
            $temp_dir = "temp/";
            $estado = true;
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

            try{
                $con = new DataBase();
                $sql = "SELECT concat(tbldistribuidor.codigo, tblarchivo.calidadServicio, tblarchivo.anio,tblarchivo.mes,tblarchivo.nombreTabla) as archivo from tblarchivo inner join tblcuenta on tblarchivo.idCuenta = tblcuenta.idCuenta inner join tbldistribuidor on tbldistribuidor.idDistribuidor = tblcuenta.idDistribuidor where tblarchivo.idArchivo = {$id}";
                $file_name = "";
                //crear archivo
                if($con->query($sql)){
                    $res = $con->get_result();
                    $file_name = $temp_dir . $res[0]["archivo"] . rand() . ".txt";
                    $file = fopen($file_name,"w");
                    foreach($encabezados as $encabezado){
                        fwrite($file,strtoupper($encabezado) . "\t");
                    }
                    //fwrite($file,"\n");
                }

                //die();

                $sql = "SELECT usuario.idUsuario, tarifa.siglas as Tarifa, registro.tipoRegistro as TIPOREGISTRO, (select tblmedidor.descripcion from tblmedidor inner join tblregistrohasmedidor on tblregistrohasmedidor.idMedidor = tblmedidor.idMedidor where tblregistrohasmedidor.idRegistro = registro.idRegistro LIMIT 0,1) as NroMedidor1, (select tblmedidor.descripcion from tblmedidor inner join tblregistrohasmedidor on tblregistrohasmedidor.idMedidor = tblmedidor.idMedidor where tblregistrohasmedidor.idRegistro = registro.idRegistro LIMIT 0,1) as TipoMedidor1, (select tblRegistroHasMedidor.fechaColocacion from tblmedidor inner join tblregistrohasmedidor on tblregistrohasmedidor.idMedidor = tblmedidor.idMedidor where tblregistrohasmedidor.idRegistro = registro.idRegistro LIMIT 0,1) as FechaColocacion1, (select tblmedidor.descripcion from tblmedidor inner join tblregistrohasmedidor on tblregistrohasmedidor.idMedidor = tblmedidor.idMedidor where tblregistrohasmedidor.idRegistro = registro.idRegistro LIMIT 1,1) as NroMedidor2, (select tblmedidor.descripcion from tblmedidor inner join tblregistrohasmedidor on tblregistrohasmedidor.idMedidor = tblmedidor.idMedidor where tblregistrohasmedidor.idRegistro = registro.idRegistro LIMIT 1,1) as TipoMedidor2, (select  tblRegistroHasMedidor.fechaColocacion from tblmedidor inner join tblregistrohasmedidor on tblregistrohasmedidor.idMedidor = tblmedidor.idMedidor where tblregistrohasmedidor.idRegistro = registro.idRegistro LIMIT 1,1) as FechaColocacion2, (select tblmedidor.descripcion from tblmedidor inner join tblregistrohasmedidor on tblregistrohasmedidor.idMedidor = tblmedidor.idMedidor where tblregistrohasmedidor.idRegistro = registro.idRegistro LIMIT 2,1) as NroMedidor3, (select tblmedidor.descripcion from tblmedidor inner join tblregistrohasmedidor on tblregistrohasmedidor.idMedidor = tblmedidor.idMedidor where tblregistrohasmedidor.idRegistro = registro.idRegistro LIMIT 2,1) as TipoMedidor3, (select  tblRegistroHasMedidor.fechaColocacion from tblmedidor inner join tblregistrohasmedidor on tblregistrohasmedidor.idMedidor = tblmedidor.idMedidor where tblregistrohasmedidor.idRegistro = registro.idRegistro LIMIT 2,1) as FechaColocacion3, usuario.nombres as NOMBRE, usuario.calle , usuario.numero, usuario.piso, usuario.unidad, usuario.telefono, municipio.codigoPostal, departamento.nombre as departamento, municipio.nombre as municipio, aldea.nombre as aldea, cantonZona.nombre as canton, caserio.nombre as CASERIO, potencia.cantidad as potencia, facturacion.codigo as PLANFACTURACION FROM tblarchivo as archivo INNER JOIN tblregistro AS registro ON archivo.idArchivo = registro.idArchivo INNER JOIN tblTarifa as tarifa ON tarifa.idTarifa = registro.idTarifa INNER JOIN tblplanfacturacion facturacion ON registro.idPlanFacturacion = facturacion.idPlanFacturacion INNER JOIN tblpotencia as potencia ON registro.idPotencia = potencia.idPotencia INNER JOIN tblusuario AS usuario ON registro.idUsuario = usuario.idU INNER JOIN tblmunicipiohasaldea as munAldea ON usuario.idMunicipioHasAldea = munAldea.idMunicipioHasAldea INNER JOIN tblcantonzona As cantonZona ON munaldea.idCantonZona = cantonZona.idCantonZona INNER JOIN tblcaserio as caserio ON munaldea.idCaserio = caserio.idcaserio INNER JOIN tblaldea as aldea  ON munaldea.idAldea = aldea.idAldea INNER JOIN tblmunicipio as municipio ON munaldea.idMunicipio = municipio.idMunicipio INNER JOIN tbldepartamento AS departamento ON municipio.idDepartamento = departamento.idDepartamento WHERE archivo.idArchivo = {$id} order by usuario.idUsuario ASC";



                //echo $sql;
                // die();
                if($con->query($sql)){

                    $registros = $con->get_result();
                    fwrite($file,"\r\n");
                    foreach($registros as $registro){
                        
                        fwrite($file, $registro["idUsuario"]."\t");
                        fwrite($file,$registro["Tarifa"]."\t");
                        fwrite($file,$registro["TIPOREGISTRO"]."\t");
                        fwrite($file,$registro["NroMedidor1"]."\t");
                        fwrite($file,$registro["TipoMedidor1"]."\t");
                        //var_dump((int) date("Y",strtotime("2019-00-00 00:00:00")));die();
                        fwrite($file, $registro["FechaColocacion1"]   ."\t");
                        fwrite($file,$registro["NroMedidor2"]."\t");
                        fwrite($file,$registro["TipoMedidor2"]."\t");
                        fwrite($file,$registro["FechaColocacion2"]."\t");
                        fwrite($file,$registro["NroMedidor3"]."\t");
                        fwrite($file,$registro["TipoMedidor3"]."\t");
                        fwrite($file,$registro["FechaColocacion3"]."\t");
                        fwrite($file,$registro["NOMBRE"]."\t");
                        fwrite($file,$registro["calle"]."\t");
                        fwrite($file,$registro["numero"]."\t");
                        fwrite($file,$registro["piso"]."\t");
                        fwrite($file,$registro["unidad"]."\t");
                        fwrite($file,$registro["telefono"]."\t");
                        fwrite($file,$registro["codigoPostal"]."\t");
                        fwrite($file,$registro["departamento"]."\t");
                        fwrite($file,$registro["municipio"]."\t");
                        fwrite($file,$registro["aldea"]."\t");
                        fwrite($file,$registro["canton"]."\t");
                        fwrite($file,$registro["CASERIO"]."\t");
                        fwrite($file,$registro["potencia"]."\t");
                        fwrite($file,$registro["PLANFACTURACION"]);
                    }
                    $estado = true;

                }else{
                    echo "ha ocurrido un error";
                    $estado = false;
                    $con->terminar();
                }
            }catch(\Exception $ex){
                MyException::Mensaje($ex);
            }finally{
                fclose($file);
                $con->terminar();
            }

            if($estado){
               // echo "<script> $('.small.modal').modal('hide');</script>";
                Helper::downFile($file_name);
            }
        }
    }

}