<?php
require_once DIR_RAIZ.DIR_CONTROLADOR.'/ControladorPorDefecto.php';
require_once DIR_RAIZ.DIR_APP.'/Seccion.php';
require_once DIR_RAIZ.DIR_APP.'/Paginacion.php';
require_once DIR_RAIZ.DIR_APP.'/Materias.php';

class ControladorSeccion extends ControladorPorDefecto{
    private $seccion;

    public function __construct(){
        parent:: __construct();
        $this->seccion = new Seccion();
    }
    
    public function metodoIrSeccion($nombreSeccion,$idSeccion,$pagina) {
        $vecSeccion=Modelo::buscarSeccion($idSeccion);
        if(empty($vecSeccion)) return $this->msjAtencion('no existe la página solicitada');
        $this->seccion->setNombre($vecSeccion["nombreSeccion"]);
        $this->seccion->setId($idSeccion);
        $this->seccion->autoCompletarse();
        $temas = $this->seccion->getTemas();
        $cantTemas= $this->seccion->cantTemas();
        $listaTemas=null;
        $vecTemas=array_slice($temas,($pagina-1)*CANT_TEMAS,CANT_TEMAS,true);
        if(!empty($vecTemas)){                          // Listando los temas de la seccion
            foreach ($vecTemas as $tema){
                $listaTemas .= $this->getVista()->crearLiTema($tema->getIdTema(),$tema->getTitulo());
            }
        }
        $unHref='<li class="page-item"><a class="page-link" href="/Seccion/irSeccion/'.$nombreSeccion.'/'.$idSeccion.'/';
        $botones = new Paginacion($pagina,$cantTemas,$unHref);
        $this->getVista()->armarHtml();
        $this->setearPanelDerecho();
        $this->getVista()->modificarCuerpo('{colDer}','5');
        $this->getVista()->modificarCuerpo('{sectorIzquierda}',file_get_contents(DIR_RAIZ.DIR_VISTA.DIR_HTMLS.'/cuerpoSeccion'));
        $this->getVista()->modificarCuerpo('{colIzq}','7');
        $this->getVista()->modificarCuerpo('{scriptJs}','/Vistas/js/indexjs.js');
        $this->getVista()->modificarCuerpo('{archCss}','/Vistas/css/index.css');
        $this->getVista()->modificarCuerpo('{pieIzq}','Temas de la seccion');
        $this->getVista()->modificarCuerpo('{listaTemas}',$listaTemas);
        $this->getVista()->modificarCuerpo('{paginacion}',$botones->getBotonera());
        $this->getVista()->modificarCuerpo('{tieneSesion}',($this->getUsuario()->tieneSesion())? $this->getVista()->crearBotonIniciarTema():'');
        $this->getVista()->modificarCuerpo('{nombreSeccion}',$this->seccion->getNombre());
        $this->getVista()->modificarCuerpo('{idSeccion}',$this->seccion->getId());
        return $this->getVista();
    }
   
    public function metodoIniciarTema($idSeccion,$nombreSeccion) {
        $this->getVista()->armarHtml();
        $this->getVista()->modificarCuerpo('{sectorIzquierda}',file_get_contents(DIR_RAIZ.DIR_VISTA.DIR_HTMLS.'/cuerpoCrearTema'));
        $this->getVista()->modificarCuerpo('{sectorDerecha}','');
        $this->getVista()->modificarCuerpo('{idSeccion}',$idSeccion);
        $this->getVista()->modificarCuerpo('{nombreSeccion}',$nombreSeccion);
        $this->getVista()->modificarCuerpo('{scriptJs}','/Vistas/js/temas.js');//ya que no tiene que aparecer "ultimos comentarios"
        $this->getVista()->modificarCuerpo('{archCss}','/Vistas/css/index.css');
        return $this->getVista();
    }
    
    public function metodoCrearTemaUna($idSeccion,$nombreSeccion) {
        $vecData = array('idTema'=> null,'titulo'=>$_POST['unTitulo'],'idUsuario'=>$_SESSION['idUsuario'],
            'comentarioInicial'=>$_POST['unComentarioInicial'],'palabraClave1'=>$_POST['unaPalabra1'],
            'palabraClave2'=>$_POST['unaPalabra2'],'palabraClave3'=>$_POST['unaPalabra3']);
        $tema = new Tema($vecData);
        $tema->crearTema($idSeccion);
        // se regresa a la seccion (opcion: regresar al tema creado)
        header('Location:http://'.DOMINIO.'/Seccion/IrSeccion/'.$nombreSeccion.'/'.$idSeccion.'/1');
    }
    
