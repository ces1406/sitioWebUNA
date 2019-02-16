<?php
//namespace Vistas;

class Vista
{
    private $cabecera;
    private $cuerpo;
    private $pie;
    private $pagina;
    
    public function setCuerpo($cuerpo){
        $this->cuerpo = DIR_RAIZ.DIR_VISTA.DIR_HTMLS.'/cuerpo'.$cuerpo;
    }
    
    public function __construct($usuario,$cuerpo,$pie){
        if($usuario->getRol() == ROL_INV){
            $this->cabecera = DIR_RAIZ.DIR_VISTA.DIR_HTMLS.'/cabeceraSinUsuario';
        }else {
            $this->cabecera = DIR_RAIZ.DIR_VISTA.DIR_HTMLS.'/cabeceraConUsuario';            
        }
        $this->cuerpo = DIR_RAIZ.DIR_VISTA.DIR_HTMLS.'/cuerpo'.$cuerpo;
        $this->pie = DIR_RAIZ.DIR_VISTA.DIR_HTMLS.'/pie'.$pie;
    }
    
    public function armarHtml() {
        $this->pagina = file_get_contents(DIR_RAIZ.DIR_VISTA.DIR_HTMLS.'/plantilla.php');
        $this->pagina = str_replace('{cabecera}',file_get_contents($this->cabecera), $this->pagina);
        $this->pagina = str_replace('{cuerpo}', file_get_contents($this->cuerpo), $this->pagina);
        $this->pagina = str_replace('{pie}', file_get_contents($this->pie), $this->pagina);
    }
    
    public function renderizar(){
        eval('?>'.$this->pagina);//.'<?php');
    }

    public function modificarCuerpo($clave,$valor) {
        $this->pagina = str_replace($clave,$valor, $this->pagina);
    }
    
}

?>