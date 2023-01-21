
//------GENERAL 44033710@200.13.230.38/44033710
//Tooltip
window.onload = function () {
  if (navigator.onLine) {
    console.log('online');
  } else {
    console.log('offline');
  }
  //CrearSelectZonas();
  //CrearSelectCiudades();
  $(function () {
    $('[data-toggle="tooltip"]').tooltip()
  });
  $('.ir-arriba').click(function () {
    $('body, html').animate({
      scrollTop: '0px'
    }, 300);
  });
  $(window).scroll(function () {
    if ($(this).scrollTop() > 0) {
      $('.ir-arriba').slideDown(300);
    } else {
      $('.ir-arriba').slideUp(300);
    }
  });
  //tecla esc
  window.addEventListener("keyup", function (event) {
    var codigo = event.keyCode || event.which;
    if (codigo == 27) {
      $('#ModalImg').modal('hide');
    }
  }, false);

};
//Metodo para consultar pedido metodo Get enviado desde estado del pedido
function ConsultaPedidoMetodoGet(intNroPedido) {
  document.getElementById('txtNroPedido').value = intNroPedido;
  ListarPedidosBusqueda();
}
function ValidarMarcado() {
  if ((document.getElementById('lblFactMarcado').value) == ('0')) {
    $('#ModalFactE').modal('show');
  } else {
    ValidacionEnvioPedidoFinalizado();
  }
}
var blnEstado = false;
var strDatosModoFoto = '';
var k = 0;
var intCantidadImg = 0;
function Mensajeria() {
  swal({
    title: 'Señor vendedor es indispensable comunicarse de manera URGENTE con la linea de soporte de la empresa para realizar una actualización, gracias por su comprensión!',
    width: 400,
    padding: '3em',
    backdrop: `
    rgba(0, 0, 0,0.4)
    url("./img/SOPORTE.gif")
    center left
    no-repeat
    `
  });
}
//Metodo para  mostrar los mensajes de notificaciones
function Msg(strMsg, strTipoMsg) {
  $.notify({
    message: '<strong>' + strMsg + '</strong>'
  }, {
    type: strTipoMsg,
    placement: {
      from: "top",
      align: "right"
    },
    z_index: 1031
  });
}

// Seccion Pedidos //

//Metodo para crear un núevo pedido de acuerdo a su consecutivo
function CrearNuevoPedido() {
  strParametros = {
    "CmdCrearNuevoPedido": 'true'
  }
  $.ajax({
    data: strParametros,
    url: '../../Controller/Pedidos/clsPedidos.php',
    type: 'post',
    success: function (response) {
      document.getElementById('txtNroPedido').value = response;
      document.getElementById('lblNroPedido').innerHTML = response;
      document.getElementById('ctnCliente').style.display = 'inline';
      document.getElementById('CtnSeccionPedido').style.display = 'inline';
      Msg('Pedido Nro ' + response + ' iniciado con éxito.', 'success');
      ListarPedidosBusqueda();
    },
    error: function (error) {
      alert('error; ' + eval(error));
    }
  });
}
// ------- //

function txtBusqueda(e) {
  tecla = (document.all) ? e.keyCode : e.which;
  if (tecla == 13) {
    find(document.getElementById('txtBusqueda').value);
  }
  document.getElementById('txtBusqueda').focus();
}


function validar(e) {
  tecla = (document.all) ? e.keyCode : e.which;
  if (tecla == 13) {
    BuscarReferencias(0)
    n = 1;
  }
}

function EnterListarPedidosBusqueda(e) {
  tecla = (document.all) ? e.keyCode : e.which;
  if (tecla == 13) {

    ListarPedidosBusqueda();
  }
}
function txtBusquedaTradicional(e) {
  tecla = (document.all) ? e.keyCode : e.which;
  if (tecla == 13) {
    find(document.getElementById('txtBusquedaTradicional').value);
  }
  document.getElementById('txtBusquedaTradicional').focus();
}
function AgregarProducto(Producto) {
  $('#MdGaleria').modal('hide');
  document.getElementById('body').style.padding = '0px';
  document.getElementById('txtReferencia').value = Producto.trim();
  BuscarReferencia(0);
  document.getElementById('txtReferencia').value = '';
}
$('#ModalImg').bind('keydown', 'ArrowRight', function () {
  if (event.keyCode == "39" && blnEstado) {
    MdDerecha();
  }
  if (event.keyCode == "37" && blnEstado) {
    MdIzquierda();
  }
});




function BuscarReferencia(strRuta) {
  parametros = {
    "btnBuscarReferencia": 'true',
    "txtReferencia": document.getElementById("txtReferencia").value.trim(),
    "intNroPedido": document.getElementById('lblNroPedido').innerHTML.trim()
  }
  document.getElementById("gif").style.display = "block";

  $.ajax({
    data: parametros,
    url: '../../Controller/Pedidos/clsPedidos.php',
    type: 'post',
    success: function (response) {

      document.getElementById("gif").style.display = "none";
      document.getElementById('Modal').innerHTML = response;
      $('#ModalImg').modal({ backdrop: 'static', keyboard: false });
      $('#ModalImg').modal('show');
      document.getElementById('btnDesplazars').style.display = 'none'
      document.getElementById('btnDesplazar2s').style.display = 'none'
      document.getElementById('imgProductos1').style.display = 'none';


      if (strRuta != 0) {
        document.getElementById('imgProductos').src = strRuta;
        document.getElementById('btnDesplazar').style.display = 'inline';
        document.getElementById('btnDesplazar2').style.display = 'inline';
        blnEstado = true;
        console.log("bueno  " + strRuta)
        console.log(parametros)

      } else {
        blnEstado = false;
        document.getElementById('lblLinea').innerHTML = '';
        document.getElementById('btnDesplazar').style.display = 'none';
        document.getElementById('btnDesplazar2').style.display = 'none';
        console.log("malo  " + strRuta)

      }
    },
    error: function (error) {
      alert('error; ' + eval(error));
    }
  })

}




$('#ModalImg').on('shown.bs.modal', function () {
  $("#txtCantidad").focus();
})
function DdlReferencias() {
  parametros = {
    "btnListarReferencias": 'true'


  }
  $.ajax({
    data: parametros,
    url: '../../Controller/Pedidos/clsPedidos.php',
    type: 'post',
    success: function (response) {
      document.getElementById("ddlReferencias").innerHTML = response;
    },
    error: function (error) {
      alert('error; ' + eval(error));
    }
  })
}

