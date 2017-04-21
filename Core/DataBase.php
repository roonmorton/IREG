<?php
namespace Core;

defined('APPPATH') or die('Acceso denegado...');


use \Core\App;
use \Core\MyException;


class DataBase{

    private $usuario;

    private $password;

    private $host;

    private $dbName;

    private static $_inst;

    private static $_conexion;

    public function __construct(){
        
        mysqli_report(MYSQLI_REPORT_STRICT);
        try{
            $config = App::getConfig();
            $this->host = $config['host'];
            $this->usuario = $config['usuario'];
            $this->password = $config['password'];
            $this->dbName = $config['database'];

            $this->_conexion = new \mysqli(
                $this->host,
                $this->usuario,
                $this->password,
                $this->dbName
            );
            $this->_conexion->set_charset("utf8");

            //$this->_conexion->exec('SET CHARACTER SET utf8');
        }catch(\Exception $ex ){
            //throw $e;
            //var_dump($ex);

            //var_dump(get_class_methods($ex->getTrace()));
            //var_dump(get_class_methods(MyException));
            MyException::Mensaje($ex);
            //var_dump($ex->getPrevious());
            //echo "Error: " . $ex->getMessage() . "linea: " .$ex->getLine() . "archivo: " . $ex->getFile() . "codigo: " . $ex->getCode() //"trace: " .$ex->getTrace() ."prev: " . $ex->getPrevious() ;
        }
    }

    public function query($sql){
        $result = $this->_conexion->query($sql);
        if($result){
            return  $result;            
        }else{
            MyException::Mensaje(
                new \Exception("Error en la consulta '$sql'",1)
            );
        }
    }

    public function get_all($result){
        //var_dump(get_class_methods($result));
        $res = array();
        while($row = $result->fetch_assoc()){
            $res[] = $row;
        }
        $result->free();
        return $res;        
    }
    
    public function prepare($sql){
        
        if($this->_stm = $this->_conexion->prepare($sql)){
            return  $this->_stm;            
        }else{
            MyException::Mensaje(
                new \Exception($this->_conexion->error,$this->_conexion->errno)
            );
        }
    }

    
    public static function instancia(){
        if(!isset(self::$_inst)){
            $clase = __CLASS__;
            self::$_inst = new $clase;
        }
        return self::$_inst;
    }

    public function __clone(){
        trigger_error('La clonacion de este objeto no esta permitida',E_USER_ERROR);
    }
    
    public function terminar(){
        if(isset($this->_conexion))
            $this->_conexion->close();
    }
}

?>