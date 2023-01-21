window.onload = function() {
	ValidarFlConfiguracion();	
}
//Listar Vendedores
function GetVendedores(){
	strParametros = {
		"CmdGetVendedores" : 'true'
	}
	$.ajax({
		data: strParametros,
		url: '../../Controller/Configuracion/clsConfiguracion.php',
		type: 'post', 
		success:  function (response) {
			var ddlVendedores=document.getElementById('ddlVendedores');
			var jsDatos=JSON.parse(response);
			var strCtnVendedores='';
			for(var i in jsDatos){
				if(jsDatos[i]===false){
					break;
				}
				strDatos=jsDatos[i].split(",");
				strCtnVendedores+="<option value='"+strDatos[0]+"'>"+strDatos[1]+"</option>";
			}
			ddlVendedores.innerHTML=strCtnVendedores;
		},
		error: function (error) {
			alert('error; ' + eval(error));
		}
	});
}

//Aceptar vendedor configuración
function AceptarVendedorConfiguracion(){
	var ddlVendedores=document.getElementById('ddlVendedores').innerHTML;
	strParametros = {
		"CmdAceptarVendConfiguracion" : 'true',
		"strCedulaVd" : document.getElementById('ddlVendedores').value.trim(),
		"strNombreVd" : ddlVendedores
	}
	$.ajax({
		data: strParametros,
		url: '../../Controller/Configuracion/clsConfiguracion.php',
		type: 'post', 
		success:  function (response) {
			if(response==1){
				document.getElementById('lblVendedorSidebar').innerHTML=$("#ddlVendedores option:selected").text();
				swal('Vendedor seleccionado con éxito.');
			}
		},
		error: function (error) {
			alert('error; ' + eval(error));
		}
	});
}
//Acceso a configuración
function  PedirClaveDeAccesoCf(){
	swal({
		title: 'Clave',
		input: 'password',
		inputAttributes: {
			autocapitalize: 'off',
			id: 'title'
		},
		confirmButtonText: 'Aceptar',
		showLoaderOnConfirm: false,
		allowOutsideClick: false
	}).then((result) => {
		if (result.value) {
			strParametros = {
				"CmdValidarClaveAccesoCf" : 'true',
				"strClave" : result.value
			}
			$.ajax({
				data: strParametros,
				url: '../../Controller/Configuracion/clsConfiguracion.php',
				type: 'post', 
				success:  function (response) {
					if(response=='false'){
						PedirClaveDeAccesoCf();
					}
				},
				error: function (error) {
					alert('error; ' + eval(error));
				}
			});
		}else{
			location.href='../Pedidos/';
		}
	})
}
//Valdiar Configuración file
function ValidarFlConfiguracion(){
	strParametros = {
		"CmdValidarConfiguracion" : 'true',
	}
	$.ajax({
		data:  strParametros,
		url:   '../../Controller/Configuracion/clsConfiguracion.php',
		type:  'post', 
		success:  function (response) {
			GetVendedores();
			if(response === 'true'){
				PedirClaveDeAccesoCf();
			}
		},
		error: function (error) {
			alert('error; ' + eval(error));
		}
	});
}