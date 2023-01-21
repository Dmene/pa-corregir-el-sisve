	/*------- GENERAL ---------------*/
	window.onload = function() {

		document.getElementById('ddlMesPedido').value=(GetMes());
		ObtenerListaPedidosFinalizados();
		ObtenerListaPedidosEnviados();
  	//Buscador table pedidos finalizados
  	$("#txtBusquedaPdFinalizados").keyup(function(){
  		_this = this;
  		$.each($("#tblPdFinalizados tbody tr"), function() {
  			if($(this).text().toLowerCase().indexOf($(_this).val().toLowerCase()) === -1)
  				$(this).hide();
  			else
  				$(this).show();
  		});
  	});
  	$("#txtBusquedaPdEnviados").keyup(function(){
  		_this = this;
  		$.each($("#tblPdEnviados tbody tr"), function() {
  			if($(this).text().toLowerCase().indexOf($(_this).val().toLowerCase()) === -1)
  				$(this).hide();
  			else
  				$(this).show();
  		});
  	});
  	
  };
  function GetMes(){
  	var today = new Date();
  	var strMes = (today.getMonth()+1);
  	if(strMes<=9){
  		strMes='0'+strMes;
  	}
  	return strMes;
  }
  function Msg(strMsg,strTipoMsg){
  	$.notify({                                     
  		message: '<strong>'+strMsg+'</strong>'
  	},{
  		type: strTipoMsg,
  		placement: {
  			from: "top",
  			align: "right"
  		},
  		z_index: 1031  
  	});
  }
  //Ordenamiento de tabla por NroPedido
  function sortTable(strTabla) {
  	var table, rows, switching, i, x, y, shouldSwitch;
  	table = document.getElementById(strTabla);
  	switching = true;
	  /*Make a loop that will continue until
	  no switching has been done:*/
	  while (switching) {
	    //start by saying: no switching is done:
	    switching = false;
	    rows = table.rows;
	    /*Loop through all table rows (except the
	    first, which contains table headers):*/
	    for (i = 1; i < (rows.length - 1); i++) {
	      //start by saying there should be no switching:
	      shouldSwitch = false;
	      /*Get the two elements you want to compare,
	      one from current row and one from the next:*/
	      x = rows[i].getElementsByTagName("TD")[0];
	      y = rows[i + 1].getElementsByTagName("TD")[0];
	      //check if the two rows should switch place:
	      if (Number(x.innerHTML) > Number(y.innerHTML)) {
	        //if so, mark as a switch and break the loop:
	        shouldSwitch = true;
	        break;
	    }
	}
	if (shouldSwitch) {
	      /*If a switch has been marked, make the switch
	      and mark that a switch has been done:*/
	      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
	      switching = true;
	  }
	}
}

/*---------------------Estado Pedidos -----------------------------*/