    public function metodoCurso($idCurso,$pagina) {
        $this->getVista()->armarHtml();
        $this->getVista()->modificarCuerpo('{sectorDerecha}','');
        $this->getVista()->modificarCuerpo('{sectorIzquierda}',file_get_contents(DIR_RAIZ.DIR_VISTA.DIR_HTMLS.'/cuerpoUnCurso'));
        $this->getVista()->modificarCuerpo('{colIzq}','12');
        $curso=Modelo::buscarUnCurso($idCurso);
        $this->getVista()->modificarCuerpo('{curso}',$this->getVista()->crearTituloTemaCurso($curso['nombreMateria'],$curso['nombreCatedra'],
                                                                                        $curso['sede'],$curso['horario'],$curso['codigo']));
        $vecComentarios = Modelo::buscarComentsCurso($idCurso);
        $cantComents=count($vecComentarios);
        $listaComents=null;
        $vecComents=array_slice($vecComentarios, ($pagina-1)*CANT_COMENTS,CANT_COMENTS,true);
        
        $unHref='<li class="page-item"><a class="page-link" href="/Seccion/Curso/'.$idCurso.'/';
        $botones = new Paginacion($pagina,$cantComents,$unHref);
        // listando los comentarios
        foreach ($vecComents as $comentario){
            $usuario = Modelo::buscarUsuario($comentario["idUsuario"]);
            $listaComents .= $this->getVista()->crearComentario($usuario["dirImg"],$usuario["apodo"],$comentario["fechaHora"],$comentario["contenido"]);
        }
        $this->getVista()->modificarCuerpo('{scriptJs}','/Vistas/js/temas.js');
        $this->getVista()->modificarCuerpo('{archCss}','/Vistas/css/temas.css');
        $this->getVista()->modificarCuerpo('{pieIzq}','comentarios de usuarios');
        $this->getVista()->modificarCuerpo('{listaComentarios}',$listaComents);
        $this->getVista()->modificarCuerpo('{paginacion}',$botones->getBotonera());
        $this->getVista()->modificarCuerpo('{tieneSesion}',($this->getUsuario()->tieneSesion())?$this->getVista()->CrearAreaComentaje($_SESSION["img"]):'');
        $this->getVista()->modificarCuerpo('{action}','action="/Seccion/ComentarCurso/'.$idCurso.'"');
        return $this->getVista();
    }
    
    public function metodoCursosCatedras($param){//,$nombreDeSeccio,$idSeccion,$pagina) {
        $listaCursos=null;
        $busqueda=null;
        if($param=='upload'){
            // Sanitizando y validando
            $catedra=trim($_POST['unaCatedra']);
            $codigo=trim($_POST['unCodigo']);
            $materia=trim($_POST['unaMateria']);
            $sede=trim($_POST['unaSede']);

            if(empty($catedra)||strlen($catedra)>25 ||!is_string($catedra))             return $this->msjAtencion('error en la catedra ingresada');
            if(strlen($codigo)>10||!is_string($codigo))                                 return $this->msjAtencion('error en el codigo ingresado');
            if(empty($materia)||strlen($materia)>TAM_MATERIA_MAX||!is_string($materia)) return $this->msjAtencion('error en la materia ingresada');
            if(empty($sede)||strlen($sede)>13||!is_string($sede))                       return $this->msjAtencion('error en la sede ingresada');
          
            $horario = $_POST['horaInicio'].':'.$_POST['minInicio'].' a '.$_POST['horaFin'].':'.$_POST['minFin'];
            $cursoRepe=Modelo::buscarCursoRepe($materia,$catedra,$sede,$codigo,$horario);
            if(count($cursoRepe)!=0){
                return $this->msjAtencion('Ya existe un curso<br/>Materia: '.$materia.'&nbsp;Catedra: '.$catedra.'&nbsp;Sede: '
                                            .$sede.'&nbsp;Codigo: '.$codigo.'&nbsp;Horario: '.$horario);
            }
            Modelo::cargarCurso($materia,$sede,$catedra,$horario,$codigo);
            $curso = Modelo::buscarCurso($materia,$catedra,$sede,$codigo,$horario);
            header('Location:http://'.DOMINIO.'/Seccion/Curso/'.$curso[0]['idCurso'].'/1');
        }elseif($param=='search'){
            // Sanitizando y validando
            $catedra=trim($_POST['unaCatedra']);
            $codigo=trim($_POST['unCodigo']);
            $materia=trim($_POST['unaMateria']);
            $sede=trim($_POST['unaSede']);
            if(strlen($catedra)>25 ||!is_string($catedra))                  return $this->msjAtencion('error en la catedra ingresada');
            if(strlen($codigo)>10||!is_string($codigo))                     return $this->msjAtencion('error en el codigo ingresado');
            if(strlen($materia)>TAM_MATERIA_MAX || !is_string($materia))    return $this->msjAtencion('error en la materia ingresada');
            if(strlen($sede)>13||!is_string($sede))                         return $this->msjAtencion('error en el codigo ingresado');
            
            $horario = $_POST['horaInicio'].':'.$_POST['minInicio'].' a '.$_POST['horaFin'].':'.$_POST['minFin'];
            if($horario ==': a :') $horario=NULL;
            
            $vecCursos=Modelo::buscarCurso($materia,$catedra,$sede,$codigo,$horario);
            if(!empty($vecCursos)){
                $busqueda .='<ul class="navbar-nav " id="listaCursos"><h3>Resultados</h3>';
                foreach ($vecCursos as $curso) {
                    $busqueda .='<a href="/Seccion/Curso/'.$curso['idCurso'].'/1" class="enlace">
                    <div class="d-flex w-100 justify-content-between"><h5 class="mb-1">Materia: '.$curso['nombreMateria'].'</h5><small>Horario: '
                    .$curso['horario'].'</small></div><p class="mb-1">Catedra: '.$curso['nombreCatedra'].'<br/>Sede: '.$curso['sede']
                    .'<br/>Código de curso:<small> '.$curso['codigo'].'</small></p> </a>
                    <img class="card-img-top " src="/Vistas/imagenes/item12.png" height="10" alt="Card image cap">';
                }
                $busqueda .= '</ul> <a class="btn btn-sm enlace" href="/Seccion/CursosCatedras/default">Limpiar busqueda</a>';
            }else{
                $busqueda .='<h3>No se encontraron resultados para '.$materia.'&#8226;'.$catedra.'&#8226;'.$codigo.'&#8226;'.$horario.'&#8226;'.$sede.'</h3>
                <a class="btn btn-sm enlace" href="/Seccion/CursosCatedras/default">Limpiar busqueda</a>';
            }
            $sectorBusq ='';
        }elseif ($param=='default'){
            $sectorBusq = file_get_contents(DIR_RAIZ.DIR_VISTA.DIR_HTMLS.'/formularioBusquedaCurso');
        }else{
            return $this->msjAtencion('pagina inexistente en el sitio');
        }
        
        $this->getVista()->armarHtml();
        $this->getVista()->modificarCuerpo('{sectorDerecha}','');
        $this->getVista()->modificarCuerpo('{sectorIzquierda}',file_get_contents(DIR_RAIZ.DIR_VISTA.DIR_HTMLS.'/cuerpoCursos'));
        $this->getVista()->modificarCuerpo('{colIzq}','12');
        $this->getVista()->modificarCuerpo('{resultadoBusqueda}',$busqueda);
        $this->getVista()->modificarCuerpo('{buscadorCursos}',$sectorBusq);
        if(isset($_SESSION['idUsuario'])){                      
            $this->getVista()->modificarCuerpo('{cargaDeCurso}',file_get_contents(DIR_RAIZ.DIR_VISTA.DIR_HTMLS.'/formularioCargarCurso'));            
        }else{
            $this->getVista()->modificarCuerpo('{cargaDeCurso}','');
        }
        
        $this->getVista()->agregarHoraYMins();          
        $this->getVista()->modificarCuerpo('{scriptJs}','/Vistas/js/cursos.js');
        $this->getVista()->modificarCuerpo('{archCss}','/Vistas/css/index.css');
        $mat = new ListaMaterias();
        $lista = $mat->listadoHtml;
        $this->getVista()->modificarCuerpo('{listaDeMaterias}',$lista);
        return $this->getVista();  
    }
    
