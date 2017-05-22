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
        Helper::peticion("POST"); //Aceptar peticiones POST
        Auth::auth(); // Verificar si hay usuario Logeado

        $temp_dir = 'temp/'; // Directorio temporal
        $errores = []; // Si existen errores
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
        
        if(strtolower($ext) != "txt")
        {
            array_push($errores, 'Extencion no soportada "'.$ext.'"');
            $estado = false;
        }
        $con = new DataBase();
        $sql = "select COUNT(1) as contador from tblDistribuidor INNER JOIN tblCuenta ON tblDistribuidor.idDistribuidor = tblCuenta.idDistribuidor where tblCuenta.nombreUsuario = '" . USUARIO ."' and tblDistribuidor.codigo = '{$dist}'";
        unset($dist);
        unset($ext);
        $con->query($sql);
        if($con->get_result()[0]["contador"] == 0)
        {
            array_push($errores, 'Indentificación de distribuidor no corresponde a su usuario');
            $estado = false;
        }
        if($campania != "C")
        {
            array_push($errores,"Código de campaña no es valido");
            $estado = false;
        }
        if($anio > (idate('y')))
        {
            array_push($errores, "No se puede cargar un archivo de un año superior");
            $estado = false;
        }
        $meses = [1,2,3,4,5,6,7,8,9,"O","N","D"];
        if(!in_array($mes,$meses))
        {
            array_push($errores,"Identificador de mes es invalido");
            $estado = false;
        }
        if($estado)
        {
            // echo "Nombre archivo Correcto";
            //var_dump($file_req);
            //var_dump($file_req);
            //die();
            $file_name = getdate()[0] . $file_name . ".txt";

            if(copy($file_req["tmp_name"], $temp_dir . $file_name))
            {// Si se almacena el archivo se procesa
                $file = fopen($temp_dir . $file_name, "r"); //Abrir archivo
                $cols = explode("\t",fgets($file)); //obtener la fila del encabezado
                if(count($cols) != 26)
                {//si las columnas no son 26 se lanza error
                    array_push($errores, "Columnas de encabezado deben de ser 26 no ". count($cols));
                    $estado = false;
                }
                else
                {
                    //procesar columnas de encabezado 
                    $encabezados = [
                        "IDUsuario", // 0
                        "Tarifa", // 1 
                        "TipoRegistro", // 2
                        "NroMedidor1", // 3
                        "TipoMedidor1", // 4
                        "FechaColocacion1", // 5
                        "NroMedidor2", // 6
                        "TipoMedidor2", // 7
                        "FechaColocacion2", // 8
                        "NroMedidor3", // 9
                        "TipoMedidor3", // 10
                        "FechaColocacion3", // 11
                        "Nombre", // 12
                        "Calle", // 13
                        "Numero", //14
                        "Piso", // 15
                        "Unidad", // 16
                        "Telefono", // 17
                        "CodigoPostal", // 18
                        "Departamento", // 19 
                        "Municipio",// 20
                        "Aldea", // 21 
                        "Canton",// 22
                        "Caserio", // 23
                        "Potencia", // 24
                        "PlanFacturacion" // 25
                    ];
                    
                    foreach($cols as $col){
                        if(!in_array(trim($col),$encabezados)){
                            array_push($errores,"Columna no valida: {$col}");
                            $estado = false;
                        }
                    }
                    
                    if($estado){
                        // Si las columnas de encabezado estan correctas
                        // se procesara las filas del archivo
                        // realizar una transaccion para deshacer los cambios por si surje un error;
                        $con->autocommit(false);
                        try{
                            while(! feof($file))
                            {// Procesar registro por registro del archivo, hasta llegar a su fin
                                $reg = explode("\t",fgets($file));//Obtener un registro del archivo, como un array separado por tabulacion
                                $ids = array(); // Alamacenar ids
                              
                                //Obtener el pais del usuario del sistema
                                $sql = "SELECT pais.idPais from tblPais as pais INNER JOIN tblDistribuidor as distribuidor ON pais.idPais = distribuidor.idPais INNER JOIN TblCuenta as cuenta ON cuenta.idDistribuidor = distribuidor.idDistribuidor WHERE cuenta.nombreUsuario = '{$_SESSION['usuario']}'";
                                $con->query($sql);
                                $pais = $con->get_result()[0];

                                if(count($pais) == 0)
                                { // si no existe un pais
                                    array_push($errores, "Error al buscar el pais del usuario, contactar soporte...");
                                    $estado = false;
                                    break;
                                }
                                else
                                {// si se encuentra el pais continua
                                    //var_dump($reg[19]);


                                    $ids["idPais"] = $pais["idPais"]; //Alamacenar el id del pais
                                    unset($pais); //varciar Pais

                                    //SQl para buscar si existe un departamento
                                    $sql = "SELECT * FROM TblDepartamento WHERE nombre = '{$reg[19]}' and idPais = {$ids["idPais"]}";
                                    echo $sql ."</br>";
                                    if($con->query($sql))
                                    {// si la consulta no da error

                                        $depto = $con->get_result();
                                        if(count($depto) > 0)
                                        {//si existe el departamento no lo insertamos
                                            $ids["idDepartamento"] = $depto[0]["idDepartamento"];
                                            unset($depto);
                                            //var_dump($ids);
                                            echo "departamento ya existe </br>";
                                        }
                                        else
                                        {//si no existe el departamento lo insertamos
                                            unset($depto);
                                            $sql = "INSERT INTO  TblDepartamento(nombre,idPais) VALUES('{$reg[19]}',{$ids["idPais"]})";
                                            echo $sql ."</br>";
                                            if($con->query($sql))
                                            { // si no da error la insercion del departamento
                                                $ids["idDepartamento"] = $con->ultimo_id();
                                                //var_dump($con->ultimo_id());
                                                $con->commit();
                                                var_dump($con->ultimo_id());
                                                echo "departamento insertado </br>";
                                                //die();
                                            }
                                            else
                                            { //si se produce error al insertar en departamento
                                                $estado = false;
                                                echo $con->error();
                                                $con->rollback();
                                                $con->autocommit(true);
                                                $con->terminar();
                                            }
                                        }

                                        if($estado)
                                        {
                                            // SQL para buscar municipio
                                            $sql = "SELECT * FROM tblMunicipio WHERE nombre = '{$reg[20]}' and codigoPostal " . ($reg[18] == "" ? " is null" : "= " . $reg[18]) . " and idDepartamento = {$ids["idDepartamento"]}";
                                            echo $sql ."</br>";
                                            if($con->query($sql))// si no da error verifica si existe el municipio
                                            {
                                                $municipio = $con->get_result();
                                                if(count($municipio))
                                                {//si existe le municipio
                                                    //var_dump($municipio);
                                                    $ids["idMunicipio"] = $municipio[0]["idMunicipio"];
                                                    unset($municipio);
                                                    echo "municipio ya existe</br>";

                                                }
                                                else
                                                {//si no existe el municipio
                                                    $sql = "INSERT INTO TblMunicipio(nombre,idDepartamento,codigoPostal) VALUES('{$reg[20]}',{$ids['idDepartamento']}, " . ($reg[18] == "" ? "null" : $reg[18]) . ")";
                                                    echo $sql ."</br>";

                                                    if($con->query($sql))
                                                    {//si no ocurre error en la consulta
                                                        $ids["idMunicipio"] = $con->ultimo_id();
                                                        echo "municipio agregado </br>";
                                                    }
                                                    else
                                                    {//Si hubo un error volver todo
                                                        $estado = false;
                                                        echo $con->error();
                                                        $con->rollback();
                                                        $con->autocommit(true);
                                                        $con->terminar();
                                                    }
                                                }
                                            }
                                            else//si se produce error volver todo a la normalidad
                                            {
                                                $estado = false;
                                                echo $con->error();
                                                $con->rollback();
                                                $con->autocommit(true);
                                                $con->terminar();
                                            }
                                            
                                            if($estado)
                                            {
                                            
                                                // se inserta departamento 
                                                // y se inserta municipio y codigo postal
                                                // contianuar aqui
                                                
                                            }
                                            
                                            
                                        }
                                        var_dump($ids);


                                    }
                                    else
                                    {//Error al buscar departamento
                                        $estado = false;
                                        $con->rollback();
                                        $con->autocommit(true);
                                        $con->terminar();
                                    }

                                    //SQL para insertar departamento

                                    //echo $sql;
                                    //var_dump($pais["idPais"]);
                                }

                                break;


                            }
                            if($estado){
                                $con->commit();
                                $con->autocommit(true);
                                $con->terminar();
                            }else{
                                $con->rollback();
                                $con->autocommit(true);
                                $con->terminar();
                            }
                        }catch(\Exception $ex){
                            $con->rollback();
                            $con->autocommit(true);
                            $con->terminar();
                        }
                    }
                }
                //var_dump($errores);

                fclose($file);
                unlink($temp_dir . $file_name);
            }else
                echo "Error al almacenar el archivo";
            die();
        }

        //$con->terminar();
        unset($con);


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