//Metodo para obtener lista de pedidos finalizados de acuerdo mes y año
function ObtenerListaPedidosFinalizados(){
	//Tipo de pedidos 0 = pedidosfinalizados
	strParametros = {
		"CmdGetPedidos" : 'true',
		"intTipoGetPedido" : "0",
		"strMesPd" : '0',
		"strAnnoPd" : '1999'
	}
	$.ajax({
		data: strParametros,
		url: '../../Controller/EstadoPedidos/clsEstadoPedidos.php',
		type: 'post', 
		success:  function (response) {
			var tblCtnPdFinalizado='';
			var tblBodyPdFinalizados=document.getElementById('tblBodyPdFinalizados');
			var btnEnviarTdPdFinalizados=document.getElementById('btnEnviarTdPdFinalizados');
			if(response.length<=2){
				tblCtnPdFinalizado="<tr><td><strong>No tiene pedidos finalizados.</strong></td></tr>";
				tblBodyPdFinalizados.innerHTML=tblCtnPdFinalizado;
				btnEnviarTdPdFinalizados.disabled=true;
				return;
			}
			var jsDatos=JSON.parse(response);
			btnEnviarTdPdFinalizados.disabled=false;
			var strDatos='';
			var strTipoPrecio='';
			for(var i in jsDatos){
				strDatos=jsDatos[i].split('%');
				//Tipo de precio
				if(strDatos[10]==5){
					strTipoPrecio='<span class="badge badge-secondary font-size-3">IM</span>';
				}
				//Construyendo los datos de los pedido finalizados
				tblCtnPdFinalizado+="<tr><td>"+strDatos[0]+"</td><td>"
				+strDatos[1]+"</td><td>"+strDatos[2]+"</td>"+
				"<td>"+strDatos[13]+"</td><td>"+strDatos[12]+
				"</td><td>"+strTipoPrecio+"</td><td>"+strDatos[14]+"</td>"+
				"<td style='display:none'><label id='lblFactMarcado"+strDatos[0]+"'>"+strDatos[17]+"</label></td>"+
				"<td><button class='btn btnSisve-primary' title='Visualizar Items' onclick='GetDetallePedido("+strDatos[0]+")'><i class='fas fa-search'></i></button>&nbsp;"+
				"<button class='btn btnSisve-primary' title='Ir al pedido' onclick='RedireccionarParaPedido("+strDatos[0]+")'><i class='fas fa-list-alt'></i></button>&nbsp;"+
				"<button class='btn btnSisve-primary' title='Quitar de lista' onclick='ValidacionQuitarPedidoFinalizado("+strDatos[0]+")'><i class='far fa-times-circle'></i></button>&nbsp;"+
				"<button  class='btn btnSisve-primary' title='Enviar pedido' onclick='ValidarMarcado("+strDatos[0]+")'><i class='far fa-share-square'></i></button>"+
				"</td><td class='d-none' id='txtObservacion"+strDatos[0]+"'>"+strDatos[16]+"</td></tr>";
			}
			tblBodyPdFinalizados.innerHTML=tblCtnPdFinalizado;
			sortTable('tblPdFinalizados');
			$(function () {
				$('[data-toggle="tooltip"]').tooltip()
			})
		},
		error: function (error) {
			alert('error; ' + eval(error));
		}
	});
}
//Obtener pedidos enviados a inmodafantasy
function ObtenerListaPedidosEnviados(){
	//Tipo de pedidos 1 = pedidosenviados
	strParametros = {
		"CmdGetPedidos" : 'true',
		"intTipoGetPedido" : "1",
		"strMesPd" : document.getElementById('ddlMesPedido').value.trim(),
		"strAnnoPd" : document.getElementById('ddlAnnoPedido').value.trim()
	}
	$.ajax({
		data: strParametros,
		url: '../../Controller/EstadoPedidos/clsEstadoPedidos.php',
		type: 'post', 
		success:  function (response) {
			var tblCtnPdFinalizado='';
			var tblBodyPdEnviados=document.getElementById('tblBodyPdEnviados');
			if(response.length<=2){
				tblCtnPdFinalizado="<tr><td><strong>No tiene pedidos enviados.</strong></td></tr>";
				tblBodyPdEnviados.innerHTML=tblCtnPdFinalizado;
				return;
			}
			var jsDatos=JSON.parse(response);
			var strDatos='';
			var strTipoPrecio='';
			for(var i in jsDatos){
				strDatos=jsDatos[i].split('%');
				//Tipo de precio
				if(strDatos[10]==5){
					strTipoPrecio='<span class="badge badge-secondary font-size-3">IM</span>';
				}
				//Construyendo los datos de los pedido finalizados
				tblCtnPdFinalizado+="<tr><td>"+strDatos[0]+"</td><td>"
				+strDatos[1]+"</td><td>"+strDatos[2]+"</td>"+
				"<td>"+strDatos[13]+"</td><td>"+strDatos[12]+
				"</td><td>"+strDatos[15]+"</td><td>"+strTipoPrecio+"</td><td>"+strDatos[14]+"</td>"+
				"<td><button class='btn btnSisve-primary' title='Visualizar Items' onclick='GetDetallePedido("+strDatos[0]+")'><i class='fas fa-search'></i></button>&nbsp;"+
				"<button class='btn btnSisve-primary' title='Ir al pedido' onclick='RedireccionarParaPedido("+strDatos[0]+")'><i class='fas fa-list-alt'></i></button>&nbsp;"+
				"</td><td class='d-none' id='txtObservacion"+strDatos[0]+"'>"+strDatos[16]+"</td></tr>";
			}
			tblBodyPdEnviados.innerHTML=tblCtnPdFinalizado;
			sortTable('tblPdEnviados');
			$(function () {
				$('[data-toggle="tooltip"]').tooltip()
			})
		},
		error: function (error) {
			alert('error; ' + eval(error));
		}
	});
}
//Metodo obtener detalle de un pedido
function GetDetallePedido(intNroPedido){
	strParametros = {
		"CmdGetDetallePedido" : 'true',
		"intNroPedido" : intNroPedido
	}
	$.ajax({
		data: strParametros,
		url: '../../Controller/EstadoPedidos/clsEstadoPedidos.php',
		type: 'post', 
		success:  function (response) {
			var tblBodyMdDetPedido=document.getElementById('tblBodyMdDetPedido');
			var txtAreaObservacion=document.getElementById('txtObservacionPd');
			var txtTblObservacion=document.getElementById('txtObservacion'+intNroPedido);
			var strCtnDetPedido='';
			if(response.length<=2){
				strCtnDetPedido="<tr><td><strong>No tiene items.</strong></td></tr>";
				tblBodyMdDetPedido.innerHTML=strCtnDetPedido;
				return;
			}
			//Convirtiendo el dellate del pedido json a array
			var jsDatos=JSON.parse(response);
			for(var i in jsDatos){
				strDatos=jsDatos[i].split('%');
				strCtnDetPedido+="<tr>"+
				"<td><img src='../../../../../ownCloud/fotos_nube/"+strDatos[0]+".jpg' width='200'></td>"+
				"<td>"+strDatos[0]+"</td>"+
				"<td>"+strDatos[2]+"</td>"+
				"<td>"+strDatos[9]+"</td>"+
				"<td>"+strDatos[1]+"</td>"+
				"<td>"+strDatos[3]+"</td>"+
				"<td>"+strDatos[4]+"</td>"+
				"<td>"+strDatos[5]+"</td>"
				"</tr>";
			}
			txtAreaObservacion.innerHTML=txtTblObservacion.innerHTML.trim();
			tblBodyMdDetPedido.innerHTML=strCtnDetPedido;
			$('#tblMdDetPedido').modal('show');
		},
		error: function (error) {
			alert('error; ' + eval(error));
		}
	});
}
//Metodo para cambiar el estado del pedido 2 a 1 para poder seguir creando items al cliente
function ValidacionQuitarPedidoFinalizado(intNroPedido){
	Swal.fire({
		title: 'Desea quitar el pedido de la lista de finalizado?',
		text: "Cambiara el estado del pedido para su respectiva modificación antes de ser enviado a Inmodafantasy.",
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Si',
		cancelButtonText: 'No'
	}).then((result) => {
		if (result.value) {
			QuitarFinalizadoPedido(intNroPedido);
		}
	})
}

