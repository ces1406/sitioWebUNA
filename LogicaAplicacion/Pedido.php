<?php
//namespace LogicaAplicacion;

class Pedido
{
    private $nombreControlador;
    private $metodo;
    private $argumentos=array();
    
    public function getMetodo(){
        return $this->metodo;
    }
    
    public function setMetodo($metodo) {
        $this->metodo=$metodo;
    }
    
    public function getArgumentos(){
        return $this->argumentos;
    }
    
    public function getNombreControlador(){
        return $this->nombreControlador;
    }
    
    public function __construct(){
        $url = filter_input(INPUT_GET,'args',FILTER_SANITIZE_URL);
        $url = explode('/',$url);
        $this->argumentos = array_slice($url,2);
        
        if (!isset($url[0])|| ($url[0] == "") || ($url[0] == null) ) {
            $this->nombreControlador='ControladorPorDefecto';
        }else{
            $this->nombreControlador ='Controlador'.$url[0];
        }
        
        if( !isset($url[1])|| ($url[1] == null)|| (empty($url[1]))){
            $this->metodo="metodoPorDefecto";
        }else{
            $this->metodo = 'metodo'.$url[1];
        }
    }
    
    public function existeControlador() {
        if(is_readable(DIR_RAIZ.DIR_CONTROLADOR.'/'.$this->getNombreControlador().'.php')){
            return TRUE;
        }else{
            return FALSE;
        }
    }
    
    public function existeMetodo(){
        if(is_callable(array($this->getNombreControlador(),$this->getMetodo()))){
            return TRUE;
        }else{
            return FALSE;
        }
    }
}

?>