function ConversionPrecio(valor) {
  especiales = new Array('N', 'Z', 'Y', 'W', 'V', 'U', 'S', 'R', 'P', 'O');
  normales = new Array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);


  i = 0;
  while (i < especiales.length) {
    valor = valor.split(especiales[i]).join(normales[i]);
    //valor = valor.replace(especiales[i], normales[i]);
    i++
  }

  return valor;
}

function AgregarPedido() {
  //Quitar precio 5 colocar el normal

  //VALIDAR SI SUPERA EL CUPO
  /*if((new Intl.NumberFormat().format(ConversionPrecio($('#lblTotalPedido').html())) > $('#lblClienteCupo').val()) && $('#lblClienteCupo').val() != ""){
    
  }*/
  var num1 = ConversionPrecio($('#lblTotalPedido').html());
  var num2 = $('#lblClienteCupo').val().replace(/,/g, "");
  /*
    if(num1 > num2 && num2 != 0){
      Swal.fire(
        'Cliente ha superado el cupo',
        '',
        'question'
      )
    }
  */
  //VALIDAR SI EL PRODUCTO ESTA EN EL PEDIDO


  var txtPrecio = document.getElementById('txtN');
  if (txtPrecio.value.trim() == '') {
    swal('Ingrese precio del producto');
    txtPrecio.focus();
    return;
  }
  if (ValidarPrecioPorLetra(txtPrecio.value.trim().toUpperCase()) == 1) {
    swal('Precio incorrecto.');
    document.getElementById('txtN').focus();
    return;
  }
  parametros = {
    "btnAgregarPedido": 'true',
    "txtReferenciaAgregar": document.getElementById("txtReferenciaAgregar").innerHTML.trim(),
    "txtEstilo": document.getElementById("txtEstilo").value.trim(),
    "txtDescripcion": document.getElementById("txtDescripcion").innerHTML.trim(),
    "txtColor": document.getElementById("txtColor").value.trim(),
    "txtCantidad": document.getElementById("txtCantidad").value.trim(),
    "txtN": txtPrecio.value.trim(),
    "txtPrecioProducto": document.getElementById('txtPrecioProducto').value.trim(),
    "txtUnidadMedida": document.getElementById('txtUnidadMedida').innerHTML.trim(),
    "blnEstadoPrecio": document.getElementById('blnEstadoPrecio').value.trim(),
    "txtNroPedido": document.getElementById("lblNroPedido").innerHTML.trim(),
    "txtObservacionProducto": document.getElementById('txtAreaProducto').value.trim()
  }
  if (document.getElementById('txtCantidad').value.trim() == '') {
    swal('Ingrese la cantidad del producto a agregar.');
    document.getElementById('txtCantidad').focus();
    return;
  }


  $.ajax({
    data: parametros,
    url: '../../Controller/Pedidos/clsPedidos.php',
    type: 'post',
    success: function (response) {
      if (!(blnEstado)) {
        $('#ModalImg').modal('hide');
      } else {
        $('#ModalImg').modal({ backdrop: 'static', keyboard: false });
      }
      $.notify({
        message: '<strong>' + response + '</strong>'
      }, {
        type: 'info',
        placement: {
          from: "top",
          align: "right"
        },
        z_index: 09999
      });
      ListarPedidos();
      document.getElementById("txtReferencia").value = "";
      document.getElementById("txtReferencia").focus();
    },
    error: function (error) {
      alert('error; ' + eval(error));
    }
  })

}

function ListarPedidos() {

  parametros = {
    "btnListarPedidos": 'true',
    "txtNroPedido": document.getElementById("lblNroPedido").innerHTML.trim()
  }
  $.ajax({
    data: parametros,
    url: '../../Controller/Pedidos/clsPedidos.php',
    type: 'post',
    success: function (response) {

      document.getElementById("tblReferencias").innerHTML = response;


    },
    error: function (error) {
      alert('error; ' + eval(error));
    }
  })
}

function ListarPedidosBusqueda() {
  if (document.getElementById("txtNroPedido").value.trim() == "") {
    document.getElementById("CtnSeccionPedido").style.display = 'none';
    document.getElementById('ctnCliente').style.display = 'none';
    $.notify({
      message: '<strong>Ingrese numero del pedido a buscar.</strong>'
    }, {
      type: 'info',
      placement: {
        from: "top",
        align: "right"
      },
      z_index: 1031
    });
    return;
  }
  parametros = {
    "btnListarPedidos": 'true',
    "txtNroPedido": document.getElementById("txtNroPedido").value.trim()
  }
  $.ajax({
    data: parametros,
    url: '../../Controller/Pedidos/clsPedidos.php',
    type: 'post',
    success: function (response) {
      if (response == "false") {
        $.notify({
          message: '<strong>No se encuentra el Pedido creado.</strong>'
        }, {
          type: 'info',
          placement: {
            from: "top",
            align: "right"
          },
          z_index: 1031
        });
        document.getElementById("CtnSeccionPedido").style.display = 'none';
        document.getElementById('ctnCliente').style.display = 'none';
        return;
      }
      var intNroPedido = document.getElementById("txtNroPedido").value.trim();
      document.getElementById("CtnSeccionPedido").style.display = 'block';
      document.getElementById('ctnCliente').style.display = 'inline';
      document.getElementById('lblNroPedido').innerHTML = intNroPedido;
      document.getElementById("tblReferencias").innerHTML = response;
      BuscarClienteAsociadoPedido();
    },
    error: function (error) {
      alert('error; ' + eval(error));
    }
  })
}
function Ocultar() {
  $('#ModalImg').modal('hide');
  document.getElementById('txtReferencia').value = '';
  document.getElementById('txtReferencia').focus();
}