    public function metodoIrOpiniones($param,$pagina){
        $listaOpiniones=null;
        $opiniones=null;
        $busqueda=null;
        if($param=='upload'){
            // Sanitizando y validando
            $catedra=trim($_POST['unaCatedra']);
            $materia=trim($_POST['unaMateria']);
            $profesor=trim($_POST['unProfesor']);
            if(empty($catedra)||strlen($catedra)>60 ||!is_string($catedra)){
                return $this->msjAtencion('error en la catedra ingresada');
            }
            if(empty($materia)||strlen($materia)>TAM_MATERIA_MAX||!is_string($materia)){
                return $this->msjAtencion('error en la materia ingresada');
            }
            if(empty($profesor)>90||!is_string($profesor)){
                return $this->msjAtencion('error en el profesor ingresado');
            }
            $listaOpiniones=Modelo::buscarCatedra($materia,$catedra);
            if(count($listaOpiniones)!=0){
                $msj = 'Ya existen foros de opiniones sobre<br/>Materia: '.$materia.'&nbsp;Catedra: '.$catedra;
                $msj .= '<br/><p>fijarse si los nombres de catedras son muy similares</p>';
                $msj .='<img class="card-img-top " src="/Vistas/imagenes/item12.png" height="10" alt="Card image cap">
                        <div id="" class="list-group">';
                foreach ($listaOpiniones as $opinion) {
                    $msj .='<a href="/Seccion/IrHiloOpinion/'.$opinion['idCatedra'].'/1" class="enlace">
                    <div class="d-flex w-100 justify-content-between"><h4 class="mb-1">Materia: '.$opinion['materia'].'</h4></div><p class="mb-1">
                        Catedra: '.$opinion['catedra'].'<br/>Profesores: '.$opinion['profesores'].'</p> </a>
                        <img class="card-img-top " src="/Vistas/imagenes/item12.png" height="10" alt="Card image cap">';
                }
                $msj .= '</div>';
                return $this->msjAtencion($msj);
            }
            Modelo::cargarOpinionCatedra($materia,$catedra,$profesor);
            $vecHilo=Modelo::buscarHiloCatedra($materia,$catedra,$profesor);
            header('Location:http://'.DOMINIO.'/Seccion/irHiloOpinion/'.$vecHilo['idCatedra'].'/1');
        }else if($param=='search'){
            // Sanitizando y validando
            $catedra=trim($_POST['unaCatedra']);
            $materia=trim($_POST['unaMateria']);
            $profesor=trim($_POST['unProfesor']);
            if(strlen($catedra)>60 ||!is_string($catedra)){
                return $this->msjAtencion('error en la catedra ingresada');
            }
            if(strlen($materia)>TAM_MATERIA_MAX||!is_string($materia)){
                return $this->msjAtencion('error en la materia ingresada');
            }
            if(strlen($profesor)>90||!is_string($profesor)){
                return $this->msjAtencion('error en el profesor ingresado');
            }
            $listaOpiniones=Modelo::buscarOpinionesCatedra($materia,$catedra,$profesor);
            if(!empty($listaOpiniones)){
                $busqueda .='<ul class="navbar-nav " id="listaOpiniones"><h3>Resultados:</h3>';
                foreach ($listaOpiniones as $opinion) {
                    $busqueda .='<a href="/Seccion/IrHiloOpinion/'.$opinion['idCatedra'].'/1" class="enlace">
                    <div class="d-flex w-100 justify-content-between"><h4 class="mb-1">Materia: '.$opinion['materia'].'</h4></div><p class="mb-1">
                        Catedra: '.$opinion['catedra'].'<br/>Profesores: '.$opinion['profesores'].'</p> </a>
                        <img class="card-img-top " src="/Vistas/imagenes/item12.png" height="10" alt="Card image cap">';
                }
                $busqueda .= '</ul><a class="btn btn-sm enlace" href="/Seccion/irOpiniones/default/1">Limpiar busqueda</a>';
            }else{
                $busqueda .='<h3>No se encontraron resultados para '.$materia.'&#8226;'.$catedra.'&#8226;'.$profesor.'</h3>
                            <a class="btn btn-sm enlace" href="/Seccion/irOpiniones/default/1">Limpiar busqueda</a>';
            }
            $listaOpiniones=Modelo::ultimasOpiniones();
            rsort($listaOpiniones);
            //echo nl2br("\ncursos1: vecCursos.length()=".count($vecCursos));
            if(!empty($listaOpiniones)){
                $opiniones='<div id="ultimosComentarios" class="list-group">';
                foreach ($listaOpiniones as $opinion) {
                    //echo nl2br('\nidCurso='.$curso['idCurso']." materia=".$curso['nombreMateria']);
                    $opiniones .= '<a  class="enlace" href="/Seccion/irHiloOpinion/'.$opinion['idCatedra'].'/1 ">
                    <h5 class="esquinaIzq2">Materia:&nbsp '.$opinion['materia'].'</h5>
                    <div class="contenido2">
                        catedra: '.$opinion['catedra'].'<br/>
                        profesores: '.$opinion['profesores'].'<br/>
                        <div class="px-2">comentario: '.$opinion['contenido'].'</div>
                    </div>
                    </a>
                   <img class="card-img-top separador" src="/Vistas/imagenes/item12.png" height="35" alt="Card image cap">'; 
                }
                $opiniones .= '</div>';
            }
            $sectorBusq ='';
        }
        elseif($param=='default'){
           $listaOpiniones=Modelo::ultimasOpiniones();
           rsort($listaOpiniones);
            //echo nl2br("\ncursos1: vecCursos.length()=".count($vecCursos));
            if(!empty($listaOpiniones)){
                $opiniones='<div id="ultimosComentarios" class="list-group">';
                foreach ($listaOpiniones as $opinion) {
                    //echo nl2br('\nidCurso='.$curso['idCurso']." materia=".$curso['nombreMateria']);
                    $opiniones .= '<a  class="enlace" href="/Seccion/irHiloOpinion/'.$opinion['idCatedra'].'/1 ">
                                    <h5 class="esquinaIzq2">Materia:&nbsp '.$opinion['materia'].'</h5>
                                    <div class="contenido2">
                                        catedra: '.$opinion['catedra'].'<br/>
                                        profesores: '.$opinion['profesores'].'<br/>
                                        <div class="px-2">comentario: '.$opinion['contenido'].'</div>
                                    </div>
                                    </a>
                                   <img class="card-img-top separador" src="/Vistas/imagenes/item12.png" height="35" alt="Card image cap">';                
                }
                $opiniones .= '</div>';
            }
            $sectorBusq = file_get_contents(DIR_RAIZ.DIR_VISTA.DIR_HTMLS.'/formularioBusquedaOpinion');
        }else{
            return $this->msjAtencion('pagina inexistente en el sitio');
        }
        
        $this->getVista()->armarHtml();
        $this->getVista()->modificarCuerpo('{sectorDerecha}',file_get_contents(DIR_RAIZ.DIR_VISTA.DIR_HTMLS.'/sectorDerecho'));
        $this->getVista()->modificarCuerpo('{sectorIzquierda}',file_get_contents(DIR_RAIZ.DIR_VISTA.DIR_HTMLS.'/cuerpoOpiniones'));
        $this->getVista()->modificarCuerpo('{colIzq}','7');
        $this->getVista()->modificarCuerpo('{colDer}','5');
        $this->getVista()->modificarCuerpo('{tituloDer}','Ultimas opiniones');
        $this->getVista()->modificarCuerpo('{pieDer}','ultimas opiniones');
        $this->getVista()->modificarCuerpo('{panelDer}',$opiniones);
        $this->getVista()->modificarCuerpo('{resultadoBusqueda}',$busqueda);
        $this->getVista()->modificarCuerpo('{buscadorOpiniones}',$sectorBusq);
        if(isset($_SESSION['idUsuario'])){
            $this->getVista()->modificarCuerpo('{crearHiloDeOpinion}',file_get_contents(DIR_RAIZ.DIR_VISTA.DIR_HTMLS.'/formularioCrearHiloOpinion'));
        }else{
            $this->getVista()->modificarCuerpo('{crearHiloDeOpinion}','');
        }
        $this->getVista()->modificarCuerpo('{scriptJs}','/Vistas/js/opiniones.js');
        $this->getVista()->modificarCuerpo('{archCss}','/Vistas/css/index.css');
        $mat = new ListaMaterias();
        $lista = $mat->listadoHtml;
        $this->getVista()->modificarCuerpo('{listaDeMaterias}',$lista);
        return $this->getVista();        
    }
    
