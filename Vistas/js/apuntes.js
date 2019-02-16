var main=function(){
	ponerFecha();
	controlarUsuario();
};

function limitaTxt1(){
	if(this.value.length>7){
		this.value=null;
	}else{
		return true;
	}
};
function limitaTxt2(){
	if(this.value.length>29){
		this.value=null;
	}else{
		return true;
	}
};
function limitaTxt3(){
	if(this.value.length>44){
		this.value=null;
	}else{
		return true;
	}
};
function limitaTxt4(){
	if(this.value.length>69){
		this.value=null;
	}else{
		return true;
	}
};
function limitaTxt5(){
	if(this.value.length>144){
		this.value=null;
	}else{
		return true;
	}
};
function controlarUsuario(){
	var materia1=document.getElementById('materia1');
	var titulo1=document.getElementById('titulo1');
	var autor1=document.getElementById('autor1');
	var materia2=document.getElementById('materia2');
	var titulo2=document.getElementById('titulo2');
	var autor2=document.getElementById('autor2');
	var url=document.getElementById('ubicacionUrl');
	var pass1=document.getElementById('password1');
	var formu2=document.getElementById('unFormulario2');
	
	materia1.addEventListener('keypress',limitaTxt2,false);
	autor1.addEventListener('keypress',limitaTxt3,false);
	titulo1.addEventListener('keypress',limitaTxt4,false);
	
	if(formu2!=null){
		formu2.addEventListener('submit',chequearCampos2,false);
		pass1.addEventListener('keypress',limitaTxt1,false);
		materia2.addEventListener('keypress',limitaTxt2,false);
		autor2.addEventListener('keypress',limitaTxt3,false);
		titulo2.addEventListener('keypress',limitaTxt4,false);
		url.addEventListener('keypress',limitaTxt5,false);
	}
}
function chequearCampos2(event){
	var materia=document.getElementById('materia2');
	var titulo=document.getElementById('titulo2');
	var autor=document.getElementById('autor2');
	var url=document.getElementById('ubicacionUrl');
	var pass1=document.getElementById('password1');
	var cancelar=false;
	var expReg= new RegExp('^(https://)www\.mediafire\.com(.+)$'); 
	
	if(pass1.value==null||pass1.value.length<6 || pass1.value.length>8){
		var small1=document.getElementById('avisoPass1');
		small1.textContent='LA CONTRASEÑA DEBEN TENER ENTRE 5 Y 8 CARACTERES';
		small1.setAttribute("style","color:#FB9209 !important;font-weight:bold !important;");
		pass1.value=null;
		cancelar=true;
	}
	if(titulo.value.length<2||titulo.value==null){
		var small2=document.getElementById('avisoTitulo');
		small2.textContent='OJO, debes indicar un titulo';
		small2.setAttribute("style","color:#FB9209 !important;font-weight:bold !important;");
		titulo.value=null;
		cancelar=true
	}
	if(materia.value.length<2||materia.value==null){
		var small3=document.getElementById('avisoMateria');
		small3.textContent='OJO, debes indicar una materia';
		small3.setAttribute("style","color:#FB9209 !important;font-weight:bold !important;");
		materia.value=null;
		cancelar=true
	}
	if(autor.value.length<2||autor.value==null){
		var small2=document.getElementById('avisoAutor');
		small2.textContent='OJO, debes indicar un autor';
		small2.setAttribute("style","color:#FB9209 !important;font-weight:bold !important;");
		autor.value=null;
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

window.onload=main();