function Eliminar(Tipo) {
  Swal.fire({
    title: 'Estás seguro?',
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Eliminar!'
  }).then((result) => {
    if (result.value) {
      parametros = {
        "btnEliminar": 'true',
        "txtReferencia": document.getElementById("tblReferencias").rows[Tipo].cells[1].innerHTML.trim(),
        "txtEstilo": document.getElementById("tblReferencias").rows[Tipo].cells[2].innerHTML.trim(),
        "txtDescripcion": document.getElementById("tblReferencias").rows[Tipo].cells[3].innerHTML.trim(),
        "txtColor": document.getElementById("tblReferencias").rows[Tipo].cells[4].innerHTML.trim(),
        "txtCantidad": document.getElementById("tblReferencias").rows[Tipo].cells[5].innerHTML.trim(),
        "txtN": document.getElementById("tblReferencias").rows[Tipo].cells[6].innerHTML.trim(),
        "txtNroPedido": document.getElementById("lblNroPedido").innerHTML.trim(),
        "intNroIndice": document.getElementById("tblReferencias").rows[Tipo].cells[9].innerHTML.trim()

      }

      console.log(parametros + "param");

      $.ajax({
        data: parametros,
        url: '../../Controller/Pedidos/clsPedidos.php',
        type: 'post',
        success: function (response) {
          $.notify({
            message: '<strong>' + response + '</strong>'
          }, {
            type: 'warning',
            placement: {
              from: "top",
              align: "right"
            },
            z_index: 09999
          });
          ListarPedidos();
        },
        error: function (error) {
          alert('error; ' + eval(error));
        }
      })
    }
  })

}


function ModalGaleria(intNro) {
  document.getElementById('txtReferencia').value = document.getElementById('lb' + intNro).innerHTML.trim().split('.')[0].split('$')[0];
  var strRuta = document.getElementById('ms' + intNro).src;
  intCantidadImg = intNro;
  BuscarReferencia(strRuta);

}
function MdIzquierda() {
  intCantidadImg = parseInt(intCantidadImg) - 1;
  if (intCantidadImg == -1) {
    intCantidadImg = document.getElementById('intCantidadImagenes').value.trim();
  }
  document.getElementById('imgProductos').src = document.getElementById('ms' + intCantidadImg).src;
  document.getElementById('lblLinea').innerHTML = document.getElementById('lb' + intCantidadImg).innerHTML;
  GaleriaProductos(document.getElementById('lblLinea').innerHTML.split('.')[0].split
    ('$')[0]);
  document.getElementById('txtCantidad').value = '';
  document.getElementById('txtColor').value = '';
  document.getElementById('txtEstilo').value = '';
  document.getElementById('txtAreaProducto').value = '';
}

//flechas modal buscar referencia
function MdIzquierda1() {
  intCantidadImg = parseInt(intCantidadImg) - 1;
  if (intCantidadImg == -1) {
    intCantidadImg = document.getElementById('intCantidadImagenes').value.trim();
  }
  document.getElementById('imgProductos1').src = document.getElementById('ms' + intCantidadImg).src;
  document.getElementById('lblLinea').innerHTML = document.getElementById('lb' + intCantidadImg).innerHTML;
  GaleriaProductos(document.getElementById('lblLinea').innerHTML.split('.')[0].split
    ('$')[0]);
  document.getElementById('txtCantidad').value = '';
  document.getElementById('txtColor').value = '';
  document.getElementById('txtEstilo').value = '';
  document.getElementById('txtAreaProducto').value = '';
}



function MdDerecha() {
  intCantidadImg = parseInt(intCantidadImg) + 1;
  var intCantidad = document.getElementById('intCantidadImagenes').value;
  if (intCantidadImg > intCantidad) {
    intCantidadImg = 0;
  }
  document.getElementById('imgProductos').src = document.getElementById('ms' + intCantidadImg).src;
  document.getElementById('lblLinea').innerHTML = document.getElementById('lb' + intCantidadImg).innerHTML;
  GaleriaProductos(document.getElementById('lblLinea').innerHTML.split('.')[0].split('$')[0]);
  document.getElementById('txtCantidad').value = '';
  document.getElementById('txtColor').value = '';
  document.getElementById('txtEstilo').value = '';
  document.getElementById('txtAreaProducto').value = '';
}


function GaleriaProductos(strReferencia) {
  var parametros = {
    "btnBusquedaGaleriaProductos": 'true',
    "btnListarArchivos": 'true',
    "strReferencia": strReferencia,
    "intTipoPrecio": document.getElementById('lblPrecio').innerHTML.trim(),
    "intNroPedido": document.getElementById('lblNroPedido').innerHTML.trim()
  };
  $.ajax({
    data: parametros,
    url: '../../Controller/Pedidos/clsPedidos.php',
    type: 'post',
    success: function (response) {
      var strDatos = response.split('%');
      document.getElementById('txtReferenciaAgregar').innerHTML = strDatos[0];
      document.getElementById('txtDescripcion').innerHTML = strDatos[1];
      if (strDatos[1].trim() == 'No disponible.') {
        document.getElementById('txtCantidad').readOnly = true;
        document.getElementById('btnAgregarReferencia').disabled = true;
      } else {
        document.getElementById('txtCantidad').readOnly = false;
        document.getElementById('btnAgregarReferencia').disabled = false;
      }
      document.getElementById('txtN').value = strDatos[2];
      document.getElementById('txtUnidadMedida').innerHTML = strDatos[3];
      document.getElementById('txtTamano').innerHTML = strDatos[4];
      document.getElementById('txtCantidadProducto').innerHTML = strDatos[5];
      document.getElementById('lblUbicacion').innerHTML = strDatos[6];
      document.getElementById('txtPrecioCinco').value = strDatos[7];
      document.getElementById('CtnProductoEnPedido').innerHTML = strDatos[8];
      document.getElementById('txtPrecioProducto').value = strDatos[2];
    },
    error: function (error) {
      alert('error; ' + eval(error));;
    }
  });
}
function EventoAgregarProducto(e) {
  tecla = (document.all) ? e.keyCode : e.which;
  if (tecla == 13) {
    AgregarPedido();
  }

}

ListarArchivos();
console.log(ListarArchivos)
var sw = 1;
oldid = null;
function fijo(id) {

  if (oldid != null) {
    document.getElementById(oldid).style = "display:inline-block;text-align: center;height: 100px; width: 100px; padding : 5px; border-radius:100%";
  }

  document.getElementById(id).style = "display:inline-block;text-align: center;height: 100px; width: 100px; padding : 5px; border-radius:100%;background-color:rgba(255,147,15,100);";
  oldid = id;
  sw = 0;

  n = 2;
}
function ListarArchivos() {

  var parametros = {
    "btnListarArchivos": 'true'
  };
  $.ajax({
    data: parametros,
    url: '../../Controller/Pedidos/clsPedidos.php',
    type: 'post',

    success: function (response) {


      document.getElementById('TblArchivos').innerHTML = response;
      console.log(response)

    },
    error: function (error) {
      alert('error; ' + eval(error));;
    }
  });
}




