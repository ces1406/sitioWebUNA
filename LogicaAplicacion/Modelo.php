<?php

class Modelo
{
    public static function conectarDB() {
        $unMysqli=new mysqli(HOST_DB,USUARIO_DB,PASS_DB,NOMBRE_DB);
        if($unMysqli->connect_error){
            die('error('.$unMysqli->connect_errno.'):'.$unMysqli->error);
        }else {
           $unMysqli->set_charset("utf8");
            return $unMysqli;
        }
    }    
    public static function cerrarConexion($unaConn) {
        mysqli_close($unaConn);
    }
    public static function buscarTemasSeccion($idSeccion) {
        $vecTemas=null;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return null;}
        $sql="SELECT * FROM unaWebDB.unaTemas WHERE idSeccion='".$idSeccion."' ORDER BY fechaCreacion DESC";
        $resultado=$unMysqli->query($sql);
        
        for ($numFila=$resultado->num_rows-1;$numFila>=0;$numFila--){
            $resultado->data_seek($numFila);
            $vecTemas[$numFila-1]=$resultado->fetch_assoc();
        }
        $resultado->close();
        Modelo::cerrarConexion($unMysqli);
        return $vecTemas;
    }    
    public static function buscarTemas($pal){
        $vecTemas=null;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return null;}
        $pal=$unMysqli->real_escape_string($pal);
        $sql="SELECT * FROM unaWebDB.unaTemas WHERE palabraClave1='".$pal."' OR palabraClave2='".$pal."' OR palabraClave3='".$pal."'";
        $resultado=$unMysqli->query($sql);
        
        for ($numFila=$resultado->num_rows-1;$numFila>=0;$numFila--){
            $resultado->data_seek($numFila);
            $vecTemas[$numFila-1]=$resultado->fetch_assoc();
        }
        $resultado->close();
        Modelo::cerrarConexion($unMysqli);
        return $vecTemas;
    }    
    public static function crearTema($autor,$titulo,$comentarioInicial,$palabra1,$palabra2,$palabra3,$idSeccion) {
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return false;}
        $autor=$unMysqli->real_escape_string($autor);
        $titulo=$unMysqli->real_escape_string($titulo);
        $comentarioInicial=$unMysqli->real_escape_string($comentarioInicial);
        $palabra1=$unMysqli->real_escape_string($palabra1);
        $palabra2=$unMysqli->real_escape_string($palabra2);
        $palabra3=$unMysqli->real_escape_string($palabra3);
        $sql = "INSERT INTO unaWebDB.unaTemas (titulo,idSeccion,idUsuario,palabraClave1,palabraClave2,palabraClave3,comentarioInicial,fechaCreacion)";
        $sql.= "VALUES('".$titulo."',".$idSeccion.",".$_SESSION['idUsuario'].",'".$palabra1."','".$palabra2;
        $sql.= "','".$palabra3."','".$comentarioInicial."',CURRENT_TIMESTAMP())";
        $unMysqli->query($sql);
        Modelo::cerrarConexion($unMysqli);
    }    
    public static function crearSeccion($titulo,$descripcion) {
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return false;}
        $tit=$unMysqli->real_escape_string($titulo);
        $descrip=$unMysqli->real_escape_string($descripcion);
        $sql = "INSERT INTO unaWebDB.unaSecciones (nombreSeccion,descripcion)";
        $sql.= "VALUES('".$tit."','".$descrip."')";
        $unMysqli->query($sql);
        Modelo::cerrarConexion($unMysqli);
    }    
    public static function eliminarSeccion($titulo) {
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return false;}
        $tit=$unMysqli->real_escape_string($titulo);
        $sql = "DELETE FROM unaWebDB.unaSecciones WHERE nombreSeccion='".$titulo."'";
        $unMysqli->query($sql);
        Modelo::cerrarConexion($unMysqli);
    }    
    public static function buscarSeccion($idSeccion) {
        $vecTema=null;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return null;}
        $sql="SELECT * FROM unaWebDB.unaSecciones WHERE idSeccion='".$idSeccion."'";
        $resultado=$unMysqli->query($sql);
        $vecTema=$resultado->fetch_assoc();
        $resultado->close();
        Modelo::cerrarConexion($unMysqli);
        return $vecTema;
    }    
    public static function buscarIdSeccion($nombre) {
        $unMysqli=Modelo::conectarDB();
        if ($unMysqli == false) { return false; }
        $sql="SELECT idSeccion FROM unaSecciones WHERE nombreSeccion='".$nombre."'";
        $resultado = $unMysqli->query($sql);
        if($unMysqli->affected_rows==0){
            $resultado->close();
            Modelo::cerrarConexion($unMysqli);
            return false;
        }elseif ($unMysqli->affected_rows==1){
            $fila=$resultado->fetch_assoc();
            $idSeccion=$fila['idSeccion'];
            $resultado->close();
            Modelo::cerrarConexion($unMysqli);
            return $idSeccion;
        }else {
            $resultado->close();
            Modelo::cerrarConexion($unMysqli);
            die('error');
        }
    }    
    public static function listarTemas(){
        $vecUser=null;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){
            return null;
        }
        $sql="SELECT * FROM unaWebDB.unaTemas ORDER BY titulo ";
        $resultado=$unMysqli->query($sql);
        for ($numFila=$resultado->num_rows-1;$numFila>=0;$numFila--){
            $resultado->data_seek($numFila);
            $vecUser[$numFila-1]=$resultado->fetch_assoc();
        }
        $resultado->close();
        Modelo::cerrarConexion($unMysqli);
        return $vecUser;
    }
    public static function eliminarTema($id) {
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return false;}
        $sql = "DELETE FROM unaWebDB.unaTemas WHERE idTema=".$id;
        $unMysqli->query($sql);
        Modelo::cerrarConexion($unMysqli);
    }    
    public static function buscarTema($idTema) {
        $vecTema=null;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){
            return null;
        }
        $sql="SELECT * FROM unaWebDB.unaTemas WHERE idTema='".$idTema."'";
        $resultado=$unMysqli->query($sql);
        $vecTema=$resultado->fetch_assoc();
        $resultado->close();
        Modelo::cerrarConexion($unMysqli);
        return $vecTema;
    }    
    public static function buscarSecciones(){
        $vecSecciones=null;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){
            return null;
        }
        $sql="SELECT * FROM unaWebDB.unaSecciones";
        $resultado=$unMysqli->query($sql);
        for($numFila=$resultado->num_rows-1;$numFila>=0;$numFila--){
            $resultado->data_seek($numFila);
            $vecSecciones[$numFila-1]=$resultado->fetch_assoc();
        }
        $resultado->close();
        Modelo::cerrarConexion($unMysqli);
        return $vecSecciones;
    }    
    public static function buscarComentarios($idTema){//,$pag) {
        $vecComents=null;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return null;}
        $sql="SELECT * FROM unaWebDB.unaComentarios WHERE idTema='".$idTema."' ORDER BY fechaHora DESC";// LIMIT ".$pag;
        $resultado=$unMysqli->query($sql);
        
        for ($numFila=$resultado->num_rows-1;$numFila>=0;$numFila--){
            $resultado->data_seek($numFila);
            $vecComents[$numFila-1]=$resultado->fetch_assoc();
        }
        $resultado->close();
        Modelo::cerrarConexion($unMysqli);
        return $vecComents;
    }    
    public static function altaUsuario($apodo,$mail,$pass,$rol,$token,$estado,$redSoc1,$redSoc2) {
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return false;}
        $apodo=$unMysqli->real_escape_string($apodo);
        $mail=$unMysqli->real_escape_string($mail);
        $pass=$unMysqli->real_escape_string($pass);
        $redSoc1=$unMysqli->real_escape_string($redSoc1);
        $redSoc2=$unMysqli->real_escape_string($redSoc2);
        $sql='INSERT INTO unaUsuarios (apodo,mail,contrasenia,rol,fechaIngreso,token,estadoCuenta,redSocial1,redSocial2) VALUES';
        $sql.='("'.$apodo.'","'.$mail.'","'.$pass.'","'.$rol.'",CURRENT_TIMESTAMP(),"'.$token.'","'.$estado.'","'.$redSoc1.'","'.$redSoc2.'")';
        //echo nl2br("\n sql: ".$sql);
        $unMysqli->query($sql);
        Modelo::cerrarConexion($unMysqli);
    }    
    public static function buscarUsuario($idUsuario) {
        $vecUser=null;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return null;}
        $sql="SELECT * FROM unaWebDB.unaUsuarios WHERE idUsuario='".$idUsuario."'";
        $resultado=$unMysqli->query($sql);
        $vecUser=$resultado->fetch_assoc();
        $resultado->close();
        Modelo::cerrarConexion($unMysqli);
        return $vecUser;
    }    
    public static function buscarUsuarioPorApodo($unApodo){
        $vecUser=null;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){
            return null;
        }
        $unApodo=$unMysqli->real_escape_string($unApodo);
        $sql="SELECT * FROM unaWebDB.unaUsuarios WHERE apodo='".$unApodo."'";
        $resultado=$unMysqli->query($sql);
        $vecUser=$resultado->fetch_assoc();
        $resultado->close();
        Modelo::cerrarConexion($unMysqli);
        return $vecUser;
    }    
    public static function listarUsuarios(){
        $vecUser=null;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){
            return null;
        }
        $sql="SELECT * FROM unaWebDB.unaUsuarios ORDER BY apodo ";
        $resultado=$unMysqli->query($sql);
        for ($numFila=$resultado->num_rows-1;$numFila>=0;$numFila--){
            $resultado->data_seek($numFila);
            $vecUser[$numFila-1]=$resultado->fetch_assoc();
        }
        $resultado->close();
        Modelo::cerrarConexion($unMysqli);
        return $vecUser;
    }    
    public static function borrarUsuario ($idUsuario) {
        $vecUser=null;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){
            return null;
        }
        $sql="DELETE FROM unaWebDB.unaUsuarios WHERE idUsuario=".$idUsuario;
        $resultado=$unMysqli->query($sql);
        Modelo::cerrarConexion($unMysqli);
    }    
    public static function deshabilitarUsuario ($idUsuario) {
        $vecUser=null;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){
            return null;
        }
        $sql="UPDATE unaWebDB.unaUsuarios SET estadoCuenta ='DESHABI' WHERE idUsuario=".$idUsuario;
        $resultado=$unMysqli->query($sql);
        Modelo::cerrarConexion($unMysqli);
    }    
    public static function habilitarUsuario ($idUsuario) {
        $vecUser=null;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){
            return null;
        }
        $sql="UPDATE unaWebDB.unaUsuarios SET estadoCuenta ='HABILIT' WHERE idUsuario=".$idUsuario;
        $resultado=$unMysqli->query($sql);
        Modelo::cerrarConexion($unMysqli);
    }    
    public static function buscarMail($mail){
        $vecUser=null;
        $unMysqli=Modelo::conectarDB();
        $sql="SELECT * FROM unaWebDB.unaUsuarios WHERE mail='".$mail."'";
        //echo nl2br("\n sql: ".$sql);
        $resultado=$unMysqli->query($sql);
        $vecUser=$resultado->fetch_assoc();
        $resultado->close();
        Modelo::cerrarConexion($unMysqli);
        return $vecUser;
    }    
    public static function actualizarEstadoCuenta($idUsuario){
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return false;}
        $sql='UPDATE unaWebDB.unaUsuarios SET estadoCuenta = "HABILIT" WHERE idUsuario ='.$idUsuario;
        $unMysqli->query($sql);
        Modelo::cerrarConexion($unMysqli);
    }    
    public static function actualizarMailToken($idUsuario,$mail,$token){
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return false;}
        $sql='UPDATE unaWebDB.unaUsuarios SET estadoCuenta = "DESHABI", mail="'.$mail.'", token="'.$token.'" WHERE idUsuario ='.$idUsuario;
        $unMysqli->query($sql);
        Modelo::cerrarConexion($unMysqli);
    }    
    public static function actualizarPassToken($id,$pass,$token){
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return false;}
        $sql='UPDATE unaWebDB.unaUsuarios SET estadoCuenta = "DESHABI", contrasenia="'.$pass.'", token="'.$token.'" WHERE idUsuario ='.$id;
        $unMysqli->query($sql);
        Modelo::cerrarConexion($unMysqli);
    }    
    public static function actualizarPassUsuario($idUsuario,$pass) {
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return false;}
        $sql='UPDATE unaWebDB.unaUsuarios SET contrasenia = "'.$pass.'" WHERE idUsuario ='.$idUsuario;
        $unMysqli->query($sql);
        Modelo::cerrarConexion($unMysqli);
    }    
    public static function actualizarImgUsuario($idUsuario,$img) {
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return false;}
        $sql='UPDATE unaWebDB.unaUsuarios SET dirImg = "'.$img.'" WHERE idUsuario ='.$idUsuario;
        $unMysqli->query($sql);
        Modelo::cerrarConexion($unMysqli);
    }
    public static function actualizarFace($idUsuario,$face) {
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return false;}
        $sql='UPDATE unaWebDB.unaUsuarios SET redSocial1 = "'.$face.'" WHERE idUsuario ='.$idUsuario;
        $unMysqli->query($sql);
        Modelo::cerrarConexion($unMysqli);
    }     
    public static function actualizarRedSocial2($idUsuario,$redSoc) {
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return false;}
        $sql='UPDATE unaWebDB.unaUsuarios SET redSocial2 = "'.$redSoc.'" WHERE idUsuario ='.$idUsuario;
        $unMysqli->query($sql);
        Modelo::cerrarConexion($unMysqli);
    } 
    public static function crearComentario($idAutor,$contenido,$idTema) {
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return null;}
        $contenido=$unMysqli->real_escape_string($contenido);
        $sql = "INSERT INTO unaWebDB.unaComentarios (contenido,fechaHora,idTema,idUsuario)";
        $sql.= "VALUES('".$contenido."',CURRENT_TIMESTAMP(),".$idTema.",'".$idAutor."')";
        $unMysqli->query($sql);
        Modelo::cerrarConexion($unMysqli);
    }
    public static function ultimosDiezComentariosXCurso() {
        $vecComent=null;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return null;}
        $sql="SELECT * FROM unaWebDB.unaComentarioXcurso C1 JOIN unaWebDB.unaUsuarios U1 ON U1.idUsuario=C1.idUsuario ORDER BY fechaHora DESC LIMIT 10";
        $resultado=$unMysqli->query($sql);
            for ($numFila=$resultado->num_rows-1;$numFila>=0;$numFila--){
                $resultado->data_seek($numFila);
                $vecComent[$numFila-1]=$resultado->fetch_assoc();
            }
        
        $resultado->close();
        Modelo::cerrarConexion($unMysqli);
        return $vecComent;
    }
    public static function ultimosDiezComentariosXCatedra() {
        $vecComent=null;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return null;}
        $sql="SELECT * FROM unaWebDB.unaComentarioXcatedra C2 JOIN unaWebDB.unaUsuarios U2 ON U2.idUsuario=C2.idUsuario ORDER BY fechaHora DESC LIMIT 10";
        $resultado=$unMysqli->query($sql);
            for ($numFila=$resultado->num_rows-1;$numFila>=0;$numFila--){
                $resultado->data_seek($numFila);
                $vecComent[$numFila-1]=$resultado->fetch_assoc();
            }
        $resultado->close();
        Modelo::cerrarConexion($unMysqli);
        return $vecComent;
    }
    public static function ultimosDiezTot() {
        $vecComent=null;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return null;}
        $sql="  SELECT * FROM unaWebDB.unaComentarioXcatedra C2 JOIN unaWebDB.unaUsuarios U2 ON U2.idUsuario=C2.idUsuario UNION
                SELECT * FROM unaWebDB.unaComentarios C3 JOIN unaWebDB.unaUsuarios U3 ON C3.idUsuario=U3.idUsuario UNION
                SELECT * FROM unaWebDB.unaComentarioXcurso C1 JOIN unaWebDB.unaUsuarios U1 ON U1.idUsuario=C1.idUsuario
                ORDER BY fechaHora DESC LIMIT 10";
        $resultado=$unMysqli->query($sql);
        for ($numFila=$resultado->num_rows-1;$numFila>=0;$numFila--){
            $resultado->data_seek($numFila);
            $vecComent[$numFila-1]=$resultado->fetch_assoc();
        }
        //$vecComent = $resultado->fetch_all(MYSQLI_ASSOC);
        $resultado->close();
        Modelo::cerrarConexion($unMysqli);
        return $vecComent;
    }
    public static function ultimosDiezComentariosXTema() {
        $vecComent=null;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return null;}
        $sql="SELECT * FROM unaWebDB.unaComentarios C3 JOIN unaWebDB.unaUsuarios U3 ON C3.idUsuario=U3.idUsuario ORDER BY fechaHora DESC LIMIT 10";
        $resultado=$unMysqli->query($sql);
            for ($numFila=$resultado->num_rows-1;$numFila>=0;$numFila--){
                $resultado->data_seek($numFila);
                $vecComent[$numFila-1]=$resultado->fetch_assoc();
            }      
        $resultado->close();
        Modelo::cerrarConexion($unMysqli);
        return $vecComent;
    }
    public static function ultimosDiezComentariosIniciales() {
        $vecComent=null;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return null;}
        $sql="SELECT * FROM unaWebDB.unaTemas C1 JOIN unaWebDB.unaUsuarios U1 ON U1.idUsuario=C1.idUsuario ORDER BY fechaCreacion DESC LIMIT 10";
        $resultado=$unMysqli->query($sql);
            for ($numFila=$resultado->num_rows-1;$numFila>=0;$numFila--){
                $resultado->data_seek($numFila);
                $vecComent[$numFila-1]=$resultado->fetch_assoc();
                $vecComent[$numFila-1]['fechaHora']=$vecComent[$numFila-1]['fechaCreacion'];
            }
        
        $resultado->close();
        Modelo::cerrarConexion($unMysqli);
        return $vecComent;
    }
    public static function buscarTemaYseccion($idTema) {
        $vecTema=null;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){
            return null;
        }
        $sql="SELECT * FROM unaWebDB.unaTemas A JOIN unaWebDB.unaSecciones B ON A.idSeccion=B.idSeccion WHERE idTema='".$idTema."'";
        $resultado=$unMysqli->query($sql);
        $vecTema=$resultado->fetch_assoc();
        $resultado->close();
        Modelo::cerrarConexion($unMysqli);
        return $vecTema;
    }    
    public static function ultimosApuntes(){
        $vecApuntes=null;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return null;}
        $sql="SELECT * FROM unaWebDB.unaApuntes JOIN unaWebDB.unaUsuarios ON usuario=idUsuario ORDER BY fechaSubida DESC LIMIT 10";
        $resultado=$unMysqli->query($sql);
        for ($numFila=$resultado->num_rows-1;$numFila>=0;$numFila--){
            $resultado->data_seek($numFila);
            $vecApuntes[$numFila-1]=$resultado->fetch_assoc();
        }
        $resultado->close();
        Modelo::cerrarConexion($unMysqli);
        return $vecApuntes;
    }    
    public static function buscarApuntes($titulo,$autor,$materia) {
        $vecApuntes=null;
        $vecComent=null;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return null;}
        $sql="SELECT * FROM unaWebDB.unaApuntes JOIN unaWebDB.unaUsuarios on idUsuario=usuario WHERE titulo LIKE ('%".$titulo."%') AND autores LIKE ('%".$autor."%') AND materia LIKE ('%".$materia."%') ORDER BY fechaSubida DESC";
        $resultado=$unMysqli->query($sql);
        for ($numFila=$resultado->num_rows-1;$numFila>=0;$numFila--){
            $resultado->data_seek($numFila);
            $vecComent[$numFila-1]=$resultado->fetch_assoc();
        }
        $resultado->close();
        Modelo::cerrarConexion($unMysqli);
        return $vecComent;
    }
    public static function buscarLinkApunte($link){
        $vecApuntes=null;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return null;}
        $sql="SELECT * FROM unaWebDB.unaApuntes WHERE dirurl LIKE ('%".$link."%')";
        $resultado=$unMysqli->query($sql);
        for ($numFila=$resultado->num_rows-1;$numFila>=0;$numFila--){
            $resultado->data_seek($numFila);
            $vecApuntes[$numFila-1]=$resultado->fetch_assoc();
        }
        $resultado->close();
        Modelo::cerrarConexion($unMysqli);
        return $vecApuntes;
    }
    public static function buscarApunteRepe($titulo,$autor){
        $vecApuntes=null;
        $unMysqli=Modelo::conectarDB();
        $titulo=$unMysqli->real_escape_string($titulo);
        $autor=$unMysqli->real_escape_string($autor);
        if($unMysqli==false){return null;}
        $sql="SELECT * FROM unaWebDB.unaApuntes WHERE titulo='".$titulo."' AND autores LIKE('%".$autor."%')";
        $resultado=$unMysqli->query($sql);
        for ($numFila=$resultado->num_rows-1;$numFila>=0;$numFila--){
            $resultado->data_seek($numFila);
            $vecApuntes[$numFila-1]=$resultado->fetch_assoc();
        }
        $resultado->close();
        Modelo::cerrarConexion($unMysqli);
        return $vecApuntes;
    }    
    public static function cargarApunte($titulo,$autor,$materia,$enlace,$idUser) {
        $vecApuntes=null;
        $unMysqli=Modelo::conectarDB();
        $titulo=$unMysqli->real_escape_string($titulo);
        $autor=$unMysqli->real_escape_string($autor);
        $materia=$unMysqli->real_escape_string($materia);
        if($unMysqli==false){return null;}
        $sql="INSERT INTO unaWebDB.unaApuntes (titulo,autores,materia,dirurl,fechaSubida,usuario) VALUES ('".$titulo."','".$autor."','".$materia;
        $sql .= "','".$enlace."',CURRENT_TIMESTAMP(),".$idUser.")";
        $resultado=$unMysqli->query($sql);
        Modelo::cerrarConexion($unMysqli);
    }    
    public static function ultimosCursos(){
        $vecComents=null;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return null;}
        $sql="SELECT * FROM unaWebDB.unaCursos ORDER BY fechaCreacion DESC LIMIT 10";
        echo nl2br('$sql:'.$sql);
        $resultado=$unMysqli->query($sql);
        if ($resultado) {
            for ($numFila=$resultado->num_rows-1;$numFila>=0;$numFila--){
                $resultado->data_seek($numFila);
                $vecComents[$numFila-1]=$resultado->fetch_assoc();
            }
        }
        if (!$resultado) {
            //echo("o fallo o no se encontraron datos");
        }else{
            $resultado->close();
        }
        Modelo::cerrarConexion($unMysqli);
        return $vecComents;
    }    
    public static function cargarCurso($materia,$sede,$catedra,$hora,$cod) {
        $vecApuntes=null;
        $unMysqli=Modelo::conectarDB();
        $sede=$unMysqli->real_escape_string($sede);
        $catedra=$unMysqli->real_escape_string($catedra);
        $materia=$unMysqli->real_escape_string($materia);
        $hora=$unMysqli->real_escape_string($hora);
        $cod=$unMysqli->real_escape_string($cod);
        if($unMysqli==false){return null;}
        $sql="INSERT INTO unaWebDB.unaCursos (nombreMateria,sede,nombreCatedra,horario,codigo,fechaCreacion) VALUES ('";
        $sql .= $materia . "','" . $sede . "','" . $catedra . "','" . $hora . "','" . $cod . "',CURRENT_TIMESTAMP())";
        $resultado=$unMysqli->query($sql);
        Modelo::cerrarConexion($unMysqli);
    }    
    public static function buscarCurso($mat,$cat,$sed,$cod,$hora) {
        $vecApuntes=null;
        $unMysqli=Modelo::conectarDB();
        $sed=$unMysqli->real_escape_string($sed);
        $cat=$unMysqli->real_escape_string($cat);
        $mat=$unMysqli->real_escape_string($mat);
        $hor=$unMysqli->real_escape_string($hora);
        $cod=$unMysqli->real_escape_string($cod);
        if($unMysqli==false){return null;}
        $sql="SELECT * FROM unaWebDB.unaCursos WHERE nombreMateria LIKE ('%".$mat."%') AND nombreCatedra LIKE ('%".$cat."%') AND sede LIKE ('%".$sed."%')";
        $sql.= "AND codigo LIKE ('%".$cod."%') AND horario LIKE ('%".$hor."%') ORDER BY fechaCreacion DESC";
        //echo nl2br("\n sql: ".$sql);
        $resultado=$unMysqli->query($sql);
        for ($numFila=$resultado->num_rows-1;$numFila>=0;$numFila--){
            $resultado->data_seek($numFila);
            $vecApuntes[$numFila]=$resultado->fetch_assoc();
        }
        if(!$resultado){
        }else{
            $resultado->close();
        }
        Modelo::cerrarConexion($unMysqli);
        return $vecApuntes;
    }    
    public static function buscarCursoRepe($mat,$cat,$sed,$cod,$hora) {
        $vecApuntes=null;
        $unMysqli=Modelo::conectarDB();
        $sed=$unMysqli->real_escape_string($sed);
        $cat=$unMysqli->real_escape_string($cat);
        $mat=$unMysqli->real_escape_string($mat);
        $hor=$unMysqli->real_escape_string($hora);
        $cod=$unMysqli->real_escape_string($cod);
        if($unMysqli==false){return null;}
        $sql="SELECT * FROM unaWebDB.unaCursos WHERE nombreMateria='".$mat."' AND nombreCatedra='".$cat."' AND sede='".$sed."'";
        $sql.= "AND codigo='".$cod."' AND horario='".$hor."'";
        echo nl2br("\n sql: ".$sql);
        $resultado=$unMysqli->query($sql);
        for ($numFila=$resultado->num_rows-1;$numFila>=0;$numFila--){
            $resultado->data_seek($numFila);
            $vecApuntes[$numFila-1]=$resultado->fetch_assoc();
        }
        if(!$resultado){
        }else{
            $resultado->close();
        }
        Modelo::cerrarConexion($unMysqli);
        return $vecApuntes;
    }    
    public static function buscarComentsCurso($id) {
        $vecComent=null;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return null;}
        $sql="SELECT * FROM unaWebDB.unaComentarioXcurso A JOIN unaWebDB.unaUsuarios B ON A.idUsuario=B.idUsuario WHERE idCurso=".$id." ORDER BY fechaHora DESC";
        $resultado=$unMysqli->query($sql);
        for ($numFila=$resultado->num_rows-1;$numFila>=0;$numFila--){
            $resultado->data_seek($numFila);
            $vecComent[$numFila-1]=$resultado->fetch_assoc();
        }
        $resultado->close();
        Modelo::cerrarConexion($unMysqli);
        return $vecComent;
    }    
    public static function buscarUnCurso($id) {
        $vecCurso=null;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return null;}
        $sql="SELECT * FROM unaWebDB.unaCursos WHERE idCurso=".$id;
        $resultado=$unMysqli->query($sql);
        $vecCurso=$resultado->fetch_assoc();
        $resultado->close();
        Modelo::cerrarConexion($unMysqli);
        return $vecCurso;
    }    
    public static function eliminarApunte ($id) {
        $vecUser=null;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){
            return null;
        }
        $sql="DELETE FROM unaWebDB.unaApuntes WHERE idApunte='".$id."'";
        
        $resultado=$unMysqli->query($sql);
        Modelo::cerrarConexion($unMysqli);
    }    
    public static function eliminarCurso ($id) {
        $vecUser=null;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){
            return null;
        }
        $sql="DELETE FROM unaWebDB.unaCursos WHERE codigo='".$id."'";
        
        $resultado=$unMysqli->query($sql);
        Modelo::cerrarConexion($unMysqli);
    }
    public static function eliminarComentario ($id) {
        $vecUser=null;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){
            return null;
        }
        $sql="DELETE FROM unaWebDB.unaComentarios WHERE idComentario='".$id."'";        
        $resultado=$unMysqli->query($sql);
        Modelo::cerrarConexion($unMysqli);
    }       
    public static function eliminarComentCatedra ($id) {
        $vecUser=null;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){
            return null;
        }
        $sql="DELETE FROM unaWebDB.unaComentarioXcatedra WHERE idComentario='".$id."'";        
        $resultado=$unMysqli->query($sql);
        Modelo::cerrarConexion($unMysqli);
    } 
    public static function eliminarComentCurso ($id) {
        $vecUser=null;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){
            return null;
        }
        $sql="DELETE FROM unaWebDB.unaComentarioXcurso WHERE idComentario='".$id."'";        
        $resultado=$unMysqli->query($sql);
        Modelo::cerrarConexion($unMysqli);
    } 

    public static function ultimasOpiniones(){
        $vecComents=null;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return null;}
        $sql="SELECT * FROM unaWebDB.unaComentarioXcatedra C JOIN unaWebDB.unaCatedra A ON A.idCatedra=C.idCatedra ORDER BY C.fechaHora DESC LIMIT 10";
        //echo nl2br('$sql:'.$sql);
        $resultado=$unMysqli->query($sql);
        if ($resultado) {
            for ($numFila=$resultado->num_rows-1;$numFila>=0;$numFila--){
                $resultado->data_seek($numFila);
                $vecComents[$numFila-1]=$resultado->fetch_assoc();
            }
        }
        if (!$resultado) {
            //echo("o fallo o no se encontraron datos");
        }else{
            $resultado->close();
        }
        Modelo::cerrarConexion($unMysqli);
        return $vecComents;
    }
    public static function buscarCatedra($mat,$cat){
        $vecData=null;
        $unMysqli=Modelo::conectarDB();
        $cat=$unMysqli->real_escape_string($cat);
        $mat=$unMysqli->real_escape_string($mat);        
        if($unMysqli==false){return null;}
        $sql = "SELECT * FROM unaWebDB.unaCatedra WHERE materia LIKE ('%".$mat."%') AND ";
        $sql .=" (catedra LIKE ('%".$cat."%') OR '".$cat."' LIKE CONCAT('% ',catedra,' %'))";
        //echo nl2br("\n sql: ".$sql);
        $resultado=$unMysqli->query($sql);
        for ($numFila=$resultado->num_rows-1;$numFila>=0;$numFila--){
            $resultado->data_seek($numFila);
            $vecData[$numFila-1]=$resultado->fetch_assoc();
        }
        if(!$resultado){
        }else{
            $resultado->close();
        }
        Modelo::cerrarConexion($unMysqli);
        return $vecData;
    }
    public static function buscarOpinionesCatedra($mat,$cat,$prof){
        $vecData=null;
        $unMysqli=Modelo::conectarDB();
        $cat=$unMysqli->real_escape_string($cat);
        $mat=$unMysqli->real_escape_string($mat);
        $prof=$unMysqli->real_escape_string($prof);
        if($unMysqli==false){return null;}
        $sql="SELECT * FROM unaWebDB.unaCatedra WHERE materia LIKE ('%".$mat."%') AND ";
        $sql .=" catedra LIKE ('%".$cat."%') AND profesores LIKE ('%".$prof."%')";
        //echo nl2br("\n sql: ".$sql);
        $resultado=$unMysqli->query($sql);
        for ($numFila=$resultado->num_rows-1;$numFila>=0;$numFila--){
            $resultado->data_seek($numFila);
            $vecData[$numFila-1]=$resultado->fetch_assoc();
        }
        if(!$resultado){
        }else{
            $resultado->close();
        }
        Modelo::cerrarConexion($unMysqli);
        return $vecData;
    }
    public static function cargarOpinionCatedra($materia,$catedra,$profesor) {
        $unMysqli=Modelo::conectarDB();
        $cat=$unMysqli->real_escape_string($catedra);
        $mat=$unMysqli->real_escape_string($materia);
        $prof=$unMysqli->real_escape_string($profesor);
        
        if($unMysqli==false){return null;}
        $sql="INSERT INTO unaWebDB.unaCatedra (materia,catedra,profesores) VALUES ('";
        $sql .= $mat . "','" . $cat . "','" . $prof."')";
        echo nl2br("\n sql: ".$sql);
        $resultado=$unMysqli->query($sql);
        Modelo::cerrarConexion($unMysqli);
    }
    public static function buscarHiloOpinion($id) {
        $vecData=null;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return null;}
        $sql="SELECT * FROM unaWebDB.unaCatedra WHERE idCatedra=".$id;
        $resultado=$unMysqli->query($sql);
        $vecData=$resultado->fetch_assoc();
        $resultado->close();
        Modelo::cerrarConexion($unMysqli);
        return $vecData;
    }
    public static function buscarComentsCatedra($id) {
        $vecComents=null;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return null;}
        $sql="SELECT * FROM unaWebDB.unaCatedra C JOIN unaWebDB.unaComentarioXcatedra A ON A.idCatedra=C.idCatedra WHERE A.idCatedra=".$id." ORDER BY A.fechaHora DESC";
        //echo nl2br('$sql:'.$sql);
        $resultado=$unMysqli->query($sql);
        if ($resultado) {
            for ($numFila=$resultado->num_rows-1;$numFila>=0;$numFila--){
                $resultado->data_seek($numFila);
                $vecComents[$numFila-1]=$resultado->fetch_assoc();
            }
        }
        if (!$resultado) {
            //echo("o fallo o no se encontraron datos");
        }else{
            $resultado->close();
        }
        Modelo::cerrarConexion($unMysqli);
        return $vecComents;
    }
    public static function comentarCatedra($idUsuario, $comentario, $idCatedra) {
        $unMysqli=Modelo::conectarDB();
        $coment=$unMysqli->real_escape_string($comentario);
        if($unMysqli==false){return null;}
        $sql="INSERT INTO unaWebDB.unaComentarioXcatedra (contenido,fechaHora,idUsuario,idCatedra) VALUES ('";
        $sql .= $coment . "',CURRENT_TIMESTAMP()," . $idUsuario. "," . $idCatedra.")";
        //echo nl2br("\n sql: ".$sql);
        $resultado=$unMysqli->query($sql);
        Modelo::cerrarConexion($unMysqli);
    }
    public static function comentarCurso($idUsuario, $comentario, $idCurso) {
        $unMysqli=Modelo::conectarDB();
        $coment=$unMysqli->real_escape_string($comentario);
        if($unMysqli==false){return null;}
        $sql="INSERT INTO unaWebDB.unaComentarioXcurso (contenido,fechaHora,idUsuario,idCurso) VALUES ('";
        $sql .= $coment . "',CURRENT_TIMESTAMP()," . $idUsuario. "," . $idCurso.")";
        //echo nl2br("\n sql: ".$sql);
        $resultado=$unMysqli->query($sql);
        Modelo::cerrarConexion($unMysqli);
    }

    public static function buscarHiloCatedra($materia,$catedra,$profesor){
        $vecData=null;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return null;}
        $sql="SELECT * FROM unaWebDB.unaCatedra WHERE materia='".$materia."' AND catedra='".$catedra."' AND profesores='".$profesor."'";
        $resultado=$unMysqli->query($sql);
        $vecData=$resultado->fetch_assoc();
        $resultado->close();
        Modelo::cerrarConexion($unMysqli);
        return $vecData;
    }
    public static function cantComentsDeTema($idTema){
        $cant=0;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return null;}
        $sql="SELECT COUNT(*) FROM unaWebDB.unaComentarios WHERE idTema='".$idTema."'";
        $resultado=$unMysqli->query($sql);
        $cant = $resultado->fetch_assoc();
        $resultado->close();
        Modelo::cerrarConexion($unMysqli);
        return $cant;
    }
    public static function cantComentsDeCurso($idCurso){
        $cant=0;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return null;}
        $sql="SELECT COUNT(*) FROM unaWebDB.unaComentarioXcurso WHERE idCurso='".$idCurso."'";
        $resultado=$unMysqli->query($sql);
        $cant = $resultado->fetch_assoc();
        $resultado->close();
        Modelo::cerrarConexion($unMysqli);
        return $cant;
    }
    public static function cantComentsDeCatedra($idCatedra){
        $cant=0;
        $unMysqli=Modelo::conectarDB();
        if($unMysqli==false){return null;}
        $sql="SELECT COUNT(*) FROM unaWebDB.unaComentarioXcatedra WHERE idCatedra='".$idCatedra."'";
        //echo nl2br("\n sql: ".$sql);
        $resultado=$unMysqli->query($sql);
        $cant = $resultado->fetch_assoc();        
        $resultado->close();
        Modelo::cerrarConexion($unMysqli);
        return $cant;
    }
}

?>