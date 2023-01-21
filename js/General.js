/*-----Funcionamiento del sidebar------*/
ValidarFlConfiguracion();

function MostrarSidebarPedidos(){
	document.getElementById('nav').style.position='static';
	document.getElementById('Main').style.marginTop='20px';
	document.getElementById('sidebar-pedidos').style.left='0px';
	document.getElementById('contenedor-sidebar').style.display='block';

}
function OcultarSidebarPedidos(){
	document.getElementById('sidebar-pedidos').style.left='-220px';
	document.getElementById('contenedor-sidebar').style.display='none';
	document.getElementById('nav').style.position='fixed';
	document.getElementById('Main').style.marginTop='110px';
}
//Valida Fl de configuración
function ValidarFlConfiguracion(){
strParametros = {
  		"CmdValidarConfiguracion" : 'true',
  	}
  	$.ajax({
  		data:  strParametros,
  		url:   '../../Controller/Configuracion/clsConfiguracion.php',
  		type:  'post', 
  		success:  function (response) {
  			if(response === 'false'){
          var strUrl=location.pathname.split("/");
          if(strUrl[5]!="Configuracion"){
            location.href='../Configuracion/';
          }
  			}
  		},
  		error: function (error) {
  			alert('error; ' + eval(error));
  		}
  	});
  }
  
 //Aceptar configuracion 
//---------------------//
/*----------------------------------------------*/