function Expandir(id) {
  var parametros = {
    "btnDbClick": 'true',
    "DbClick": document.getElementById(id).innerHTML.trim()
  };
  $.ajax({
    data: parametros,
    url: '../../Controller/Pedidos/clsPedidos.php',
    type: 'post',

    success: function (response) {
      document.getElementById('CtnFiltrosCarpetas').style.display = 'block';
      document.getElementById('TblArchivos').innerHTML = response;
      document.getElementById('strCarpetas').innerHTML = document.getElementById('txtCarpetas').innerHTML;
    },
    error: function (error) {
      alert('error; ' + eval(error));;
    }
  });

  n = 1;
}
history.pushState(null, null, location.href);
window.onpopstate = function () {
  history.go(1);
};
function Home() {
  var parametros = {
    "btnHome": 'true'
  };
  $.ajax({
    data: parametros,
    url: '../../Controller/Pedidos/clsPedidos.php',
    type: 'post',

    success: function (response) {
      document.getElementById('CtnFiltrosCarpetas').style.display = 'none';
      document.getElementById('strCarpetas').innerHTML = '';
      document.getElementById('TblArchivos').innerHTML = response;
    },
    error: function (error) {
      alert('error; ' + eval(error));;
    }
  });
}
Home();
function Back() {
  var parametros = {
    "btnBack": 'true'
  };
  $.ajax({
    data: parametros,
    url: '../../Controller/Pedidos/clsPedidos.php',
    type: 'post',

    success: function (response) {
      document.getElementById('CtnFiltrosCarpetas').style.display = 'none';
      document.getElementById('strCarpetas').innerHTML = '';
      document.getElementById('TblArchivos').innerHTML = response;

    },
    error: function (error) {
      alert('error; ' + eval(error));;
    }
  });
}



function CrearCSV() {
  $('#ModalCargando').modal({ backdrop: 'static', keyboard: false });
  $('#ModalCargando').modal('show');
  var parametros = {
    "btnCrearCSVClientes": 'true'
  }

  $.ajax({
    data: parametros,
    url: '../../Controller/Pedidos/clsPedidos.php',
    type: 'post',

    success: function (response) {
      CrearSelectZonas();
      //CrearSelectCiudades();
      $('#ModalCargando').modal('hide');
      swal(response);

    },
    error: function (error) {
      alert('error; ' + eval(error));;
    }
  });
}
function MostrarClientes() {
  $('#mdClientes').modal('show');
}



function ListarClientes() {

  var parametros = {
    "btnMostrarClientes": 'true',
    "strCiudad": $("#ddlCiudadesClientes").val()

  }
  $.ajax({
    data: parametros,
    url: '../../Controller/Pedidos/clsPedidos.php',
    type: 'post',
    success: function (response) {

      document.getElementById('tbodyClientes').innerHTML = response;

    },
    error: function (error) {
      alert('error; ' + eval(error));;
    }
  });

}
function CrearSelectZonas() {
  var parametros = {
    "btnZonasSelect": 'true'

  }
  $.ajax({
    data: parametros,
    url: '../../Controller/Pedidos/clsPedidos.php',
    type: 'post',
    success: function (response) {
      document.getElementById('ddlZonasVendedor').innerHTML = response;
      CrearSelectCiudades();
    },
    error: function (error) {
      alert('error; ' + eval(error));;
    }
  });
}

function CrearSelectCiudades() {
  var parametros = {
    "btnCiudadesSelect": 'true',
    "intZona": document.getElementById('ddlZonasVendedor').value.trim()
  }
  $.ajax({
    data: parametros,
    url: '../../Controller/Pedidos/clsPedidos.php',
    type: 'post',
    success: function (response) {
      document.getElementById('ddlCiudadesClientes').innerHTML = response;
      //BuscarClienteTabla();
      ListarClientes();
    },
    error: function (error) {
      alert('error; ' + eval(error));;
    }
  });
}

