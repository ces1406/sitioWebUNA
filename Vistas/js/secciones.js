var main=function(){
	ultimosComentarios();
	ponerFecha();
	setInterval(ultimosComentarios,20000);
};

function ultimosComentarios(){
	var peticion=new XMLHttpRequest();
	peticion.open('get','UltimosComents',true);
	peticion.send(null);
	peticion.onreadystatechange= function (){
		if(peticion.readyState==4){
			if(peticion.status==200){
				var listado = document.getElementById('ultimosComentarios');
				listado.innerHTML='';
				var rta = peticion.responseXML.childNodes[0];
				for(var i=rta.getElementsByTagName('comentario').length-1;i>=0;i--){
					var item=rta.childNodes[i*2+1];
					var autor=item.getElementsByTagName('autor');
					var cadenaCruda=item.getElementsByTagName('contenido');
					var fecha=item.getElementsByTagName('fechaHora');
					var tema=item.getElementsByTagName('tema');
					var seccion=item.getElementsByTagName('seccion');
					var idTema=item.getElementsByTagName('idTema');
					var unLi= document.createElement('li');
					var link=document.createElement('a');
					
					var fecha1=fecha[0].textContent;
					console.log("fecha:"+fecha1);
					var fecha2=new Date(fecha1);
					var str3='( '+fecha2.getDate().toString()+'/'+fecha2.getMonth().toString()+'/'+fecha2.getFullYear().toString()+'  '+fecha2.getHours().toString()+':'+fecha2.getMinutes().toString()+' )';
					
					var str1='Autor: '+autor[0].textContent;
					var txt1= document.createTextNode(str1);
					var h61= document.createElement('h6');
					h61.appendChild(txt1);
					
					var h62= document.createElement('small');
					var h63= document.createElement('small');
					var h64= document.createElement('small');
					var br=document.createElement('br');
		
					var coment=cadenaCruda[0].textContent;
					var posAncho=coment.indexOf('width:');
					if( posAncho!=-1){
						var posAlto=coment.indexOf('height:');
						console.log("alto:"+coment[posAlto]+"|"+coment[posAlto+1]+"|"+coment[posAlto+2]+'|'+coment[posAlto+3]+'|'+coment[posAlto+4]+'|'+coment[posAlto+5]+'|'+ coment[posAlto+6]+'|'+coment[posAlto+7]+'|'+coment[posAlto+8]+'|'+coment[posAlto+9]+'|'+coment[posAlto+10])
						console.log("ancho:"+coment[posAncho]+"|"+coment[posAncho+1]+"|"+coment[posAncho+2]+'|'+coment[posAncho+3]+'|'+coment[posAncho+4]+'|'+coment[posAncho+5]+'|'+ coment[posAncho+6]+'|'+coment[posAncho+7]+'|'+coment[posAncho+8]+'|'+coment[posAncho+9]+'|'+coment[posAncho+10]);
						var a=true;
						var h1=6;
						var h2=7;
						while(a){
							if(isNaN(coment[posAncho+h1])){
								a=false;
							}else{
								console.log("charAncho["+(posAncho+h1).toString()+']:'+coment[posAncho+h1]);
								h1++;
							}
						}
						var ancho=coment.slice(posAncho+6,posAncho+h1);
						a=true;
						while(a){
							if(isNaN(coment[posAlto+h2])){
								a=false;
							}else{
								console.log("charAlto["+(posAlto+h2).toString()+']'+coment[posAlto+h2]);
								h2++;
							}
						}
						var alto=coment.slice(posAlto+7,posAlto+h2);
						numAncho=Number(ancho);
						numAlto=Number(alto);
						var nuevoAncho=0;
						var nuevoAlto=0;
						console.log("alto-->String:"+alto+"-Number:"+numAlto+" ancho-->String:"+ancho+"-Number:"+numAncho);
						if(numAncho>300){
							console.log("modificar anchura");
							nuevoAncho=300;
							nuevoAlto=parseInt(300*numAlto/numAncho);
							console.log("coment viejo:"+coment);
							console.log("hay que reemplazar:"+ancho+" por:"+nuevoAncho.toString());
							console.log("hay que reemplazar:"+alto+" por:"+nuevoAlto.toString());
							coment=coment.replace('width:'+ancho,'width:'+nuevoAncho.toString());
							coment=coment.replace('height:'+alto,'height:'+nuevoAlto.toString());
							console.log("coment nuevo:"+coment);
						}
						if(nuevoAlto>600){
							console.log("modificar altura");
							nuevoAlto=600;
							nuevoAncho=parseInt(600*numAncho/numAlto);
							console.log("coment viejo:"+coment);
							console.log("hay que reemplazar:"+ancho+" por:"+nuevoAncho.toString());
							console.log("hay que reemplazar:"+alto+" por:"+nuevoAlto.toString());
							coment=coment.replace('width:'+ancho,'width:'+nuevoAncho.toString());
							coment=coment.replace('height:'+alto,'height:'+nuevoAlto.toString());
							console.log("coment nuevo:"+coment);
						}
						console.log("------------------>SE DETECTARON WIDTH Y HEITGH");
						
					}
					h62.innerHTML=coment;
					
					var str31=document.createTextNode(str3);
					h63.appendChild(str31);
					
					var temSec=document.createTextNode('Seccion: '+seccion[0].textContent+' - Tema: '+tema[0].textContent);
					h64.appendChild(temSec);
					
					var imag=document.createElement('img');
					imag.setAttribute('src','/Vistas/imagenes/separador.png');
					imag.setAttribute('class','separador');
					unLi.appendChild(h61);
					
					link.setAttribute('href','/Seccion/irTema/'+idTema[0].textContent+'/1')
					link.appendChild(h62);
					link.appendChild(h64);
					link.appendChild(br);
					link.appendChild(h63);
					link.appendChild(imag);
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