function QuitarFinalizadoPedido(intNroPedido){
	var strParametros = {
		"CmdQuitarFinalizadoPedido" : 'true',
		"intNroPedido": intNroPedido
	}
	$.ajax({
		data:  strParametros,
		url:    '../../Controller/EstadoPedidos/clsEstadoPedidos.php',
		type:  'post',
		success:  function (response) {
			Msg(response,'success');
			ObtenerListaPedidosFinalizados();
		},
		error: function (error) {
			alert('error; ' + eval(error));
		}
	});
}
//Redireccionar vista al pedido
function RedireccionarParaPedido(intNroPedido){
	location.href=(window.location.protocol+"//"+window.location.host+"/ownCloud/data/Sisve/View/Pedidos/?intNroPedido="+intNroPedido);
}

//Envio de pedido
function EnviarPedidoFinalizado(intNroPedido,blnTipoDeEnvio){

	//Tipo de envio 0 y 1
	//0 significa envio uno a uno
	//1 Significa envio de todo los pedidos finalizados
	var lblFactCorreo=document.getElementById('lblFactCorreo');
	var lblFactTelefono=document.getElementById('lblFactTelefono');
	var lblFactCelular=document.getElementById('lblFactCelular');
	var lblFactCiudad=document.getElementById('lblFactCiudad');

	var strParametros = {
		"CmdEnvioDePedidoWs" : 'true',
		"blnTipoDeEnvio" : blnTipoDeEnvio,
		"intNroPedido" : intNroPedido,
		"strFactCorreo" : lblFactCorreo.value.trim(),
		"strFactCelular" : lblFactCelular.value.trim(),
		"strFactTelefono" : lblFactTelefono.value.trim(),
		"strFactCiudad" : lblFactCiudad.value.trim()
	}
	$.ajax({
		data:  strParametros,
		url:    '../../Controller/EstadoPedidos/clsEstadoPedidos.php',
		type:  'post',
		success:  function (response) {
			if(response==='1'){
				var strTexto='Pedido '+intNroPedido+' enviado con éxito.';
				if(blnTipoDeEnvio==='0'){
					strTexto='Pedidos enviados con éxito.';
				}
				Msg(strTexto,'success');
			}else if(response==='3'){
				var strTexto='Pedido '+intNroPedido+' ya fue enviado.Recargue el pedido.';
				Msg(strTexto,'success');
				return;
			}else{
				Msg('Ocurrio un error enviado los datos. Intente más tarde.','danger');
				return;
			}
			console.log(response);
			ObtenerListaPedidosFinalizados();
			ObtenerListaPedidosEnviados();
			lblFactCorreo.value='';
			lblFactTelefono.value='';
			lblFactCelular.value='';
			lblFactCiudad.value='';
		},
		error: function (error) {
			alert('error; ' + eval(error));
		}
	});
}
/*
a();
function a(){
	if(navigator.onLine) {
    alert('si');
    // el navegador está conectado a la red
} else {
	alert('no');
    // el navegador NO está conectado a la red
}
}*/
//Verifica si se envia todos los pedidos finalizados a inmoda
function ValidacionEnvioPedidosFinalizados(){
	Swal.fire({
		title: 'Desea enviar todos los pedidos finalizados?',
		html: "Con lleva al envio total de todos los pedidos finalizados a InmodaFantasy.( <strong> Una vez hecho esto nose podra retractar. </strong>)",
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Si',
		cancelButtonText: 'No'
	}).then((result) => {
		if (result.value) {
			//Enviar todos los pedidos finalizados a inmodafantasy
			EnviarPedidoFinalizado('',1);
		}
	})
}