function SeleccionarCliente(intFila) {
  var txtReferenciaBusqueda = document.getElementById('txtReferencia');
  var txtAreatxtAreaObservacion = document.getElementById('txtAreatxtAreaObservacion');
  var intCartera = document.getElementById('tblClientes').rows[intFila].cells[8].innerHTML;
  var btnDuplicarPedido = document.getElementById('btnDuplicarPedido');
  document.getElementById('lblClienteCedula').value = document.getElementById('tblClientes').rows[intFila].cells[1].innerHTML;
  document.getElementById('lblClienteNombre').value = document.getElementById('tblClientes').rows[intFila].cells[2].innerHTML;
  document.getElementById('lblCliente').innerHTML = document.getElementById('tblClientes').rows[intFila].cells[2].innerHTML;
  var intTelefono1 = document.getElementById('tblClientes').rows[intFila].cells[3].innerHTML;
  var intTelefono2 = document.getElementById('tblClientes').rows[intFila].cells[4].innerHTML;
  var strDireccion1 = document.getElementById('tblClientes').rows[intFila].cells[5].innerHTML;
  var strDireccion2 = document.getElementById('tblClientes').rows[intFila].cells[6].innerHTML;
  var strCiudad = document.getElementById('tblClientes').rows[intFila].cells[7].innerHTML;
  var strCartera = document.getElementById('tblClientes').rows[intFila].cells[8].innerHTML;
  var intCupo = document.getElementById('tblClientes').rows[intFila].cells[9].innerHTML;
  var intPrecio = document.getElementById('tblClientes').rows[intFila].cells[10].innerHTML;
  var intMarcado = document.getElementById('tblClientes').rows[intFila].cells[11].innerHTML;
  console.log(intMarcado);
  document.getElementById('lblClienteTelefono1').value = intTelefono1;
  document.getElementById('lblClienteTelefono2').value = intTelefono2;
  document.getElementById('lblClienteDireccion1').value = strDireccion1;
  document.getElementById('lblClienteDireccion2').value = strDireccion2;
  document.getElementById('lblClienteCiudad').value = strCiudad;
  document.getElementById('lblClienteCupo').value = intCupo;
  document.getElementById('lblClientePrecio').innerHTML = intPrecio;
  document.getElementById('lblClienteCartera').value = strCartera;
  document.getElementById('btnBuscarCliente').disabled = true;
  document.getElementById('lblFactMarcado').value = intMarcado
  txtReferenciaBusqueda.disabled = false;
  txtAreatxtAreaObservacion.disabled = false;
  btnDuplicarPedido.disabled = false;
  if (intPrecio == 5) {
    document.getElementById('lblTipoPedido').style.display = 'inline';
  } else {
    document.getElementById('lblTipoPedido').style.display = 'none';
  }
  $('#mdClientes').modal('hide');
  AsociarClienteAPedido();
  ListarPedidos();
}
function AsociarClienteAPedido() {
  var parametros = {
    "btnAsociarCliente": 'true',
    "intNroPedido": document.getElementById('lblNroPedido').innerHTML.trim(),
    "strCedula": document.getElementById('lblClienteCedula').value.trim(),
    "strNombre": document.getElementById('lblClienteNombre').value.trim(),
    "strCartera": document.getElementById('lblClienteCartera').value.trim(),
    "intTelefono1": document.getElementById('lblClienteTelefono1').value.trim(),
    "intTelefono2": document.getElementById('lblClienteTelefono2').value.trim(),
    "strDireccion1": document.getElementById('lblClienteDireccion1').value.trim(),
    "strDireccion2": document.getElementById('lblClienteDireccion2').value.trim(),
    "strCiudad": document.getElementById('lblClienteCiudad').value.trim(),
    "strCupo": document.getElementById('lblClienteCupo').value.trim(),
    "intPrecio": document.getElementById('lblClientePrecio').innerHTML.trim(),
    "intMarcado": document.getElementById('lblFactMarcado').value

  }
  $.ajax({
    data: parametros,
    url: '../../Controller/Pedidos/clsPedidos.php',
    type: 'post',
    success: function (response) {
      Mensaje("Cliente " + document.getElementById('lblClienteNombre').value.trim() + " asociado con exito.");
      document.getElementById('btnEliminarCliente').disabled = false;
      document.getElementById('btnFinalizarPedido').disabled = false;
      document.getElementById('btnQuitarFinalizadoPedido').style.display = 'none';
    },
    error: function (error) {
      alert('error; ' + eval(error));;
    }
  });
}
function Mensaje(strMensaje) {
  $.notify({
    message: '<strong>' + strMensaje + '</strong>'
  }, {
    type: 'success',
    placement: {
      from: 'top',
      align: 'right'
    },
    z_index: 1031
  });
}
function EliminarAsociado() {

  var parametros = {
    "btnEliminarAsociado": 'true',
    "intNroPedido": document.getElementById('lblNroPedido').innerHTML.trim()

  }
  $.ajax({
    data: parametros,
    url: '../../Controller/Pedidos/clsPedidos.php',
    type: 'post',
    success: function (response) {
      swal(response);
      BuscarClienteAsociadoPedido();
      var btnEliminarProducto = document.getElementsByClassName('btnEliminar');
      var txtCantProducto = document.getElementsByClassName('txtCantProducto');
      for (var i = 0; i <= btnEliminarProducto.length - 1; i++) {
        btnEliminarProducto[i].disabled = true;
        txtCantProducto[i].disabled = true;
      }
    },
    error: function (error) {
      alert('error; ' + eval(error));;
    }
  });
}
function BuscarClienteTabla() {
  if (document.getElementById('txtMdlClientes').value.trim() == '') {
    ListarClientes();
    return;
  }
  var parametros = {
    "btnBuscarClienteTabla": 'true',
    "intIdCiudad": document.getElementById('ddlCiudadesClientes').value.trim(),
    "strCliente": document.getElementById('txtMdlClientes').value.trim()

  }
  $.ajax({
    data: parametros,
    url: '../../Controller/Pedidos/clsPedidos.php',
    type: 'post',
    success: function (response) {
      document.getElementById('tbodyClientes').innerHTML = response;
    },
    error: function (error) {
      alert('error; ' + eval(error));;
    }
  });
}
function BuscarClienteAsociadoPedido() {
  var parametros = {
    "btnBuscarClienteAsociado": 'true',
    "intNroPedido": document.getElementById('lblNroPedido').innerHTML.trim()

  }
  $.ajax({
    data: parametros,
    url: '../../Controller/Pedidos/clsPedidos.php',
    type: 'post',
    success: function (response) {
      var txtReferenciaBusqueda = document.getElementById('txtReferencia');
      var lblTipoPedido = document.getElementById('lblTipoPedido');
      var btnFinalizarPedido = document.getElementById('btnFinalizarPedido');
      var btnQuitarFinalizadoPedido = document.getElementById('btnQuitarFinalizadoPedido');
      var lblEstadoDelPedido = document.getElementById('lblEstadoDelPedido');
      var btnEliminarClienteAsociado = document.getElementById('btnEliminarCliente');
      var txtAreatxtAreaObservacion = document.getElementById('txtAreatxtAreaObservacion');
      var btnDuplicarPedido = document.getElementById('btnDuplicarPedido');
      var btnEnviarPedidoWs = document.getElementById('btnEnviarPedidoWs');
      txtAreatxtAreaObservacion.disabled = true;
      btnEnviarPedidoWs.disabled = false;
      btnEnviarPedidoWs.style.display = 'none';
      if (response.length > 0) {
        var strDatos = response.split('%');
        console.log(strDatos);
        document.getElementById('lblClienteNombre').value = strDatos[1];
        document.getElementById('lblClienteCedula').value = strDatos[0];
        document.getElementById('lblClienteCartera').value = strDatos[2];
        document.getElementById('lblClienteTelefono1').value = strDatos[3];
        document.getElementById('lblClienteTelefono2').value = strDatos[4];
        document.getElementById('lblClienteDireccion1').value = strDatos[5];
        document.getElementById('lblClienteDireccion2').value = strDatos[6];
        document.getElementById('lblClienteCiudad').value = strDatos[7];
        document.getElementById('lblClienteCupo').value = strDatos[8];
        document.getElementById('lblCliente').innerHTML = strDatos[1];
        document.getElementById('lblFactMarcado').value = strDatos[12];
        document.getElementById('btnBuscarCliente').disabled = true;
        btnEliminarClienteAsociado.disabled = false;
        txtAreatxtAreaObservacion.value = strDatos[11].trim();
        btnDuplicarPedido.disabled = false;
        //Validamos si el estado del pedido 1=PdIniciado 2=PdFinalizado 3=PdEnviadoWs
        lblEstadoDelPedido.style.background = '#ffc107';
        if (strDatos[10] == 1) {
          btnFinalizarPedido.disabled = false;
          btnFinalizarPedido.style.display = 'inline';
          txtReferenciaBusqueda.disabled = false;
          btnQuitarFinalizadoPedido.style.display = 'none';
          lblEstadoDelPedido.style.display = 'none';
          txtAreatxtAreaObservacion.disabled = false;
        } else if (strDatos[10] == 2) {
          btnFinalizarPedido.disabled = true;
          btnFinalizarPedido.style.display = 'none';
          btnQuitarFinalizadoPedido.style.display = 'inline';
          txtReferenciaBusqueda.disabled = true;
          btnEnviarPedidoWs.disabled = false;
          btnEnviarPedidoWs.style.display = 'inline';
          lblEstadoDelPedido.style.display = 'inline';
          lblEstadoDelPedido.innerHTML = 'Pedido Finalizado';
        } else if (strDatos[10] == 3) {
          btnFinalizarPedido.disabled = true;
          btnFinalizarPedido.style.display = 'none';
          btnQuitarFinalizadoPedido.style.display = 'none';
          txtReferenciaBusqueda.disabled = true;
          lblEstadoDelPedido.style.display = 'inline';
          lblEstadoDelPedido.innerHTML = 'Pedido Enviado';
          lblEstadoDelPedido.style.background = 'red';
          btnEliminarClienteAsociado.disabled = true;
        }
        //Indicamos tipo de cliente Im o no Im
        if (strDatos[9] == 5) {
          lblTipoPedido.style.display = 'inline';
        } else {
          lblTipoPedido.style.display = 'none';
        }
      } else {
        document.getElementById('lblClienteNombre').value = '';
        document.getElementById('lblClienteCedula').value = '';
        document.getElementById('lblClienteCartera').value = '';
        document.getElementById('lblClienteTelefono1').value = '';
        document.getElementById('lblClienteTelefono2').value = '';
        document.getElementById('lblClienteDireccion1').value = '';
        document.getElementById('lblClienteDireccion2').value = '';
        document.getElementById('lblClienteCiudad').value = '';
        document.getElementById('lblClienteCupo').value = '';
        document.getElementById('btnBuscarCliente').disabled = false;
        btnEliminarClienteAsociado.disabled = true;
        document.getElementById('lblCliente').innerHTML = 'Sin cliente.';
        btnFinalizarPedido.style.display = 'inline';
        btnFinalizarPedido.disabled = true;
        btnQuitarFinalizadoPedido.style.display = 'none';
        lblTipoPedido.style.display = 'none';
        txtReferenciaBusqueda.disabled = true;
        lblEstadoDelPedido.style.display = 'none';
        txtAreatxtAreaObservacion.value = '';
        btnDuplicarPedido.disabled = true;
      }
    },
    error: function (error) {
      alert('error; ' + eval(error));;
    }
  });
}