    public function metodoIrHiloOpinion($idCatedra,$pagina){
        $this->getVista()->armarHtml();
        $this->getVista()->modificarCuerpo('{sectorDerecha}','');
        $this->getVista()->modificarCuerpo('{sectorIzquierda}',file_get_contents(DIR_RAIZ.DIR_VISTA.DIR_HTMLS.'/cuerpoHiloOpinion'));
        $this->getVista()->modificarCuerpo('{colIzq}','12');
        //$curso=Modelo::buscarUnCurso($idCurso);
        $hilo=Modelo::buscarHiloOpinion($idCatedra);
        $titulo='<h2>Materia:&nbsp; '.$hilo['materia'].'<br/> Cátedra:&nbsp;'.$hilo['catedra'].'<br/> Profesor/es:&nbsp;'.$hilo['profesores'].'</h2>';
        $this->getVista()->modificarCuerpo('{catedra}',$titulo);
        //$vecComentarios = Modelo::buscarComentsCurso($idCurso);
        $vecComentarios = Modelo::buscarComentsCatedra($idCatedra);
        //rsort($vecComentarios);
        $cantComents=count($vecComentarios);
        $listaComents=null;
        $vecComents=array_slice($vecComentarios, ($pagina-1)*CANT_COMENTS,CANT_COMENTS,true);
        
        if ($this->getUsuario()->tieneSesion()&&($pagina-1==intdiv($cantComents-1,10))) {
            $subMenuSesion='<form class="form-horizontal" id="crearComentario" action="/Seccion/ComentarCatedra/'.$idCatedra.'" method="POST">
				<fieldset>
					<h2>Agrega un comentario:</h2>
                	<div class="form-group row" id="formularioRegistro">
						<label class="col-sm-1 col-form-label" for="comentario" placeholder="agrega una opinion">Opinar:</label>
						<br>
                        <div class="col-sm-11">
							<textarea name="unComentario" id="area1"></textarea>
                            <small id="avisoTxt"></small>
						</div>
					</div>
					<button class="btn btn-secondary enlace" type="submit" id="comentar">Opinar</button>
				</fieldset>
			</form> ';
        }else{
            $subMenuSesion='';
        }
        $unHref='<li class="page-item"><a class="page-link" href="/Seccion/irHiloOpinion/'.$idCatedra.'/';
        $botones = new Paginacion($pagina,$cantComents,$unHref);

        // listando los comentarios
        foreach ($vecComents as $comentario){
            $usuario = Modelo::buscarUsuario($comentario["idUsuario"]);
            $vecFecha=date_parse($comentario["fechaHora"]);
            $listaComents .= '  <div class="media">
                                    <div class="media-left">
                                        <img src="/Vistas/imagenesUsers/'.$usuario["dirImg"].'" class="comentarista media-object" >
                                        <h3 class="media-heading">'.htmlentities($usuario["apodo"]).' </h3>
                                        <div id="fechaHora">
                                            <small class="text-muted">'.$vecFecha[day].'/'.$vecFecha[month].'/'.$vecFecha[year].'<br/>'.$vecFecha[hour].':'.$vecFecha[minute].'</small>
                                        </div>
                                    </div>
                                    <div class="media-body">
                                        <div class="elComentario">
                                            '.$comentario["contenido"].'
                                        </div>
                                    </div>
                                </div>
                                <img src="/Vistas/imagenes/separador.png" class="separador"><br/>';
        }
        $this->getVista()->modificarCuerpo('{scriptJs}','/Vistas/js/opiniones.js');
        $this->getVista()->modificarCuerpo('{archCss}','/Vistas/css/opiniones.css');
        $this->getVista()->modificarCuerpo('{pieIzq}','comentarios de usuarios');
        $this->getVista()->modificarCuerpo('{listaOpiniones}',$listaComents);
        $this->getVista()->modificarCuerpo('{paginacion}',$botones->getBotonera());
        $this->getVista()->modificarCuerpo('{tieneSesion}',$subMenuSesion);
        return $this->getVista();
    }

