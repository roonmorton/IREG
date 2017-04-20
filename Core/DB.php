<?php
namespace Core;

defined('APPPATH') or die('Acceso denegado...');


use \Core\App;
use \Core\MyException;


class DB{

    private $usuario;

    private $password;

    private $host;

    private $dbName;

    private $_conexion;

    public function __construct(){
        try{
            $config = App::getConfig();
            $this->host = $config['host'];
            $this->usuario = $config['usuario'];
            $this->password = $config['password'];
            $this->dbName = $config['database'];

            $this->_conexion = mysqli_connect(
                $this->host,
                $this->usuario,
                $this->password,
                $this->dbName
            );
            
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
        return $this->_conexion->query($sql);
    }
    
    public function prepare($sql){
        //var_dump($this->_conexion->prepare("select * from tbl"));
        return $this->_conexion->prepare($sql);
    }

}


?>