function BuscarCategoria(strTipo) {
  $("#" + strTipo).prop('disabled', false);
  document.getElementById(strTipo).focus();
  $("#" + strTipo).prop('disabled', true);
}

/* Pedido detalle */
function BuscarReferenciaPedido() {
  var strDato = document.getElementById('txtDatoPedido');
  if (strDato.value.trim() == '') {
    ListarPedidos();
    return;
  }
  var parametros = {
    "btnBuscarReferenciaPedido": 'true',
    "strDato": strDato.value.trim(),
    "strNroPedido": document.getElementById('lblNroPedido').innerHTML.trim()


  }
  $.ajax({
    data: parametros,
    url: '../../Controller/Pedidos/clsPedidos.php',
    type: 'post',
    success: function (response) {
      document.getElementById('tblReferencias').innerHTML = response;
    },
    error: function (error) {
      alert('error; ' + eval(error));;
    }
  });
}

// ----------------- SECCION PEDIDOS -----------------------//
//24/05/2019
//Variables Globales
function FinalizarPedido() {
  var intNroPedido = document.getElementById('lblNroPedido').innerHTML.trim();
  var strTotalPedido = document.getElementById('lblTotalPedido');
  var txtAreatxtAreaObservacion = document.getElementById('txtAreatxtAreaObservacion');
  strParametros = {
    "intNroPedido": intNroPedido,
    "CmdFinalizarPedido": "true",
    "strTotalPedido": strTotalPedido.innerHTML.trim()
  }
  //Valida si hay items en el detalle para poder finalizar el pedido.
  if ($("#tblProductos tr").length == '1') {
    Msg('No puede finalizar el pedido.No tiene productos en él.', 'danger');
    return;
  }
  $.ajax({
    data: strParametros,
    url: '../../Controller/Pedidos/clsPedidos.php',
    type: 'post',
    success: function (response) {
      //Bloqueo de los botones finalizar y quitar pedido finalizado
      var btnEnviarPedidoWs = document.getElementById('btnEnviarPedidoWs');
      document.getElementById('txtReferencia').disabled = true;
      document.getElementById('btnFinalizarPedido').style.display = 'none';
      document.getElementById('btnQuitarFinalizadoPedido').style.display = 'inline';
      document.getElementById('lblEstadoDelPedido').style.display = 'inline';
      document.getElementById('lblEstadoDelPedido').innerHTML = 'Pedido Finalizado';
      var btnEliminarProducto = document.getElementsByClassName('btnEliminar');
      var txtCantProducto = document.getElementsByClassName('txtCantProducto');
      btnEnviarPedidoWs.disabled = false;
      btnEnviarPedidoWs.style.display = 'inline';
      document.getElementById('lblEstadoDelPedido').style.background = '#ffc107';
      txtAreatxtAreaObservacion.disabled = true;

      //Bloqueando los botones de eliminar del pedido cuando finaliza
      for (var i = 0; i <= btnEliminarProducto.length - 1; i++) {
        btnEliminarProducto[i].disabled = true;
        txtCantProducto[i].disabled = true;
      }
      Msg('Pedido Nro ' + intNroPedido + ' finalizado con éxito.', 'warning');
    },
    error: function (error) {
      alert('error' + error);
    }
  });
}
//Metodo para cambiar el estado del pedido 2 a 1 para poder seguir creando items al cliente
function QuitarFinalizadoPedido() {
  var txtAreatxtAreaObservacion = document.getElementById('txtAreatxtAreaObservacion');
  var strParametros = {
    "btnQuitarFinalizadoPedido": 'true',
    "intNroPedido": document.getElementById('lblNroPedido').innerHTML.trim()
  }
  $.ajax({
    data: strParametros,
    url: '../../Controller/Pedidos/clsPedidos.php',
    type: 'post',
    success: function (response) {
      Msg(response, 'success');
      ListarPedidos();
      var btnEnviarPedidoWs = document.getElementById('btnEnviarPedidoWs');
      document.getElementById('btnFinalizarPedido').disabled = false;
      document.getElementById('txtReferencia').disabled = false;
      document.getElementById('btnFinalizarPedido').style.display = 'inline';
      document.getElementById('btnQuitarFinalizadoPedido').style.display = 'none';
      document.getElementById('lblEstadoDelPedido').style.display = 'none';
      document.getElementById('lblEstadoDelPedido').innerHTML = '';
      btnEnviarPedidoWs.disabled = true;
      btnEnviarPedidoWs.style.display = 'none';
      txtAreatxtAreaObservacion.disabled = false;
    },
    error: function (error) {
      alert('error; ' + eval(error));;
    }
  });

}
//Metodo para modificar producto del detalle
function ModificarProductoPd(intNroIndiceProducto) {
  var strParametros = {
    "CmdModificarProductoPedido": 'true',
    "intNroPedido": document.getElementById('lblNroPedido').innerHTML.trim(),
    "intNroIndiceProducto": intNroIndiceProducto,
    "intCantProducto": document.getElementById('txtCantProducto' + intNroIndiceProducto).value.trim()
  }
  $.ajax({
    data: strParametros,
    url: '../../Controller/Pedidos/clsPedidos.php',
    type: 'post',
    success: function (response) {
      Msg(response);
      ListarPedidos();
    },
    error: function (error) {
      alert('error; ' + eval(error));;
    }
  });
}
//Metodo para agregar la observación al pedido
function AgregarObservacionPedido() {
  var txtAreatxtAreaObservacion = document.getElementById('txtAreatxtAreaObservacion');
  var strParametros = {
    "CmdAgregarObservacionPedido": 'true',
    "intNroPedido": document.getElementById('lblNroPedido').innerHTML.trim(),
    "strObservacion": txtAreatxtAreaObservacion.value.trim()
  }
  $.ajax({
    data: strParametros,
    url: '../../Controller/Pedidos/clsPedidos.php',
    type: 'post',
    success: function (response) {
      console.log('Add Observación');
    },
    error: function (error) {
      alert('error; ' + eval(error));;
    }
  });
}

