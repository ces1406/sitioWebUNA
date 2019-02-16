var main=function(){
	ponerFecha();
	controlarUsuario();
};

function limitaTxt1(){
	if(this.value.length>59){
		this.value=null;
	}else{
		return true;
	}
};
function limitaTxt2(){
	if(this.value.length>39){
		this.value=null;
	}else{
		return true;
	}
};
function limitaTxt3(){
	if(this.value.length>29){
		this.value=null;
	}else{
		return true;
	}
};
function limitaTxt4(){
	if(this.value.length>14){
		this.value=null;
	}else{
		return true;
	}
};
function limitaTxt5(){
	if(this.value.length>39){
		this.value=null;
	}else{
		return true;
	}
};
function controlarUsuario(){
	var materia1=document.getElementById('materia1');
	var catedra1=document.getElementById('catedra1');
	var sede1=document.getElementById('sede1');
	var codCurso1=document.getElementById('codigo1');
	var horario1=document.getElementById('horario1');
	
	var materia2=document.getElementById('materia2');
	var catedra2=document.getElementById('catedra2');
	var sede2=document.getElementById('sede2');
	var codCurso2=document.getElementById('codigo2');
	var horario2=document.getElementById('horario2');
	
	var formu1=document.getElementById('unFormulario1');
	var formu2=document.getElementById('unFormulario2');
	
	materia1.addEventListener('keypress',limitaTxt1,false);
	catedra1.addEventListener('keypress',limitaTxt2,false);
	sede1.addEventListener('keypress',limitaTxt3,false);
	codCurso1.addEventListener('keypress',limitaTxt4,false);
	horario1.addEventListener('keypress',limitaTxt5,false);
	
	if(formu2!=null){
		formu2.addEventListener('submit',chequearCampos2,false);
		materia2.addEventListener('keypress',limitaTxt1,false);
		catedra2.addEventListener('keypress',limitaTxt2,false);
		sede2.addEventListener('keypress',limitaTxt3,false);
		codCurso2.addEventListener('keypress',limitaTxt4,false);
		horario2.addEventListener('keypress',limitaTxt5,false);
	}
}
function chequearCampos2(event){
	var materia2=document.getElementById('materia2');
	var catedra2=document.getElementById('catedra2');
	var sede2=document.getElementById('sede2');
	var codCurso2=document.getElementById('codigo2');
	var horario2=document.getElementById('horario2');
	var cancelar=false;
	
	if(materia2.value.length<5||materia2.value==null){
		var small2=document.getElementById('avisoMateria2');
		small2.textContent='OJO, debes indicar una materia';
		small2.setAttribute("style","color:#FB9209 !important;font-weight:bold !important;");
		titulo.value=null;
		cancelar=true
	}
	if(catedra2.value.length<3||catedra2.value==null){
		var small3=document.getElementById('avisoCatedra2');
		small3.textContent='OJO, debes indicar una catedra';
		small3.setAttribute("style","color:#FB9209 !important;font-weight:bold !important;");
		materia.value=null;
		cancelar=true
	}
	if(sede2.value.length<2||sede2.value==null){
		var small2=document.getElementById('avisoSede2');
		small2.textContent='OJO, debes indicar una sede';
		small2.setAttribute("style","color:#FB9209 !important;font-weight:bold !important;");
		autor.value=null;
		cancelar=true
	}
	if(horario2.value.length<5||horario2.value==null){
		var small2=document.getElementById('avisoHorario2');
		small2.textContent='OJO, debes indicar un horario de cursada';
		small2.setAttribute("style","color:#FB9209 !important;font-weight:bold !important;");
		titulo.value=null;
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