    public function metodoComentarCatedra($idCatedra) {
        if(!filter_var($idCatedra,FILTER_VALIDATE_INT)){
            return $this->msjAtencion("error en la catedra ingresada");
        }
        Modelo::comentarCatedra($_SESSION['idUsuario'], $_POST['unComentario'], $idCatedra);
        header('Location:http://'.DOMINIO.'/Seccion/irHiloOpinion/'.$idCatedra.'/1');
    }
    
    public function metodoIrAApuntes($param,$pagina){
        $this->getVista()->armarHtml();
        $this->getVista()->modificarCuerpo('{sectorDerecha}','');
        $this->getVista()->modificarCuerpo('{sectorIzquierda}',file_get_contents(DIR_RAIZ.DIR_VISTA.DIR_HTMLS.'/cuerpoApuntes'));
        $this->getVista()->modificarCuerpo('{colIzq}','12');
        if($param=='default'){
            $busq=file_get_contents(DIR_RAIZ.DIR_VISTA.DIR_HTMLS.'/formularioBusquedaApuntes');
            $mat = new ListaMaterias();
            $lista = $mat->listadoHtml;
        }else if($param=='upload'){
            // Sanitizando y validando
            $titulo=trim($_POST['unTitulo']);
            $autor=trim($_POST['unAutor']);
            $materia=trim($_POST['unaMateria']);
            if(empty($titulo)||strlen($titulo)>TAM_TITULO_MAX||!is_string($titulo)){
                return $this->msjAtencion('error en el titulo ingresado');
            }
            if(empty($autor)||strlen($autor)>TAM_AUTOR_MAX||!is_string($autor)){
                return $this->msjAtencion('error en el autor ingresado');
            }
            if(empty($materia)||strlen($materia)>TAM_MATERIA_MAX||!is_string($materia)){
                return $this->msjAtencion('error en la materia ingresada');
            }
            $enlace=trim($_POST['unaUbicacionUrl']);
            if(empty($enlace)){
                return $this->msjAtencion('no indicaste un link al apunte correcto');
            }
            if(strlen($_POST['unaUbicacionUrl'])<TAM_LINK_APUNTE_MIN || strlen($_POST['unaUbicacionUrl'])>TAM_LINK_APUNTE_MAX){
                return $this->msjAtencion('el enlace indicado tiene una longitud incorrecta');
            }
            $vecRepe=Modelo::buscarLinkApunte($enlace);
            if(!empty($vecRepe)){
                return $this->msjAtencion('el enlace indicado ya se encuentra cargado (o sea que ese apunte ya está subido)');
            }
            $vecRepe=Modelo::buscarApunteRepe($titulo,$autor);
            if(!empty($vecRepe)){
                return $this->msjAtencion('el autor y titulo indicados ya se encuentran cargados (o sea que ese apunte ya está subido)');
            }
            Modelo::cargarApunte($titulo,$autor,$materia,$enlace,$_SESSION['idUsuario']);   
            $busq=file_get_contents(DIR_RAIZ.DIR_VISTA.DIR_HTMLS.'/formularioBusquedaApuntes');
        }else if($param=='search'){
            // Sanitizando y validando
            $titulo=trim($_POST['unTitulo']);
            $autor=trim($_POST['unAutor']);
            $materia=trim($_POST['unaMateria']);
            if(strlen($titulo)>TAM_TITULO_MAX||!is_string($titulo)){
                return $this->msjAtencion('error en el titulo ingresado');
            }
            if(strlen($autor)>TAM_AUTOR_MAX||!is_string($autor)){
                return $this->msjAtencion('error en la autor ingresado');
            }
            if(strlen($materia)>TAM_MATERIA_MAX||!is_string($materia)){
                return $this->msjAtencion('error en la materia ingresada');
            }
            $vecApTot=Modelo::buscarApuntes($titulo,$autor,$materia);
            $cantApuntes=count($vecApTot);
            $vecAp=array_slice($vecApTot, ($pagina-1)*CANT_COMENTS,CANT_COMENTS,true);
            $unHref='<li class="page-item"><a class="page-link" href="/Seccion/irAApuntes/search/';
            $botones = new Paginacion($pagina,$cantApuntes,$unHref);
            $this->getVista()->modificarCuerpo('{paginacion}',$botones->getBotonera());
            if(!empty($vecAp)){
                $this->getVista()->modificarCuerpo('{listaApuntes}','<ul class="navbar-nav " id="listaApuntes">{apuntes}</ul>');
                $busq ='<br/><h2>Resultado de la búsqueda</h2><br/><div class="list-group">';
                foreach ($vecAp as $apunte) {
                    if($this->getUsuario()->getRol()=='ADMI'){
                        $borrado ='<div class="badge badge-primary text-wrap esquinaDer2" style="background-color: rgba(21, 24, 29, 0.9);">
                                    <form class="form-inline" id="" action="/Administrar/EliminarApunte'.$apunte["idApunte"].'" method="POST" enctype="multipart/form-data">
                                        <button type="submit" id="BorrarCurso" value="Borrar" class="btn btn-sm enlace" style="font-size: 1.6ex;">eliminar apunte</button>
                                        <div class="form-group mx-sm-3 mb-2" id="" >
                                            <input type="password" class="form-control" name="unaPassword1" placeholder="password de Admin" style="font-size: 1.6ex;"required>
                                        </div>
                                    </form>
                                   </div>';
                    }else{ 
                        $borrado='';
                    }

                    $busq.= '<div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">Titulo: '.$apunte['titulo'].'</h5>
                                    <small>subido el '.$apunte['fechaSubida']."&nbsp; (id:.".$apunte['idApunte'].')<br/>por:'.$apunte['apodo'].'</small>
                                </div>
                                <p class="mb-1">Autor/es: '.$apunte['autores'].'<br/>Materia: '.$apunte['materia'].'
                                <br/>Dirección para descargarlo:<small> '.$apunte['dirurl'].'</small></p>
                            <div class="col-mb-1" id="">
                                      <a class="btn btn-sm enlace " href="'.$apunte['dirurl'].'" >Descargar</a>
                                      '.$borrado.'
	                            </div><img src="/Vistas/imagenes/separador.png" class="separador"><br/>';
                }
                $busq .= '</div></br>';
            }else{
                $busq ='<h3>No se encotraron apuntes</h3>';// de '.$autor.' * '.$titulo.' * '.$materia.'</h3><br/>';
            }
            //$busq.=file_get_contents(DIR_RAIZ.DIR_VISTA.DIR_HTMLS.'/formularioBusquedaApuntes');
            $busq.= '<a class="btn btn-sm enlace" href="/Seccion/irAApuntes/default/0/Apuntes/10/1">Limpiar busqueda</a>';
        }else{
            $this->msjError('pagina inexistente en el sitio');
        }
        if($this->getUsuario()->tieneSesion()){
            $this->getVista()->modificarCuerpo('{subidaDeApuntes}',file_get_contents(DIR_RAIZ.DIR_VISTA.DIR_HTMLS.'/formularioSubirApunte'));
            $mat = new ListaMaterias(); 
            $this->getVista()->modificarCuerpo('{listaDeMaterias}',$mat->listadoHtml);            
        }else{
            $this->getVista()->modificarCuerpo('{subidaDeApuntes}','');
        }
        $this->getVista()->modificarCuerpo('{paginacion}','');
        $this->getVista()->modificarCuerpo('{busquedaApuntes}',$busq);
        if($param=='default'){$this->getVista()->modificarCuerpo('{listaDeMaterias}',$lista);}
        $this->getVista()->modificarCuerpo('{scriptJs}','/Vistas/js/apuntes.js');
        $this->getVista()->modificarCuerpo('{archCss}','/Vistas/css/index.css');
        return $this->getVista();
    }
    
    public function metodoIrTema($idTema,$pagina) {
        $this->getVista()->armarHtml();
        $this->getVista()->modificarCuerpo('{sectorDerecha}','');
        $this->getVista()->modificarCuerpo('{sectorIzquierda}',file_get_contents(DIR_RAIZ.DIR_VISTA.DIR_HTMLS.'/cuerpoTema'));
        $this->getVista()->modificarCuerpo('{colIzq}','12');
        //if existe tema?
        $vecData = Modelo::buscarTema($idTema);
        if (empty($vecData)) {
            return $this->msjAtencion('no existe la pagina pedida');
        }
        $tema = new Tema($vecData);
        $this->getVista()->modificarCuerpo('{nombreTema}',htmlentities($tema->getTitulo()));
        $usuario = Modelo::buscarUsuario($tema->getAutor());
        $face=NULL;
        $redSoc2=NULL;
        if(($usuario["redSocial1"]==NULL)||(empty($usuario["redSocial1"]))){
            $face = '';
        }else{
            $face = '<div class=""><a href="'.$usuario["redSocial1"].'"> <img src="/Vistas/imagenes/face.png" class="icono3" ></a></div>';
        }
        if(($usuario["redSocial2"]==NULL)||(empty($usuario["redSocial2"]))){
            $redSoc2 = '';
        }else{
            $redSoc2 = '<div clas=""><a href="'.$usuario["redSocial2"].'"> <img src="/Vistas/imagenes/redSocial2.png" class="icono3"></a></div>';
        }
        $this->getVista()->modificarCuerpo('{redesSociales}',$face.$redSoc2);
        $this->getVista()->modificarCuerpo('{autor}',htmlentities($usuario["apodo"]));
        $this->getVista()->modificarCuerpo('{comentario}',$tema->getComentarioInicial());//,htmlentities($tema->getComentarioInicial()));
        $vecFecha=date_parse($tema->getFecha());
        $hora=str_pad((int) $vecFecha[hour],2,"0",STR_PAD_LEFT);
        $min=str_pad((int) $vecFecha[minute],2,"0",STR_PAD_LEFT);
        $mes=str_pad((int) $vecFecha[month],2,"0",STR_PAD_LEFT);
        $dia=str_pad((int) $vecFecha[day],2,"0",STR_PAD_LEFT);

        $fechaString='<br/>'.$dia.'/'.$mes.'/'.$vecFecha[year].'<br/>'.$hora.':'.$min;
        $this->getVista()->modificarCuerpo('{fecha}',$fechaString);
        $this->getVista()->modificarCuerpo('{dirImg}',$usuario['dirImg']);
        
        $vecComentarios = Modelo::buscarComentarios($idTema);
        $cantComents=count($vecComentarios);
        $listaComents=null;
        $vecComents=array_slice($vecComentarios, ($pagina-1)*CANT_COMENTS,CANT_COMENTS,true);
        if ($this->getUsuario()->tieneSesion()&&($pagina-1==intdiv($cantComents-1,10))) {
            $subMenuSesion='
            <div class="media">
                <div class="media-left">
                    <img src="/Vistas/imagenesUsers/'.$_SESSION["img"].'"class="mr-3 media-object icono1" >
                </div>
                <div class="media-body">
                    <form class="form-horizontal" id="crearComentario" action="/Seccion/CrearComentario/'.$idTema.'" method="POST">
				        <div class="form-group row" id="formularioRegistro">
					        <div id="comentarioActual">
							    <textarea name="unComentario" id="area1"></textarea>
						    </div>
					    </div>
					    <button class="btn btn-secondary btn-sm enlace" type="submit" id="CrearComentario">comentar</button>
                    </form>
                </div>
            </div>
            <img src="/Vistas/imagenes/separador.png" class="separador">';            
        }else{
            $subMenuSesion='';
        }
        $unHref='<li class="page-item"><a class="page-link" href="/Seccion/irTema/'.$idTema.'/';
        //echo nl2br("\n\n\n\n\n\n\n yendo a paginacion pag:".$pagina." cantComens:".$cantComents."\n");
        $botones = new Paginacion($pagina,$cantComents,$unHref);
        // listando los comentarios
        foreach ($vecComents as $comentario){
            $usuario = Modelo::buscarUsuario($comentario["idUsuario"]);
            $vecFecha=date_parse($comentario["fechaHora"]);
            $face=NULL;
            $redSoc2=NULL;
            if(($usuario["redSocial1"]==NULL)||(empty($usuario["redSocial1"]))){
                $face = '';
            }else{
                $face = '<div class=""><a href="'.$usuario["redSocial1"].'"> <img src="/Vistas/imagenes/face.png" class="icono3" ></a></div>';
            }
            if(($usuario["redSocial2"]==NULL)||(empty($usuario["redSocial2"]))){
                $redSoc2 = '';
            }else{
                $redSoc2 = '<div clas=""><a href="'.$usuario["redSocial2"].'"> <img src="/Vistas/imagenes/redSocial2.png" class="icono3"></a></div>';
            }
            $hora=str_pad((int) $vecFecha[hour],2,"0",STR_PAD_LEFT);
            $min=str_pad((int) $vecFecha[minute],2,"0",STR_PAD_LEFT);
            $mes=str_pad((int) $vecFecha[month],2,"0",STR_PAD_LEFT);
            $dia=str_pad((int) $vecFecha[day],2,"0",STR_PAD_LEFT);
            if($this->getUsuario()->getRol()=='ADMI'){
                if(isset($_POST['idComentario'])&&($_POST['idComentario']==$comentario['idComentario'])){
                    $borrado ='';/*'<div class="badge badge-primary text-wrap esquinaDer2" style="background-color: rgba(21, 24, 29, 0.9);">
                            <form class="form-inline" id="" action="/Administrar/EliminarComentario'.$comentario["idComentario"].'" method="POST" enctype="multipart/form-data">
                            <h6>El comentario se eliminara permanentemente, esta seguro de borrarlo? &nbsp; </h6>     
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="si" name="confirmado" class="custom-control-input">    
                                <label class="custom-control-label" for="si">Si</label>                            
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="no" name="confirmado" class="custom-control-input">
                                <label class="custom-control-label" for="no">No</label>
                            </div>
                            <button type="submit" id="BorrarCurso" value="Borrar" class="btn btn-sm enlace" style="font-size: 1.6ex;">OK</button>
                                <div class="form-group mx-sm-3 mb-2 py-0 my-0" id="" >
                                    <input type="password" class="form-control py-0 my-0" name="unaPassword1" placeholder="password de Admin" style="font-size: 1.6ex;"required>
                                </div>
                            </form>
                           </div>';  */                  
                }else{
                    $borrado ='
                            <div class="badge badge-primary text-wrap esquinaDer2" style="background-color: rgba(21, 24, 29, 0.9);">
                                <form class="form-inline formuBorrar" id="" action="" method="POST" enctype="multipart/form-data">
                                    <button type="submit" id="BorrarCurso" value="Borrar" class="btn btn-sm enlace" style="font-size: 1.6ex;">borrar comentario</button>
                                    <input type="hidden" id="idDeComent" name="idComentario" value="'.$comentario["idComentario"].'">
                                </form>
                            </div>';                  
                }
            }else{ 
                $borrado='';
            }
            /*/Seccion/irTema/'.$idTema.'/'.$pagina.'*/
            $listaComents .= '<div class="media">
                                <div class="media-left">
                                    <h3 class="media-heading">'.htmlentities($usuario["apodo"]).' </h3>
                                    <img src="/Vistas/imagenesUsers/'.$usuario["dirImg"].'" class="icono2 media-object" >
                                    '.$face.$redSoc2.'
                                </div>
                                <div class="media-body "><h6 class="text-right"> '.$dia.'/'.$mes.'/'.$vecFecha[year].
                                '<br/>'.$hora.':'.$min.'</h6>
                                <div class="contenedor1">                                
                                  <div class="comentario1">'.$comentario["contenido"].'
                                  </div>
                                </div>'.$borrado.'         
                               </div>
                              </div><img src="/Vistas/imagenes/separador.png" class="separador"><br/>';
        }       
        $this->getVista()->modificarCuerpo('{scriptJs}','/Vistas/js/temas.js');
        $this->getVista()->modificarCuerpo('{archCss}','/Vistas/css/index.css');
        $this->getVista()->modificarCuerpo('{pieIzq}','comentarios de usuarios');
        $this->getVista()->modificarCuerpo('{listaComentarios}',$listaComents);
        $this->getVista()->modificarCuerpo('{paginacion}',$botones->getBotonera());
        $this->getVista()->modificarCuerpo('{tieneSesion}',$subMenuSesion);        
        $this->getVista()->modificarCuerpo('{eliminar}',$borrado);
        return $this->getVista();        
    }
   
    public function metodoCrearComentario($idTema){
        if(filter_var($idTema,FILTER_VALIDATE_INT)){
            Modelo::crearComentario($_SESSION['idUsuario'], $_POST['unComentario'], $idTema);
        }
        $vec = Modelo::cantComentsDeTema($idTema);
        $num = $vec['COUNT(*)'];
        $pag=0;
        if(($num%10)==0){
            $pag = $num/10;
        }else{
            $pag = intval(($num/10))+1;
        }
        header('Location:http://'.DOMINIO.'/Seccion/irTema/'.$idTema.'/'.$pag);
    }

    public function metodoComentarCurso($idCurso){
        if(filter_var($idCurso,FILTER_VALIDATE_INT)){
            Modelo::comentarCurso($_SESSION['idUsuario'], $_POST['unComentario'], $idCurso);
        }
        $vec = Modelo::cantComentsDeCurso($idCurso);
        $num = $vec['COUNT(*)'];
        $pag=0;
        if(($num%10)==0){
            $pag = $num/10;
        }else{
            $pag = intval(($num/10))+1;
        }
        header('Location:http://'.DOMINIO.'/Seccion/Curso/'.$idCurso.'/'.$pag);
    }
    
}
?>