//Verifica si se duplica el pedido
function ValidarDuplicarPedido() {
  var lblNroPedido = document.getElementById('lblNroPedido');
  Swal.fire({
    title: 'Desea duplicar el pedido Nro ' + lblNroPedido.innerHTML + '?',
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Si',
    cancelButtonText: 'No'
  }).then((result) => {
    if (result.value) {
      //Duplica un pedido expecifico
      DuplicarPedido(lblNroPedido.innerHTML);
    }
  })
}
//Metodo para duplicar un pedido
function DuplicarPedido(intNroPedido) {
  if ($("#tblProductos tr").length == '1') {
    Msg('No puede duplicar el pedido.No tiene productos en él.', 'danger');
    return;
  }
  var strParametros = {
    "CmdDuplicarPedido": 'true',
    "intNroPedido": intNroPedido,
    "strCedula": document.getElementById('lblClienteCedula').value,
    "strNombre": document.getElementById('lblClienteNombre').value,
    "strCiudad": document.getElementById('lblClienteCiudad').value,
    "intTelefono1": document.getElementById('lblClienteTelefono1').value,
    "intTelefono2": document.getElementById('lblClienteTelefono2').value,
    "strDireccion1": document.getElementById('lblClienteDireccion1').value,
    "strDireccion2": document.getElementById('lblClienteDireccion2').value,
    "strCartera": document.getElementById('lblClienteCartera').value,
    "strCupo": document.getElementById('lblClienteCupo').value,
    "intPrecio": document.getElementById('lblClientePrecio').innerHTML.trim(),
    "intMarcado": document.getElementById('lblFactMarcado').value.trim()
  }
  $.ajax({
    data: strParametros,
    url: '../../Controller/Pedidos/clsPedidos.php',
    type: 'post',
    success: function (response) {
      document.getElementById('txtNroPedido').value = response;
      ListarPedidosBusqueda();
      Msg('Pedido Nro ' + intNroPedido + ' copiado con éxito. Su pedido nuevo es el número : <strong>' + response + '</strong>');
      setTimeout(function () { $('#txtAreatxtAreaObservacion').focus(); }, 500);
    },
    error: function (error) {
      alert('error; ' + eval(error));;
    }
  });
}
//Verifica si se envia un pedido finalizado a inmoda
function ValidacionEnvioPedidoFinalizado() {
  var intNroPedido = document.getElementById('lblNroPedido').innerHTML.trim();
  Swal.fire({
    title: 'Desea enviar el pedido Nro ' + intNroPedido + ' finalizado?',
    html: "Con lleva al envio del pedido finalizado a InmodaFantasy.( <strong> Una vez hecho esto no se podra retractar. </strong>)",
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Si',
    cancelButtonText: 'No'
  }).then((result) => {
    if (result.value) {
      //Enviar un pedido expecifico
      EnviarPedidoFinalizado(intNroPedido);
    }
  })
}
//Validar formulario de clientes marcados
function ValidarFormularioMarcado() {
  if ($.trim($('#lblFactCorreo').val()).trim() != '') {
    if ($.trim($('#lblFactTelefono').val()).trim() != '') {
      if ($.trim($('#lblFactCelular').val()).trim() != '') {
        if ($.trim($('#lblFactCiudad').val()).trim() != '') {
          if ($.trim($('#lblFactCorreo').val()).includes('@')) {
            if ($.trim($('#lblFactCorreo').val()).includes('.')) {
              if ($.trim($('#lblFactTelefono').val()).length >= 7) {
                if ($.trim($('#lblFactCelular').val()).length >= 10) {
                  ValidacionEnvioPedidoFinalizado();
                  $('#ModalFactE').modal('hide');
                } else {
                  Msg('Ingrese un <b>Celular </b> valido', 'danger')
                }
              } else {
                Msg('Ingrese un <b>Telefono </b> valido', 'danger')
              }
            } else {
              Msg('Ingrese un <b>Correo </b> valido', 'danger')
            }
          } else {
            Msg('Ingrese un <b>Correo </b> valido', 'danger')
          }

        } else {
          Msg('Campo <b>Ciudad </b> obligatorio', 'danger')
        }
      } else {
        Msg('Campo <b>Celular </b> obligatorio', 'danger')
      }
    } else {
      Msg('Campo <b>Telefono </b> obligatorio', 'danger')
    }
  } else {
    Msg('Campo <b>Correo </b> obligatorio', 'danger')
  }
}
//Envio de pedido
function EnviarPedidoFinalizado(intNroPedido) {
  //Tipo de envio 0
  //0 significa envio uno a uno
  //1 Significa envio de todo los pedidos finalizados

  var strParametros = {
    "CmdEnvioDePedidoWs": 'true',
    "blnTipoDeEnvio": 0,
    "intNroPedido": intNroPedido,
    "strFactCorreo": $('#lblFactCorreo').val(),
    "strFactCelular": $('#lblFactCelular').val(),
    "strFactTelefono": $('#lblFactTelefono').val(),
    "strFactCiudad": $('#lblFactCiudad').val()

  }
  $.ajax({
    data: strParametros,
    url: '../../Controller/EstadoPedidos/clsEstadoPedidos.php',
    type: 'post',
    success: function (response) {
      console.log(response);
      $('#lblFactCorreo').val("");
      $('#lblFactCelular').val("");
      $('#lblFactTelefono').val("");
      $('#lblFactCiudad').val("");
      if (response === '1') {
        var strTexto = 'Pedido ' + intNroPedido + ' enviado con éxito.';
        Msg(strTexto, 'success');
        ListarPedidosBusqueda();
      } else if (response === '3') {
        var strTexto = 'Pedido ' + intNroPedido + ' ya fue enviado.Recargue el pedido.';
        Msg(strTexto, 'success');

        return;
      } else {
        Msg('Ocurrio un error enviado los datos. Intente más tarde.', 'danger');
        return;
      }
    },
    error: function (error) {
      alert('error; ' + eval(error));
    }
  });
}
//SOLO LETRAS Y NUMEROS
function BloqueaCharP(e) {
  tecla = (document.all) ? e.keyCode : e.which;
  //Tecla de retroceso para borrar, siempre la permite
  if (tecla == 8) {
    return true;
  }
  if (tecla == 32) {
    return true;
  }
  if (tecla == 64) {
    return true;
  }
  if (tecla == 46) {
    return true;
  }
  if (tecla == 45) {
    return true;
  }
  if (tecla == 95) {
    return true;
  }
  // Patron de entrada, en este caso solo acepta numeros y letras
  patron = /[A-Za-z0-9]/;
  tecla_final = String.fromCharCode(tecla);
  return patron.test(tecla_final);
}
//Validar precio por letras 
function ValidarPrecioPorLetra(strPrecio) {
  let strCadena = "NOPRSUVWYZ";
  for (i = 0; i <= strPrecio.length - 1; i++) {
    if (strCadena.indexOf(strPrecio.charAt(i), 0) == -1) {
      return 1;
    }
  }
  return 0;
}
//Generar Pdf
document.getElementById('btnGenerarPdfPedido').addEventListener('click',
  () => {
    Swal.fire({
      title: 'Generar PDF.',
      type: 'info',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si',
      cancelButtonText: 'No'
    }).then((result) => {
      if (document.getElementById('lblClienteCedula').value.trim() == '') {
        swal('No puede generar el PDF no hay cliente asociado.');
        return;
      }
      if (result.value) {
        window.open("../Pdf/PedidoEmpresaPdf.php?intNroPedido=" + document.getElementById('lblNroPedido').innerHTML.trim());
      }
    });
  }
);

