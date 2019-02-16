<?php
require DIR_RAIZ.DIR_APP.'/Modelo.php';
class Comment{
    public $autor;
    public $contenido;
    public $fechaHora;
    public $tema;
    public $idTema;
    public $cantComents;
    public $seccion;
    public $idCurso;
    public $materiaCurso;
    public $catedraCurso;
    public $codigoCurso;
    public $idCatedra;
    public $catedraOpinion;
    public $materiaOpinion;
    public $profesoresOpinion;
}

class ControladorUltimosComents{
    public function metodoCheqApodo($apodo){
        $apodoOk=Modelo::buscarUsuarioPorApodo($apodo);
        if(empty($apodoOk)||is_null($apodoOk)){
            $apodoOk=0;
        }else{
            $apodoOk=1;
        }
        $rta=array('apodo'=>$apodoOk);        
        echo json_encode($rta);
        exit();
    }
    public function metodoCheqMail($mail){
        $mailOk=Modelo::buscarMail($mail);
        if(empty($mailOk)||is_null($mailOk)){
            $mailOk=0;
        }else{
            $mailOk=1;
        }
        $rta=array('mail'=>$mailOk);        
        echo json_encode($rta);
        exit();
    }
    
    public function metodoPorDefecto() {
        $comentarios = array(); //array de objetos tipo Comment a JSONear y enviar
        $vec1 = (array) Modelo::ultimosDiez1();
        $vec2 = (array) array_merge($vec1, Modelo::ultimosDiez2());
        $vecCom = (array) array_merge($vec2, Modelo::ultimosDiez3());
        usort($vecCom, array("ControladorUltimosComents","ordenFecha"));
        $vecCom=array_slice($vecCom,0,10,true);
        
        foreach($vecCom as $comentario){
            $elemento = new Comment();
            $vecData = array();
            $elemento->autor = $comentario['apodo'];
            $elemento->fechaHora = $comentario['fechaHora'];
            $elemento->contenido = htmlentities($comentario['contenido']);            
            
            if(!empty($comentario['idTema'])){
                $vecCant = Modelo::cantComentsDeTema($comentario['idTema']);
                $elemento->cantComents = $vecCant['COUNT(*)'];
                $vecData = Modelo::buscarTemaYseccion($comentario['idTema']);
                $elemento->tema = $vecData['titulo'];
                $elemento->idTema = $comentario['idTema'];
                $elemento->seccion = $vecData['nombreSeccion'];                
            }elseif (!empty($comentario['idCurso'])) {
                $vecCant = Modelo::cantComentsDeCurso($comentario['idCurso']);
                $elemento->cantComents = $vecCant['COUNT(*)'];
                $vecData =Modelo::buscarUnCurso($comentario['idCurso']);
                $elemento->idCurso = $comentario['idCurso'];
                $elemento->materiaCurso = $vecData['nombreMateria'];
                $elemento->catedraCurso = $vecData['nombreCatedra'];
                $elemento->codigoCurso = $vecData['codigo'];
                $elemento->seccion = 'Cursos por catedra';
            } else{
                $vecCant = Modelo::cantComentsDeCatedra($comentario['idCatedra']);
                $elemento->cantComents = $vecCant['COUNT(*)'];
                $vecData = Modelo::buscarHiloOpinion($comentario['idCatedra']);
                $elemento->idCatedra = $comentario['idCatedra'];
                $elemento->catedraOpinion = $vecData['catedra'];
                $elemento->materiaOpinion = $vecData['materia'];
                $elemento->profesoresOpinion = $vecData['profesores'];
                $elemento->seccion = 'Opinion y recomendacion de catedras';
            }
            $comentarios[] = $elemento;
        }
        echo json_encode($comentarios);
        exit();            
    }
    public static function ordenFecha($a,$b) {
        if ($a['fechaHora']<$b['fechaHora']) return 1;
        if ($a['fechaHora']>$b['fechaHora']) return -1;
        return 0;
    }
}
?>