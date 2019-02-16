<?php
//namespace Vistas;
class Vista{
    
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

    public function crearAreaComentaje($dirImg){
        return '
            <div class="media">
                <div class="media-left">
                    <img src="/Vistas/imagenesUsers/'.$dirImg.'" class="mr-3 comentarista media-object" >
                </div>
                <div class="media-body">
                    <form class="form-horizontal" id="crearComentario" {action} method="POST">
				        <div class="form-group row" id="formularioRegistro">
					        <div id="comentarioActual">
							    <textarea name="unComentario" id="area1"></textarea>
						    </div>
					    </div>
					    <button class="btn btn-secondary btn-sm enlace" type="submit" id="CrearComentario">COmentar</button>
                    </form>
                </div>
            </div>
            <img src="/Vistas/imagenes/separador.png" class="separador">';
    }

    public function crearComentario($dirImg,$apodo,$fecha,$contenido){
        return '<div class="media">
                    <div class="media-left">
                        <img src="/Vistas/imagenesUsers/'.$dirImg.'" class="comentarista media-object" >
                    </div>
                    <div class="media-body">
                        <h3 class="media-heading">'.htmlentities($apodo).'</h3>
                        <small class="text-muted">(posteado el '.$fecha.')</small>
                        <p>Dijo:<br> '.$contenido.'</p>
                    </div>
                </div>
                <img src="/Vistas/imagenes/separador.png" class="separador"><br/>';
    }
    
    public function crearTituloTemaCurso($mate,$catedra,$sede,$hora,$cod){
        return '<h2>Materia: '.$mate.'<br/>cátedra: '.$catedra.'<br/>sede: '.$sede.'<br/>horario: '
                .$hora.'<br/>curso: '.$cod.'</h2>';
    }

    public function crearLiTema($idTema,$titulo){
        return '<li class="nav-item li0 enlace "><img src="/Vistas/imagenes/item7.png" width="40" height="40"/>
                <a href="/Seccion/IrTema/'.$idTema.'/1" >'.htmlentities($titulo).'</a>	</li>';
    }

    public function crearBotonIniciarTema(){
        return '<a  href="/Seccion/IniciarTema/{idSeccion}/{nombreSeccion}" class="btn btn-secondary enlace" 
                id="botonIniciar">Iniciar un tema</a>';
    }

    public function renderizar(){
        eval('?>'.$this->pagina);
    }

    public function modificarCuerpo($clave,$valor) {
        $this->pagina = str_replace($clave,$valor, $this->pagina);
    }

    public function agregarHoraYMins(){
        for ($i=7; $i <24 ; $i++) { $hor.='<option>'.str_pad((int)$i,2,"0",STR_PAD_LEFT).'</option>';}
        for ($i=0; $i <12 ; $i++) { $min .='<option>'.str_pad((int)($i*5),2,"0",STR_PAD_LEFT).'</option>';}
        $this->modificarCuerpo('{hora}',$hor);
        $this->modificarCuerpo('{minuto}',$min);   
    }
    
}

?>