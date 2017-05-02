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

    //private static $_inst;
    private $statement;

    private $conexion;
    
    private $result;

    public function __construct(){
        
        mysqli_report(MYSQLI_REPORT_STRICT);
        try{
            $config = App::getConfig();
            $this->host = $config['host'];
            $this->usuario = $config['usuario'];
            $this->password = $config['password'];
            $this->dbName = $config['database'];

            $this->conexion = new \mysqli(
                $this->host,
                $this->usuario,
                $this->password,
                $this->dbName
            );
            $this->conexion->set_charset("utf8");

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
        $this->result = $this->conexion->query($sql);
        if($this->result){
            //var_dump($this->result);
            return $this->result;
            //$this->statement->execute();
            //$this->result = $this->statement->get_result();
        }else{
            MyException::Mensaje(
                new \Exception("Error en la consulta '$sql'",1)
            );
        }
    }

    public function get_result($result = null){
        //var_dump(get_class_methods($result));
        $res = array();
        if($result != null)
            $this->result = $result;
        
        while($row = $this->result->fetch_assoc()){
            $res[] = $row;
        }
        $this->result->free();
        return $res;        
    }
    
    public function prepare($sql){
        $this->statement = $this->conexion->prepare($sql);
        if($this->statement){
            return  $this->statement;            
        }else{
            MyException::Mensaje(
                new \Exception($this->_conexion->error,$this->_conexion->errno)
            );
        }
    }

    
   
    
   /* 
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
    */
    public function terminar(){
        if(isset($this->conexion)){
            if(isset($this->statement))
                $this->statement->close();
            $this->conexion->close();
            //unset($this);
        }
    }
}

?>