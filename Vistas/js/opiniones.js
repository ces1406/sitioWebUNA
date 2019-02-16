var main=function(){
	ponerFecha();
	controlarUsuario();
	if(document.getElementById('ultimosComentarios')!=null){
		redimensionarImgsUltimosComents();
	};
	if(document.getElementById('opinionesCargadas')!=null){
		redimensionarImgsComentsOdinarios();
	}
	
};

function limitaTxt1(){
	if(this.value.length>59){
		this.value=null;
	}else{
		return true;
	}
};
function limitaTxt2(){
	if(this.value.length>59){
		this.value=null;
	}else{
		return true;
	}
};
function limitaTxt3(){
	if(this.value.length>99){
		this.value=null;
	}else{
		return true;
	}
};
function cambiarDimension(anchor,cadena){
	console.log('cadena llegada: '+cadena.outerHTML);
	console.log('ancho original-alto original: '+cadena.style.width+' x '+cadena.style.height);
	var j=0;
	var ancho='';
	while(/^([0-9])*$/.test(cadena.style.width[j])){
		ancho += cadena.style.width[j];
		j++;
	};
	j=0;
	var alto='';
	while(/^([0-9])*$/.test(cadena.style.height[j])){
		alto += cadena.style.height[j];
		j++;
	};
	var ratio = (ancho / alto);
	console.log('ratio '+ratio);
	var limitAncho=Math.trunc((6*anchor)/10);
	var limitAlto =Math.trunc((7*anchor)/10);
	if(ancho>limitAncho){//corregir ancho
		ancho=limitAncho;
		alto =Math.trunc(ancho/ratio); 					
	}
	if(alto>limitAlto){//corregir alto
		alto=limitAlto;
		ancho=Math.trunc(alto/ratio);
	}
	console.log('ancho altos nuevos: '+ancho+' x '+alto);
	cadena.style.width = ancho+'px';
	cadena.style.height= alto+'px';
}
function redimensionarImgsComentsOdinarios() {	
	var anchor = document.getElementById('opinionesCargadas').clientWidth;
	var comentarios = document.getElementsByClassName('elComentario');
	for (let i = 0; i < comentarios.length; i++) {
		const element0 = comentarios[i];
		var imgs = element0.getElementsByTagName('img');
		for (let n = 0; n < imgs.length; n++) {
			var element1 = imgs[n];	
			cambiarDimension(anchor,element1);		
		}		
	}
}
function redimensionarImgsUltimosComents(){
	console.log('resizeando');
	var mensajes=document.getElementById('ultimosComentarios');
	var anchor = mensajes.clientWidth;
	console.log('anchor: '+mensajes.clientWidth);
	if(mensajes!=null){
		console.log('hay mensajes');
		var imagenes=mensajes.getElementsByTagName('img');
		console.log('cant de imagenes:'+imagenes.length);
		for(var i=0;i<imagenes.length;i++){
			var contenido=imagenes[i];
			cambiarDimension(anchor,contenido);
					
		}
	}
}
function controlarUsuario(){
	var materia1=document.getElementById('materia1');
	var catedra1=document.getElementById('catedra1');
	var profesor1=document.getElementById('profesor1');;
	
	var materia2=document.getElementById('materia2');
	var catedra2=document.getElementById('catedra');
	var profesor2=document.getElementById('profesor');
	
	var formu1=document.getElementById('unFormulario1');
	var formu2=document.getElementById('unFormulario2');
	var formu3=document.getElementById('comentar');
	var comentar=document.getElementById('comentar');
	
	if(comentar!=null){
		console.log('vacia');
		comentar.addEventListener('click',chequearTextArea);
		console.log('area');
		//formu3.addEventListener('submit',chequearTextArea,false);
	}
	if(formu1!=null){
		materia1.addEventListener('keypress',limitaTxt1,false);
		catedra1.addEventListener('keypress',limitaTxt2,false);
		profesor1.addEventListener('keypress',limitaTxt3,false);
	}
	if(formu2!=null){
		formu2.addEventListener('submit',chequearCampos2,false);
		materia2.addEventListener('keypress',limitaTxt1,false);
		catedra2.addEventListener('keypress',limitaTxt2,false);
		profesor2.addEventListener('keypress',limitaTxt3,false);
	}
}
function chequearTextArea(event){
	var aviso=document.getElementById('avisoTxt');
	console.log('el area esta ');
	if (/^\s*$/.test(CKEDITOR.instances.area1.document.getBody().getText())) {
	  	console.log('Vacío');
	  	aviso.textContent='debes escribir algo';
	  	event.preventDefault();
	  	return false;
	} else {
	  	console.log('llena');
	  	return true;
	}
}
function chequearCampos2(event){
	var materia2=document.getElementById('materia');
	var catedra2=document.getElementById('catedra');
	var sede2=document.getElementById('profesor');
	var cancelar=false;
	
	if(materia2.value.length<4||materia2.value==null){
		var small2=document.getElementById('avisoMateria2');
		small2.textContent='OJO, debes indicar una materia';
		small2.setAttribute("style","color:#FB9209 !important;font-weight:bold !important;");
		materia2.value=null;
		cancelar=true
	}
	if(catedra2.value.length<3||catedra2.value==null){
		var small3=document.getElementById('avisoCatedra2');
		small3.textContent='OJO, debes indicar una catedra';
		small3.setAttribute("style","color:#FB9209 !important;font-weight:bold !important;");
		/*materia.value=null;*/
		catedra2.value=null;
		cancelar=true
	}
	if(cancelar){
		event.preventDefault();
		return false;
	}
	return true;
}

function ponerFecha(){
	var var2=document.getElementById("fecha");
	var ahora= new Date();
	var dia=ahora.getDate().toString();
	var mes=ahora.getMonth()+1;
	var anio=ahora.getFullYear().toString();
	var fecha=dia+'-'+mes.toString()+'-'+anio;
	var2.appendChild(document.createTextNode(fecha));
};

window.onload=function(){
	if(document.getElementById('area1')!=null){
		CKEDITOR.replace('area1');
	}
	main();
}