//Add observacion
$("#txtAreatxtAreaObservacion").blur(function () {
  AgregarObservacionPedido();
});




//PRUEBA FLECHAS....................................................................................................../

//validacion referencia
function validar(e) {
  tecla = (document.all) ? e.keyCode : e.which;
  if (tecla == 13) {
    BuscarReferencias(0)
    n = 1;
  }
}

//modal referencia buscada

function BuscarReferencias(strRuta) {
  parametros = {
    "btnBuscarReferencia": 'true',
    "txtReferencia": document.getElementById("txtReferencia").value.trim(),
    "intNroPedido": document.getElementById('lblNroPedido').innerHTML.trim()
  }
  document.getElementById("gif").style.display = "block";

  $.ajax({
    data: parametros,
    url: '../../Controller/Pedidos/clsPedidos.php',
    type: 'post',
    success: function (response) {

      document.getElementById("gif").style.display = "none";
      document.getElementById('Modal').innerHTML = response;
      $('#ModalImg').modal({ backdrop: 'static', keyboard: false });
      $('#ModalImg').modal('show');
      document.getElementById('btnDesplazar').style.display = 'none';
      document.getElementById('btnDesplazar2').style.display = 'none';
      document.getElementById('btnDesplazars').style.display = 'none';
      document.getElementById('btnDesplazar2s').style.display = 'none';
      document.getElementById('imgProductos').style.display = 'none';
      if (strRuta === 0) {
        document.getElementById('btnDesplazars').style.display = 'inline';
        document.getElementById('btnDesplazar2s').style.display = 'inline';
        blnEstado = false;
      }
    },
    error: function (error) {
      alert('error; ' + eval(error));
    }
  })

}

//cambiar con flechas de teclado
$('#ModalImg').bind('keydown', 'ArrowRight', function () {
  if (event.keyCode == "39" && document.getElementById('foto '+n) != null && !blnEstado) {
    cambiar();
  }
  if (event.keyCode == "37" && !blnEstado) {
    volver();
  }
});

let n = 1;

function cambiar(){
    if(document.getElementById('foto '+n) != null){
      n = n + 1;
      document.getElementById('btnDesplazars').disabled = false;
      document.getElementById('imgProductos1').src = document.getElementById('foto '+n).src;
      console.log(n);
      let nombre = document.getElementById('imgProductos1');
      let lblinea = (nombre.src).split('/');
      lblinea = lblinea[lblinea.length-1];
      console.log(lblinea); 
      document.getElementById('lblLinea').innerHTML = lblinea;
    }else{
      document.getElementById('btnDesplazar2s').disabled = true;
      n= n-1;
    }
}

function volver() {
  if(n > 1){
    n = n - 1;
    document.getElementById('btnDesplazar2s').disabled = false;
    document.getElementById('imgProductos1').src = document.getElementById('foto '+n).src;
    let nombre = document.getElementById('imgProductos1');
    let lblinea = (nombre.src).split('/');
    lblinea = lblinea[lblinea.length-1];
    console.log(lblinea); 
    document.getElementById('lblLinea').innerHTML = lblinea;
    console.log(n);
  }else{
    document.getElementById('btnDesplazars').disabled = true;
    n = 1;
  } 
}

//fin flechas-----------------------------------------------------------------------------------------------------------------

