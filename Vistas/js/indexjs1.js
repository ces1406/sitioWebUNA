var fecha={
	dia:0,
	mes:0,
	anio:0,
	actualizar:function(){}
}
var main=function(){
	ultimosComentarios();
	setInterval(ultimosComentarios,2000000);
	ponerFecha();
};

function ultimosComentarios(){
	var peticion=new XMLHttpRequest();
	console.log('pidiendo comentarios');
	peticion.open('get','UltimosComents',true);
	peticion.send(null);
	peticion.onreadystatechange= function (){
		console.log('en estado listo');
		if(peticion.readyState==4){
			console.log('peticion lista');
			if(peticion.status==200){
				console.log('estado 200');
				var rta2= JSON.parse(peticion.responseText);
				console.log('JSON.parse(peticion.responseText:');
				console.log(rta2);
				console.log('peticion.responseText:');
				console.log(peticion.responseText);
				console.log('JSON.parse()->data:\n'+rta2.data);
				console.log('JSON.stringify():'+JSON.stringify(peticion.responseText));
				var listado = document.getElementById('ultimosComentarios');
				listado.innerHTML='';
				var coso =rta2[0];
				console.log('var coso = rta2[0];\ncoso["autor"]='+coso['autor']);
				for( var i=0; i<rta2.length-1; i++ ) {
					item = rta2[i];
					var autor		= item.autor;
					var cadenaCruda = item['contenido'];
					var fecha		= item['fechaHora'];
					var tema		= item['tema'];
					var seccion		= item['seccion'];
					var idTema		= item['idTema'];
					var idCatedra	= item['idCatedra'];
					var idCurso		= item['idCurso'];
					var codCurs		= item['codigoCurso'];
					var catedraCurs = item['catedraCurso'];
					var materiaCurs = item['materiaCurso'];
					var catedraOp	= item['catedraOpinion'];
					var materiaOp	= item['materiaOpinion'];
					var profsOp		= item['profesoresOpinion'];
					console.log('***************************************************************');
					
					var unLi= document.createElement('li');
					var link=document.createElement('a');
					var fecha1=fecha;
					var fecha2=new Date(fecha1);
					var str3='( '+fecha2.getDate().toString()+'/'+fecha2.getMonth().toString()+'/'+fecha2.getFullYear().toString()+' · '+fecha2.getHours().toString()+':'+fecha2.getMinutes().toString()+')';
					var str1='Autor: '+autor;
					var txt1= document.createTextNode(str1);
					var h61= document.createElement('h6');
					h61.appendChild(txt1);
					var h62= document.createElement('h5');
					var h63= document.createElement('small');
					var h64= document.createElement('small');
					var br=document.createElement('br');
					console.log('por 2');

					var vecCom = cadenaCruda.split(';');
					var comentario = vecCom[2];
					comentario = comentario.substring(0,comentario.length-3);
					console.log("IMAGENCRUDA:\n"+cadenaCruda);
					var vecImgs = cadenaCruda.match(/&lt;img /gi);
					if( vecImgs!=null ){
						/* hay imagenes en el comentario */
						console.log("----------->HAY IMAGENES:"+vecImgs.length);
						console.log("primera en pos:"+cadenaCruda.search(/&lt;img /i));
						var inicio=0;
						var clas=' class="img-fluid img-thumbnail mx-auto d-block" ';
						for(i=0;i<vecImgs.length;i++){
							console.log("---->En for con i="+i+'  inicio:'+inicio+'  cadenCruda.length:'+cadenaCruda.length);
							console.log("CADENA TOMADA:\n"+cadenaCruda.substring(inicio,cadenaCruda.length));
							var posImg = cadenaCruda.substring(inicio,cadenaCruda.length).search(/&lt;img /i);
							var posAncho = cadenaCruda.search(/width:/gi);
							var posAlto = cadenaCruda.search(/height:/gi);
							console.log("hay imagen\n posImg:"+posImg+" posAncho:"+posAncho+" imagen posAlto:"+posAlto);
														
							var esFluid = cadenaCruda.substring(inicio,cadenaCruda.length).search(/class="/g);
							if(esFluid === -1){
								console.log("NO ES FLUID");
								cadenaCruda = cadenaCruda.substring(0,posImg+7+inicio)+clas+cadenaCruda.substring(posImg+7+inicio,cadenaCruda.length);
								console.log("cadenaCruda con class incluido: "+cadenaCruda);
							}
							inicio = posImg + 56 ;
						}
					}
					/*var posImg = cadenaCruda.search(/&lt;img /i);
					if (posImg != -1){
						var posAncho = cadenaCruda.search(/width:/gi);
						var posAlto = cadenaCruda.search(/height:/gi);
						console.log("hay imagen\n posImg:"+posImg+" posAncho:"+posAncho+" imagen posAlto:"+posAlto);
						var esFluid = cadenaCruda.search(/class="/g);
						if(esFluid === -1){
							console.log("NO ES FLUID");
							var clas=' class="img-fluid" ';
							cadenaCruda = cadenaCruda.substring(0,posImg+7)+clas+cadenaCruda.substring(posImg+7,cadenaCruda.length);
							console.log("cadenaCruda: "+cadenaCruda);
						}
					}*/
					var coment = cadenaCruda;
					//console.log('***********************************ncoment antes:\n'+coment);
					coment=coment.replace(/&lt;/g,'<');
					coment=coment.replace(/&gt;/g,'>');
					coment=coment.replace(/&quot;/g,'"');
					//console.log('coment despues:'+coment);

					/*if( posAncho!=-1){
						console.log('por 3');
						var posAlto=coment.indexOf('height:');
						//console.log("alto:"+coment[posAlto]+"|"+coment[posAlto+1]+"|"+coment[posAlto+2]+'|'+coment[posAlto+3]+'|'+coment[posAlto+4]+'|'+coment[posAlto+5]+'|'+ coment[posAlto+6]+'|'+coment[posAlto+7]+'|'+coment[posAlto+8]+'|'+coment[posAlto+9]+'|'+coment[posAlto+10])
						//console.log("ancho:"+coment[posAncho]+"|"+coment[posAncho+1]+"|"+coment[posAncho+2]+'|'+coment[posAncho+3]+'|'+coment[posAncho+4]+'|'+coment[posAncho+5]+'|'+ coment[posAncho+6]+'|'+coment[posAncho+7]+'|'+coment[posAncho+8]+'|'+coment[posAncho+9]+'|'+coment[posAncho+10]);
						var a=true;
						var h1=6;
						var h2=7;
						while(a){
							if(isNaN(coment[posAncho+h1])){
								a=false;
							}else{
								//console.log("charAncho["+(posAncho+h1).toString()+']:'+coment[posAncho+h1]);
								h1++;
							}
						}
						var ancho=coment.slice(posAncho+6,posAncho+h1);
						a=true;
						while(a){
							if(isNaN(coment[posAlto+h2])){
								a=false;
							}else{
								//console.log("charAlto["+(posAlto+h2).toString()+']'+coment[posAlto+h2]);
								h2++;
							}
						}
						var alto=coment.slice(posAlto+7,posAlto+h2);
						numAncho=Number(ancho);
						numAlto=Number(alto);
						var nuevoAncho=0;
						var nuevoAlto=0;
						//console.log("alto-->String:"+alto+"-Number:"+numAlto+" ancho-->String:"+ancho+"-Number:"+numAncho);
						if(numAncho>300){
							//console.log("modificar anchura");
							nuevoAncho=300;
							nuevoAlto=parseInt(300*numAlto/numAncho);
							//console.log("coment viejo:"+coment);
							//console.log("hay que reemplazar:"+ancho+" por:"+nuevoAncho.toString());
							//console.log("hay que reemplazar:"+alto+" por:"+nuevoAlto.toString());
							coment=coment.replace('width:'+ancho,'width:'+nuevoAncho.toString());
							coment=coment.replace('height:'+alto,'height:'+nuevoAlto.toString());
							//console.log("coment nuevo:"+coment);
						}
						if(nuevoAlto>600){
							//console.log("modificar altura");
							nuevoAlto=600;
							nuevoAncho=parseInt(600*numAncho/numAlto);
							//*console.log("coment viejo:"+coment);
							//console.log("hay que reemplazar:"+ancho+" por:"+nuevoAncho.toString());
							//console.log("hay que reemplazar:"+alto+" por:"+nuevoAlto.toString());
							coment=coment.replace('width:'+ancho,'width:'+nuevoAncho.toString());
							coment=coment.replace('height:'+alto,'height:'+nuevoAlto.toString());
							//console.log("coment nuevo:"+coment);
						}
						//console.log("------------------>SE DETECTARON WIDTH Y HEITGH");
					}*/

					h62.innerHTML=coment;
					console.log('por 4');
					var str31=document.createTextNode(str3);
					h63.appendChild(str31);
					
					if(idTema!=null && idTema.length!=0){
						console.log('ES UN COMENTARIO COMUN');
						var temSec=document.createTextNode('Seccion: '+seccion);
						var temSec2=document.createTextNode('Tema: '+tema);
						var enter=document.createElement('br');
						h64.appendChild(temSec);
						h64.appendChild(enter);
						h64.appendChild(temSec2);
						link.setAttribute('href','/Seccion/irTema/'+idTema+'/1');
					}else if(idCurso!=null && idCurso!=''){
						console.log('ES UN COMENTARIO DE CURSO');
						var curso=document.createTextNode('Seccion: '+seccion);
						var curso2=document.createTextNode('Curso Codigo: '+codCurs+'Materia: '+materiaCurs+' Catedra:'+catedraCurs);
						var enter=document.createElement('br');
						h64.appendChild(curso);
						h64.appendChild(enter);
						h64.appendChild(curso2);
						link.setAttribute('href','/Seccion/irTema/'+idCurso+'/1');
					}else  if(idCatedra!=null && idCatedra!=''){
						console.log('ES UN COMENTARIO DE OPINION DE CATEDRA');
						var opinion=document.createTextNode('Seccion: '+seccion);
						var opinion2=document.createTextNode('Materia: '+materiaOp+' Catedra: '+catedraOp+' Profesores:'+profsOp);
						var enter=document.createElement('br');
						h64.appendChild(opinion);
						h64.appendChild(enter);
						h64.appendChild(opinion2);
						link.setAttribute('href','/Seccion/irHiloOpinion/'+idCatedra+'/1');
					}
					//h64.setAttribute('style',"line-height:1.0 !important");
					
					var imag=document.createElement('img');
					imag.setAttribute('src','/Vistas/imagenes/separador.png');
					imag.setAttribute('class','separador');
					unLi.appendChild(h61);
					
					
					//link.appendChild(h62);
					link.appendChild(h64);
					link.appendChild(br);
					link.appendChild(h63);
					link.appendChild(imag);
					unLi.appendChild(h62);
					unLi.appendChild(link);
					
					listado.appendChild(unLi);
				}
			}
		}
	}
};

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
	main();
}