//Verifica si se envia un pedido finalizado a inmoda
function ValidacionEnvioPedidoFinalizado(intNroPedido){
	Swal.fire({
		title: 'Desea enviar el pedido Nro '+intNroPedido+' finalizado?',
		html: "Con lleva al envio del pedido finalizado a InmodaFantasy.( <strong> Una vez hecho esto nose podra retractar. </strong>)",
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Si',
		cancelButtonText: 'No'
	}).then((result) => {
		if (result.value) {
			//Enviar un pedido expecifico
			EnviarPedidoFinalizado(intNroPedido,0);
		}
	})
}
function ValidarFormularioMarcado(){
	var lblFactCorreo=document.getElementById('lblFactCorreo');
	var lblFactTelefono=document.getElementById('lblFactTelefono');
	var lblFactCelular=document.getElementById('lblFactCelular');
	var lblFactCiudad=document.getElementById('lblFactCiudad');

	if(lblFactCorreo.value.trim()==''){
		$.notify({                                     
			message: '<strong>Ingrese correo</strong>'
		},{
			type: 'danger',
			placement: {
				from: "top",
				align: "right"
			},
			z_index: 222031  
		});
		return;
	}
	if(lblFactTelefono.value.trim()==''){
		$.notify({                                     
			message: '<strong>Ingrese telefono.</strong>'
		},{
			type: 'danger',
			placement: {
				from: "top",
				align: "right"
			},
			z_index: 222031    
		});
		return;
	}
	if(lblFactCelular.value.trim()==''){
		$.notify({                                     
			message: '<strong>Ingrese celular</strong>'
		},{
			type: 'danger',
			placement: {
				from: "top",
				align: "right"
			},
			z_index: 222031   
		});
		return;
	}
	if(lblFactCiudad.value.trim()==''){
		$.notify({                                     
			message: '<strong>Ingrese ciudad.</strong>'
		},{
			type: 'danger',
			placement: {
				from: "top",
				align: "right"
			},
			z_index: 222031   
		});
		return;
	}
	$('#ModalFactE').modal('hide');
	var lblIdPedido=document.getElementById('lblIdPedido').innerHTML.trim();
	ValidacionEnvioPedidoFinalizado(lblIdPedido);
}
function ValidarMarcado(intNroPedido){
	if((document.getElementById('lblFactMarcado'+intNroPedido).innerHTML.trim())==('0')){
		$('#ModalFactE').modal('show');
		document.getElementById('lblIdPedido').innerHTML=intNroPedido;
	}else{
		ValidacionEnvioPedidoFinalizado(intNroPedido);
	}
}
