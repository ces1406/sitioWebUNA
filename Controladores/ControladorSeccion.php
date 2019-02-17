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
            foreach ($vecTemas as $tema){  $listaTemas .= $this->getVista()->crearLiTema($tema->getIdTema(),$tema->getTitulo());  }
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
    
    // ir a un foro de un Curso determinado (de seccion cursos por catedra)
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
    
    // Foros creados para cursos por catedra
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
            if(strlen($sede)>13||!is_string($sede))                         return $this->msjAtencion('error en la sede ingresada');
            
            $horario = $_POST['horaInicio'].':'.$_POST['minInicio'].' a '.$_POST['horaFin'].':'.$_POST['minFin'];
            if($horario ==': a :') $horario=NULL;
            
            $vecCursos=Modelo::buscarCurso($materia,$catedra,$sede,$codigo,$horario);
            $busqueda=$this->getVista()->listaCursosBuscados($vecCursos);
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
    
    // Opiniones de catedras y profesores
    public function metodoIrOpiniones($param,$pagina){
        $listaOpiniones=null;
        $opiniones=null;
        $busqueda=null;
        if($param=='upload'){
            // Sanitizando y validando
            $catedra=trim($_POST['unaCatedra']);
            $materia=trim($_POST['unaMateria']);
            $profesor=trim($_POST['unProfesor']);
            if(empty($catedra)||strlen($catedra)>60 ||!is_string($catedra))             return $this->msjAtencion('error en la catedra ingresada');
            if(empty($materia)||strlen($materia)>TAM_MATERIA_MAX||!is_string($materia)) return $this->msjAtencion('error en la materia ingresada');
            if(empty($profesor)>90||!is_string($profesor))                              return $this->msjAtencion('error en el profesor ingresado');
            // Armando la vista html a devolver
            $listaOpiniones=Modelo::buscarCatedra($materia,$catedra);
            if(count($listaOpiniones)!=0){
                $msj = $this->getVista()->crearListaOpinionesDeCurso($listaOpiniones,$materia,$catedra);
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
            if(strlen($catedra)>60 ||!is_string($catedra))              return $this->msjAtencion('error en la catedra ingresada');
            if(strlen($materia)>TAM_MATERIA_MAX||!is_string($materia))  return $this->msjAtencion('error en la materia ingresada');
            if(strlen($profesor)>90||!is_string($profesor))             return $this->msjAtencion('error en el profesor ingresado');
            // Armando la vista html a devolver
            $listaOpiniones=Modelo::buscarOpinionesCatedra($materia,$catedra,$profesor);
            $busqueda =  $this->getVista()->crearListaOpinionesDeCurso2($listaOpiniones,$materia,$catedra);
            $listaOpiniones=Modelo::ultimasOpiniones();
            rsort($listaOpiniones);
            $opiniones = $this->getVista()->crearListaOpinionesDeCurso3($listaOpiniones);
            $sectorBusq ='';
        }
        elseif($param=='default'){       // Armando la vista html a devolver
           $listaOpiniones=Modelo::ultimasOpiniones();
           rsort($listaOpiniones);
           $opiniones = $this->getVista()->crearListaOpinionesDeCurso3($listaOpiniones);
           $sectorBusq = file_get_contents(DIR_RAIZ.DIR_VISTA.DIR_HTMLS.'/formularioBusquedaOpinion');
        }else{                          // Armando la vista html a devolver
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
        (isset($_SESSION['idUsuario']))?
            $this->getVista()->modificarCuerpo('{crearHiloDeOpinion}',file_get_contents(DIR_RAIZ.DIR_VISTA.DIR_HTMLS.'/formularioCrearHiloOpinion'))
            :$this->getVista()->modificarCuerpo('{crearHiloDeOpinion}','');
        $this->getVista()->modificarCuerpo('{scriptJs}','/Vistas/js/opiniones.js');
        $this->getVista()->modificarCuerpo('{archCss}','/Vistas/css/index.css');
        $mat = new ListaMaterias();
        $lista = $mat->listadoHtml;
        $this->getVista()->modificarCuerpo('{listaDeMaterias}',$lista);
        return $this->getVista();        
    }
    
    //ir al foro de opinion de una catedra-curso
    public function metodoIrHiloOpinion($idCatedra,$pagina){
        $this->getVista()->armarHtml();
        $this->getVista()->modificarCuerpo('{sectorDerecha}','');
        $this->getVista()->modificarCuerpo('{sectorIzquierda}',file_get_contents(DIR_RAIZ.DIR_VISTA.DIR_HTMLS.'/cuerpoHiloOpinion'));
        $this->getVista()->modificarCuerpo('{colIzq}','12');
        $hilo=Modelo::buscarHiloOpinion($idCatedra);
        $titulo='<h2>Materia:&nbsp; '.$hilo['materia'].'<br/> Cátedra:&nbsp;'.$hilo['catedra'].'<br/> Profesor/es:&nbsp;'.$hilo['profesores'].'</h2>';
        $this->getVista()->modificarCuerpo('{catedra}',$titulo);
        $vecComentarios = Modelo::buscarComentsCatedra($idCatedra);
        $cantComents=count($vecComentarios);
        $listaComents=null;
        $vecComents=array_slice($vecComentarios, ($pagina-1)*CANT_COMENTS,CANT_COMENTS,true);
        
        if ($this->getUsuario()->tieneSesion()&&($pagina-1==intdiv($cantComents-1,10))) {
            $subMenuSesion=$this->getVista()->crearAreaComentaje($_SESSION["img"]);
            $this->getVista()->modificarCuerpo('{action}','action="/Seccion/ComentarCatedra/'.$idCatedra.'"');
        }else{
            $subMenuSesion='';
        }
        $unHref='<li class="page-item"><a class="page-link" href="/Seccion/irHiloOpinion/'.$idCatedra.'/';
        $botones = new Paginacion($pagina,$cantComents,$unHref);

        // listando los comentarios
        foreach ($vecComents as $comentario){
            $usuario = Modelo::buscarUsuario($comentario["idUsuario"]);
            $listaComents .= $this->getVista()->crearComentario($usuario["dirImg"],$usuario["apodo"],$comentario["fechaHora"],$comentario["contenido"]);
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
        if(!filter_var($idCatedra,FILTER_VALIDATE_INT)) return $this->msjAtencion("error en la catedra ingresada");
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
            if(empty($titulo)||strlen($titulo)>TAM_TITULO_MAX||!is_string($titulo))return $this->msjAtencion('error en el titulo ingresado');
            if(empty($autor)||strlen($autor)>TAM_AUTOR_MAX||!is_string($autor)) return $this->msjAtencion('error en el autor ingresado');
            if(empty($materia)||strlen($materia)>TAM_MATERIA_MAX||!is_string($materia))return $this->msjAtencion('error en la materia ingresada');
            
            $enlace=trim($_POST['unaUbicacionUrl']);
            if(empty($enlace)) return $this->msjAtencion('no indicaste un link al apunte correcto');
            if(strlen($_POST['unaUbicacionUrl'])<TAM_LINK_APUNTE_MIN || strlen($_POST['unaUbicacionUrl'])>TAM_LINK_APUNTE_MAX)
                return $this->msjAtencion('el enlace indicado tiene una longitud incorrecta');
            
            $vecRepe=Modelo::buscarLinkApunte($enlace);
            if(!empty($vecRepe))return $this->msjAtencion('el enlace indicado ya se encuentra cargado (o sea que ese apunte ya está subido)');
            
            $vecRepe=Modelo::buscarApunteRepe($titulo,$autor);
            if(!empty($vecRepe))return $this->msjAtencion('el autor y titulo indicados ya se encuentran cargados (o sea que ese apunte ya está subido)');
            
            Modelo::cargarApunte($titulo,$autor,$materia,$enlace,$_SESSION['idUsuario']);   
            $busq=file_get_contents(DIR_RAIZ.DIR_VISTA.DIR_HTMLS.'/formularioBusquedaApuntes');
        }else if($param=='search'){
            // Sanitizando y validando
            $titulo=trim($_POST['unTitulo']);
            $autor=trim($_POST['unAutor']);
            $materia=trim($_POST['unaMateria']);
            if(strlen($titulo)>TAM_TITULO_MAX||!is_string($titulo))     return $this->msjAtencion('error en el titulo ingresado');
            if(strlen($autor)>TAM_AUTOR_MAX||!is_string($autor))        return $this->msjAtencion('error en la autor ingresado');
            if(strlen($materia)>TAM_MATERIA_MAX||!is_string($materia))  return $this->msjAtencion('error en la materia ingresada');
            
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
                    $borrado = ($this->getUsuario()->getRol()=='ADMI')? $this->getVista()->crearMenuBorrarApunte($apunte["idApunte"]) : '';
                    $busq.= $this->getVista()->crearListaApuntesBuscados($apunte,$borrado);
                }
                $busq .= '</div></br>';
            }else{
                $busq ='<h3>No se encotraron apuntes</h3>';
            }
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
        if (empty($vecData)) return $this->msjAtencion('no existe la pagina pedida');
        
        $tema = new Tema($vecData);
        $this->getVista()->modificarCuerpo('{nombreTema}',htmlentities($tema->getTitulo()));
        $usuario = Modelo::buscarUsuario($tema->getAutor());
        $face=NULL;
        $redSoc2=NULL;
        $face = (($usuario["redSocial1"]==NULL)||(empty($usuario["redSocial1"])))? ''
                :'<div class=""><a href="'.$usuario["redSocial1"].'"> <img src="/Vistas/imagenes/face.png" class="icono3" ></a></div>';
        $redSoc2 = (($usuario["redSocial2"]==NULL)||(empty($usuario["redSocial2"])))? ''
                :'<div clas=""><a href="'.$usuario["redSocial2"].'"> <img src="/Vistas/imagenes/redSocial2.png" class="icono3"></a></div>';
        
        $this->getVista()->modificarCuerpo('{redesSociales}',$face.$redSoc2);
        $this->getVista()->modificarCuerpo('{autor}',htmlentities($usuario["apodo"]));
        $this->getVista()->modificarCuerpo('{comentario}',$tema->getComentarioInicial());
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
            $subMenuSesion=$this->getVista()->crearAreaComentaje($_SESSION["img"]);
            $this->getVista()->modificarCuerpo('{action}','action="/Seccion/CrearComentario/'.$idTema.'"');          
        }else{
            $subMenuSesion='';
        }
        $unHref='<li class="page-item"><a class="page-link" href="/Seccion/irTema/'.$idTema.'/';
        $botones = new Paginacion($pagina,$cantComents,$unHref);
        // listando los comentarios
        foreach ($vecComents as $comentario){
            $usuario = Modelo::buscarUsuario($comentario["idUsuario"]);
            $vecFecha=date_parse($comentario["fechaHora"]);
            $face=NULL;
            $redSoc2=NULL;
            $face = (($usuario["redSocial1"]==NULL)||(empty($usuario["redSocial1"])))? ''
                :'<div class=""><a href="'.$usuario["redSocial1"].'"> <img src="/Vistas/imagenes/face.png" class="icono3" ></a></div>';
            $redSoc2 = (($usuario["redSocial2"]==NULL)||(empty($usuario["redSocial2"])))? ''
                :'<div clas=""><a href="'.$usuario["redSocial2"].'"> <img src="/Vistas/imagenes/redSocial2.png" class="icono3"></a></div>';
            ($this->getUsuario()->getRol()=='ADMI')?
                // Solo mostrar la opcion de borrar el comentario (el borrado efectivo se hace en ControladorAdministrar->metodoEliminarComentario)
                $borrado = $this->getVista()->crearMenuBorrarMsj($comentario["idComentario"],$idTema,$pagina)                 
                :$borrado='';
            $listaComents .= $this->getVista()->crearListaComentarios($usuario["apodo"],$usuario["dirImg"],$face,$redSoc2,$vecFecha,$comentario["contenido"],$borrado);            
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