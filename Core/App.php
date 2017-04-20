<?php
namespace Core;
defined('APPPATH') or die('Acceso denegado.');

class App{

    private $_controlador;

    private $_metodo;

    private $_parametros = [];

    const NAMESPACE_CONTROLADORES = '\App\Controladores\\';

    const DIRECTORIO_CONTROLADORES = 'App/Controladores/';

    private $tipo_peticion;

    
    private $files = [];
    
    public function __construct(){
        #Url Parseada
        $url = $this->parseUrl();
        //print_r(dirname(__DIR__) . self::DIRECTORIO_CONTROLADORES . ucfirst($url[0]) . '.php');
        //Comprobar que existe archivo en el directorio de controladores
        if(file_exists(self::DIRECTORIO_CONTROLADORES . ucfirst($url[0]) . '.php')){
            //nombre archivo a llamar
            $this->_controlador = ucfirst($url[0]);
            //eliminamos controlador de url, asi solo quedan los parametros del metodo
            unset($url[0]);
        }else{
            include APPPATH . '/Vistas/errores/404.php';
            exit;
        }
        //si existe el segundo segmento corresponde al metodo
        //comprobamos que el motodo exista dentro del controlador
        if(isset($url[1]))
        {
            //aqui obtenemos el metodo
            $this->_metodo = $url[1];
        }else{
            //El metodo por defecto sera inicio
            $this->_metodo = 'index';
        }

        //obtenemos clase con su espacio de nombres
        $full_class = self::NAMESPACE_CONTROLADORES . $this->_controlador;
        //echo $full_class;
        //Asociamos la instancia al controlador
        $this->_controlador = new $full_class;

        //Comprobamos que exista el metodo en el controlador
        if(method_exists($this->_controlador,$this->_metodo)){
            //eliminamos el metodo
            unset($url[1]);
        }else{
            //Metodo no existe tratarlo
            \Core\MyException::Mensaje(new \Exception("Error Procesando Metodo: $this->_metodo , no existe en controlador: " . get_class($this->_controlador), 1));
        }




        //asocuamos el resto de segmentos correspondiente a los parametros por defecto sera un array vacio
        $this->_parametros = $url ? array_values($url) : [];
    }

    public function parseUrl(){
        $this->tipo_peticion = $_SERVER['REQUEST_METHOD'];
        //var_dump($_GET);
        //var_dump($_POST);
        $this->files = $_FILES;
        if(isset($_GET)){
            return $_GET ? explode('/',filter_var(rtrim($_GET['url'],'/'),FILTER_SANITIZE_URL)): [CONTROLADOR_DEFECTO];
        }
    }

    public function iniciar(){
        call_user_func_array([$this->_controlador,$this->_metodo],$this->_parametros);
    }

    public function getControlador(){
        return $this->_controlador;
    }

    public function getMetodo(){
        return $this->_metodo;
    }

    public function getParametros(){
        return $this->_parametros;
    }

    public function getConfig(){
        return parse_ini_file(APPPATH . '/config.ini');
    }
    
    public function get_tipo_peticion(){
        return $this->tipo_peticion;
    }
    
    public function get_files(){
        return $this->files;
    }
}