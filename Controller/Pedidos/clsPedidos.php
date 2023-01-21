<?php
date_default_timezone_set('America/Bogota');
header("Content-Type: text/html;charset=utf-8");
$clsPedido = new clsPedido();
if(isset($_POST['CmdCrearNuevoPedido'])){
	$clsPedido->CrearArchivosParaElPedido();
}
if(isset($_POST["btnBuscarReferencia"])){
	$clsPedido->LeerExcel();
}
if(isset($_POST["btnListarPedidos"])){
	$clsPedido->LeerPedidoTxt();
}
if(isset($_POST["btnAgregarPedido"])){
	$clsPedido->AgregarPedido();
}
if(isset($_POST["btnListarReferencias"])){
	$clsPedido->ListarReferencias();
}
if(isset($_POST['btnGenerarExcel'])){
	$clsPedido->GenerarExcel();
}
if(isset($_POST['btnEliminar'])){
	$clsPedido->EliminarReferencia();
}
if(isset($_POST['btnBorrarPedido'])){
	$clsPedido->Borrar();
}

if(isset($_POST['btnActualziarPedido'])){
	$clsPedido->ActualizarPedido();
}
if(isset($_POST{'btnMostrarClientes'})){
	$clsPedido->BuscarCliente();
}
if(isset($_POST['btnCiudadesSelect'])){
	$clsPedido->CrearSelectCiudades($_POST['intZona']);
}
if(isset($_POST['btnZonasSelect'])){
	$clsPedido->CrearSelectZonas();
}
if(isset($_POST['btnAsociarCliente'])){
	$clsPedido->AsociarPedidoCliente();
}
if(isset($_POST['btnEliminarAsociado'])){
	$clsPedido->EliminarAsociado();
}
if(isset($_POST['btnBuscarClienteAsociado'])){
	$clsPedido->BuscarClienteAsociadoPedido();
}
if(isset($_POST['btnBuscarClienteTabla'])){
	$clsPedido->BuscarClienteTabla();
}
if(isset($_POST['btnBuscarReferenciaPedido'])){
	$clsPedido->BuscarReferenciaPedido();
}
if(isset($_POST['CmdModificarProductoPedido'])){
	$clsPedido->ModificarProductoPedido();
}
if(isset($_POST['CmdAgregarObservacionPedido'])){
	$clsPedido->AgregarObservacionPedido();
}
if(isset($_POST['CmdDuplicarPedido'])){
	$clsPedido->DuplicarPedido();
}
//Por Foto
@session_start();
if(!isset($_SESSION['StrDir'])){
	$_SESSION['blnEstado']='true';
	$_SESSION['StrDir']='../../../../ownCloud/fotos_nube/FOTOS  POR SECCION CON PRECIO';
}
if(isset($_POST['btnListarArchivos'])){
	$clsPedido->ListarArchivos();
}
if(isset($_POST['btnDbClick'])){
	$clsPedido->DbClick($_POST['DbClick']);
}
if(isset($_POST['btnHome'])){
	$_SESSION['blnEstado']='true';
	$_SESSION['StrDir']='../../../../../ownCloud/fotos_nube/FOTOS  POR SECCION CON PRECIO';
	$clsPedido->ListarArchivos();
}


if(isset($_POST['btnBack'])){
	$arDir=explode('/', $_SESSION['StrDir']);
	$_SESSION['blnEstado']='true';
	if($_SESSION['StrDir']!='../../../../../ownCloud/fotos_nube/FOTOS  POR SECCION CON PRECIO'){
		$_SESSION['StrDir']='';
		for ($i=0; $i <= sizeof($arDir)-2 ; $i++) { 
			$_SESSION['StrDir'].= $arDir[$i]."/";
		}
		$_SESSION['StrDir'] = substr($_SESSION['StrDir'], 0, -1);
	}
	$clsPedido->ListarArchivos();
}
if(isset($_POST['btnBusquedaGaleriaProductos'])){
	$clsPedido->BusquedaGaleriaProductos();
}

/*if(isset($_POST['btnsumar'])){
	$clsPedido->sumar($_POST['sumar']);
}*/


//Pedidos

if(isset($_POST['CmdFinalizarPedido'])){
	$clsPedido->FinalizarPedido();
}
if(isset($_POST['btnQuitarFinalizadoPedido'])){
	$clsPedido->QuitarFinalizadoPedido();
}

/*

Estructura txt PedidosEncabezados
%NroPedido
%CedulaCliente
%NombreCliente
%Cartera
%Telefono1
%Telefono2
%Dirección1
%Dirección2
%Ciudad
%Cupo
%SegmentoPrecio
%EstadoPedido(1,2,3)
%FechaFinalizado
%NroItems
%ValorTotalPedido
%Fecha Envio Inmoda
%Observación




*/
$clsPedido=null;

class clsPedido 
{	
	private $strReferencia;
	private $objPHPExcel;
	private $Rutaimg;
	private $strEstilo;
	private $strDescripcion;
	private $strColor;
	private $strCantidad; 
	private $strN;
	private $nroPedido;
	private $strRutaEncarpetado;
	private $j=0;
	private $strContenido;
	private $intCliente;
	private $intNroIndice=0;
	private $strConfFile;
	private $ban = 0;

	function __construct()
	{
		$this->strReferencia="";
		$this->strDescripcion="";
		$this->strColor="";
		$this->strCantidad="";
		$this->strN="";
		$this->nroPedido="";
		$this->strRutaEncarpetado='';
		$this->intCliente='';

		/*-----Bueno -----*/
		$this->strRutaFlEncabezado="../../../../../PedidosSisve/PedidosEncabezado";
		$this->strRutaFlDetalle="../../../../../PedidosSisve/PedidoDetalle";
		$this->strRutaFlConsecutivo="../../../../../PedidosSisve/ConsecutivosPedidos";
		$this->strConfFile='../../../../../PedidosSisve/Sisve.cnf';
	}
	//Este metodo funciona para crear el respectivo número de pedido.En el cual,
	//se crea los respectivos archivos planos para el uso de pedido creado por el vendedor.
	public function CrearArchivosParaElPedido(){
		if(!file_exists("../../../../../PedidosSisve")){
			mkdir("../../../../../PedidosSisve", 0777, true);
		}
		//Validamos el consecutivo que sigue
		$strNroPedido=1;
		if(!file_exists("../../../../../PedidosSisve/ConsecutivosPedidos.txt")){
			$fl=fopen("../../../../../PedidosSisve/ConsecutivosPedidos.txt","w");
			fwrite($fl,'1'.PHP_EOL);
			fclose($fl);
		}

		//Validando consecutivo siguiente
		$fl=file("../../../../../PedidosSisve/ConsecutivosPedidos.txt");
		$intConsecutivo=trim((string)end($fl));
		if(!(($intConsecutivo)=='')){
			//Validación
			if(file_exists("../../../../../PedidosSisve/PedidoDetalle".$intConsecutivo.".txt")){
				$fl=file("../../../../../PedidosSisve/PedidoDetalle".$intConsecutivo.".txt");
				if(end($fl)!=''){
					$strNroPedido=(int)$intConsecutivo+1;
					//Agregando consecutivo siguiente del pedido.
					$fl = fopen("../../../../../PedidosSisve/ConsecutivosPedidos.txt","w");
					fwrite($fl,$strNroPedido.PHP_EOL);
					fclose($fl);
					echo $strNroPedido;
				}else{
					echo $intConsecutivo;
				}
			}else{
				echo 1;
			}
		}
		
		//Creando los respetivos archivos del pedido
		if(!file_exists("../../../../../PedidosSisve/PedidosEncabezado.txt")){
			$fl=fopen("../../../../../PedidosSisve/PedidosEncabezado.txt","w");
			fclose($fl);
			$fl=fopen("../../../../../PedidosSisve/PedidosEncabezadoAuxiliar.txt","w");
			fclose($fl);
		}
		if(!(file_exists("../../../../../PedidosSisve/PedidoDetalle".$strNroPedido.".txt"))){
			$fp = fopen("../../../../../PedidosSisve/PedidoDetalle".$strNroPedido.".txt","w");
			fclose($fp);
			$fp = fopen("../../../../../PedidosSisve/PedidoAuxiliarDetalle".$strNroPedido.".txt","w");
			fclose($fp);
			chmod("../../../../../PedidosSisve/PedidoDetalle".$strNroPedido.".txt",0777);
			chmod("../../../../../PedidosSisve/PedidoAuxiliarDetalle".$strNroPedido.".txt",0777);
		}
	}

	public function LeerPedidoTxt(){
		$this->nroPedido=trim($_POST['txtNroPedido']);


		if((file_exists("../../../../../PedidosSisve/PedidoDetalle".$this->nroPedido.".txt"))){
			$Acumulador="";
			$fp = fopen("../../../../../PedidosSisve/PedidoDetalle".$this->nroPedido.".txt","r");
			$i=0;
			$h=0;
			while(!feof($fp)) {
				$linea=fgets($fp);
				$Datos=explode("%",$linea);
				if(trim($Datos[0])!=""){	
					$h++;
				}
			}


			fclose($fp);
			$strFile=file("../../../../../PedidosSisve/PedidoDetalle".$this->nroPedido.".txt");
			$h=0;
			//Obtener estado del pedido 1 0 2 para bloqueo de botones eliminar
			$intEstadoPedido=$this->GetEstadoPedido($this->nroPedido);
			$strBloqueoBtn="disabled=''";
			if($intEstadoPedido==1){
				$strBloqueoBtn="";
			}
			for($i=count($strFile)-1;$i>=0;$i--){
				$Datos=explode("%",$strFile[$i]);
				if(trim($Datos[0])!=""){
					if($h==400){
						break;
					}
					$Acumulador.="<tr id='cell".$i."'><td>".($h+1)."</td><td>".$Datos[0]."</td><td style='display:none'><label style='width:100%;' class='labelProductos' id='Esti".($h+1)."' >".$Datos[1]."</label></td><td>".$Datos[2]."</td><td>".$Datos[9]."</td><td><label class='labelProductos' style='width:100%;' id='Col".($h+1)."'>".$Datos[3]."</label></td><td>
					<input type='number' value='".$Datos[4]."' class='form-control w-100 ml-auto mr-auto txtCantProducto'
					onchange='ModificarProductoPd(".trim($Datos[6]).")' id='txtCantProducto".trim($Datos[6])."' ".$strBloqueoBtn." ></td><td><label class='labelProductos' style='width:100%;' id='Prec".($h+1)."' >".$Datos[5]."</label></td><td><button style='cursor:pointer;' ".$strBloqueoBtn." class='btn btnSisve-primary btnEliminar' onclick='Eliminar(\"".($h)."\");'><i class='fas fa-trash'></i></button></td><td style='display:none' id='indice".$i."'>".$Datos[6]."</td></tr>" ;
					$h++;		
				}
			}
			$this->ObtenerTotal();
			echo $Acumulador;
		}else{
			echo "false";
		}
	}
	public function ObtenerTotal(){
		$intNroItems=0;
		$fp = fopen("../../../../../PedidosSisve/PedidoDetalle".$this->nroPedido.".txt","r");
		$Array = array("0","1","2","3","4","5","6","7","8","9");
		$Valores=array("N","Z","Y","W","V","U","S","R","P","O");
		$Suma=0;
		$Dato='';
		while(!feof($fp)) {
			$linea = fgets($fp);
			$Datos=explode("%",$linea);
			if(trim($Datos[0])!=""){
				$Tamano=strlen(trim($Datos[5]));
				for($i=0;$i<=$Tamano-1;$i++){
					for($k=0;$k<sizeof($Valores);$k++){
						if(trim($Datos[5][$i])==$Valores[$k]){			   		
							$Dato.=$Array[$k];
						}
					}				
				}
				$Suma+=(int)$Dato*$Datos[4];
				$Dato='';
				$intNroItems++;
			}	
		}
		fclose($fp);
		$Total='';
		for($i=0;$i<=strlen(trim($Suma))-1;$i++){
			for($k=0;$k<=sizeof($Array)-1;$k++){
				if( trim($Suma)[$i]==$Array[$k]){	
					$Total.=$Valores[$k];
				}
			}	
		}		
		echo "<strong>Total: </strong><span id='lblTotalPedido'>".$Total."</span><br><strong>Nro Items:".$intNroItems."</strong>";
	}
	public function quitar_tildes ($cadena) 
	{ 
		$cadBuscar = array("á", "Á", "é", "É", "í", "Í", "ó", "Ó", "ú", "Ú","ñ","Ñ"); 
		$cadPoner = array("a", "A", "e", "E", "i", "I", "o", "O", "u", "U","n","N"); 
		$cadena = str_replace ($cadBuscar, $cadPoner, $cadena); 
		return $cadena; 
	} 
	public function AgregarPedido(){
		$this->strReferencia=trim(strtoupper($_POST["txtReferenciaAgregar"]));
		$this->strEstilo=trim($_POST["txtEstilo"]);
		$this->strDescripcion=$this->quitar_tildes(trim($_POST['txtDescripcion']));
		$this->strColor=trim($_POST['txtColor']);
		$this->strCantidad=trim($_POST['txtCantidad']);
		$this->strN=trim(strtoupper($_POST['txtN']));
		$this->nroPedido=trim($_POST['txtNroPedido']);
		$strPrecioProducto=trim($_POST['txtPrecioProducto']);
		$strUnidadMedida=trim($_POST['txtUnidadMedida']);
		$stObserProducto=$this->quitar_tildes(trim($_POST['txtObservacionProducto']));
		if($this->nroPedido==""){
			echo "Ingrese Nro pedido.";
			return;
		}
		if(!is_numeric($this->nroPedido)){
			echo "Ingrese solo numeros en pedido.";
			return;
		}

		if($this->strReferencia==""){
			echo "Ingrese Referencia.";
			return;
		}
		if($this->strDescripcion==""){
			echo "Ingrese Descripcion.";
			return;
		}
		if($this->strCantidad==""){
			echo "Ingrese Cantidad.";
			return;
		}
		if($this->strN==""){
			echo "Ingrese Precio.";
			return;
		}
		if(!is_numeric($this->strCantidad)){
			echo "Ingrese Solo Numeros en la cantidad.";
			return;
		}
		$flPedido = "../../../../../PedidosSisve/PedidoDetalle".$this->nroPedido.".txt";
		$flPedido = file($flPedido);
		if((int)count($flPedido)==0){
			$this->intNroIndice=1;
		}else{
			$ultima_linea = $flPedido[count($flPedido)-1];
			$strDato=explode("%",$ultima_linea);
			$this->intNroIndice=trim(((int)$strDato[6])+1);
		}
		//Dividiendo el precio por mitad si es IM

		/*
		if(isset($_POST['blnEstadoPrecio'])){
			if($_POST['blnEstadoPrecio']!='0'){
				$strPrecio1=$this->strN;
				$strArrayPrecio= array('N','Z','Y','W','V','U','S','R','P','O');
				for($i=0;$i<=9;$i++){
					$strPrecio1=str_replace($strArrayPrecio[$i],$i, $strPrecio1);
				}
				$strPrecio2= $strPrecio1/2;
				for($i=0;$i<=9;$i++){
					$strPrecio2=str_replace($i,$strArrayPrecio[$i], $strPrecio2);
				}
				$this->strN=$strPrecio2;
			}}*/
			$fp = fopen("../../../../../PedidosSisve/PedidoDetalle".$this->nroPedido.".txt","a");
			fwrite($fp,$this->strReferencia."%".$this->strEstilo."%".$this->strDescripcion."%".$this->strColor."%".$this->strCantidad."%".$this->strN."%".$this->intNroIndice."%".$strPrecioProducto."%".$strUnidadMedida."%".$stObserProducto.PHP_EOL);
			fclose($fp);
			echo "Referencia agregada.";

		}

		public function EliminarReferencia(){
			$this->strReferencia=trim($_POST["txtReferencia"]);
			$this->strEstilo=trim($_POST["txtEstilo"]);
			$this->strDescripcion=trim($_POST['txtDescripcion']);
			$this->strColor=trim($_POST['txtColor']);
			$this->strCantidad=trim($_POST['txtCantidad']);
			$this->strN=trim($_POST['txtN']);
			$this->nroPedido=trim($_POST['txtNroPedido']);
			$this->intNroIndice=trim($_POST['intNroIndice']);


			$fp = fopen("../../../../../PedidosSisve/PedidoDetalle".$this->nroPedido.".txt","r");
			$fp2 = fopen("../../../../../PedidosSisve/PedidoAuxiliarDetalle".$this->nroPedido.".txt","w");

			while(!feof($fp)) {
				$linea = fgets($fp);
				$Datos=explode("%",$linea);
				if(trim($Datos[0])!=""){

					if(trim($Datos[6])==$this->intNroIndice){
					}else{	
						fwrite($fp2,$Datos[0]."%".$Datos[1]."%".$Datos[2]."%".$Datos[3]."%".$Datos[4]."%".$Datos[5]."%".$Datos[6]."%".$Datos[7]."%".$Datos[8]."%".$Datos[9]);		
					}
				}
			}
			fclose($fp);
			fclose($fp2);


			$fp2 = fopen("../../../../../PedidosSisve/PedidoAuxiliarDetalle".$this->nroPedido.".txt","r");
			$fp = fopen("../../../../../PedidosSisve/PedidoDetalle".$this->nroPedido.".txt","w");
			while(!feof($fp2)) {
				$linea = fgets($fp2);
				$Datos=explode("%",$linea);	
				if(trim($Datos[0])!=""){

					fwrite($fp,$Datos[0]."%".$Datos[1]."%".$Datos[2]."%".$Datos[3]."%".$Datos[4]."%".$Datos[5]."%".$Datos[6]."%".$Datos[7]."%".$Datos[8]."%".$Datos[9]);	
				}	
			}
			fclose($fp);
			fclose($fp2);
			echo 'Referencia eliminada con exito.';
		}

		public function ListarReferencias(){
			$Acumulador="";
			$fp = fopen("../../../../../PedidosSisve/Pedidos".$this->nroPedido.".txt","r");
			while(!feof($fp)) {
				$linea = fgets($fp);
				$Datos=explode("%",$linea);
				$Acumulador.="<option value='".$Datos[0]."'>".$Datos[0]."</option>" ;
			}
			fclose($fp);
			echo $Acumulador;
		}

		public function LeerExcel()
	{
		$intNroPedido = trim($_POST['intNroPedido']);
		$this->strReferencia = strtoupper(trim($_POST["txtReferencia"]));
		if ($this->strReferencia == "") {
			echo "<div class='text-center w-100'><h2>No se encuentra el producto</h2>
				<input type='button' class='btn btnSisve-primary' onclick='$(\"#ModalImg\").modal(\"hide\");' value='Aceptar'></div>";
			return;
		}
		$blnEstado = false;
		$strCodigo = '';
		$strNombre = '';
		$strPrecio = '';
		$strUnidadMedida = '';
		$strTamano = '...';
		$intPrecio = $this->BuscarClientePrecioPedido($intNroPedido);
		$intPosicion = 3;
		$blnEstadoPrecio = 0;
		$intPrecioCinco = 0;
		$strDisplay = 'none';
		$intCantidadPQT = 0;
		$strCantidad = '';
		$strUbicacion = '';
		$intTipoPrecio = 1;

		$clase = '';
		$linea = '';
		$grupo = '';
		$tipo = '';

		switch ($intPrecio) {
			case 1:
				$intPosicion = 3;
				break;
			case 2:
				$intPosicion = 4;
				$intTipoPrecio = 2;
				break;
			case 3:
				$intPosicion = 5;
				$intTipoPrecio = 3;
				break;
			case 4:
				$intPosicion = 6;
				$intTipoPrecio = 4;
				break;
			case 5:
				$intPosicion = 3;
				$strDisplay = 'inline';
				$intTipoPrecio = 5;
				$blnEstadoPrecio = 1;
				break;
			case 0:
				$intPosicion = 3;
				break;
		}

		$fp = fopen("../../dataCryptPr.txt", "r");

		while (!feof($fp)) {
			$linea = fgets($fp);
			$Datos = explode(";", $linea);
			if (@(trim(str_replace('"', '', $Datos[0])) === $this->strReferencia) ||  @trim(str_replace('"', '', $Datos[1])) === $this->strReferencia) {

				$strCodigo = trim(str_replace('"', '', $Datos[1]), "id='code'" );
				$strNombre = trim(str_replace('"', '', $Datos[2]));
				$strPrecio = trim(str_replace('"', '', $Datos[$intPosicion]));
				$strUnidadMedida = trim(str_replace('"', '', $Datos[8]));
				$strTamano = trim(str_replace('"', '', $Datos[9]));
				$intCantidadPQT = trim(str_replace('"', '', $Datos[10]));
				$strUbicacion = trim(str_replace('"', '', $Datos[11]));
				if ($blnEstadoPrecio == 1) {
					$intPrecioCinco = trim(str_replace('"', '', $Datos[7]));
				}
				$blnEstado = true;
				$clase = trim(str_replace('"', '', utf8_decode($Datos[12])));
				$linea = trim(str_replace('"', '', utf8_decode($Datos[13])));
				$grupo = trim(str_replace('"', '', utf8_decode($Datos[14])));
				$tipo = trim(str_replace('"', '', utf8_decode($Datos[15])));

				$clasificacion = '';
				if ($clase != "GENERAL") {
					$clasificacion .= $clase . '/';
				}
				if ($linea != "GENERAL") {
					$clasificacion .= $linea . '/';
				}
				if ($grupo != "GENERAL") {
					$clasificacion .= $grupo . '/';
				}
				if ($tipo != "GENERAL") {
					$clasificacion .= $tipo . '/';
				}
			}
		}
		fclose($fp);
		if ($strUbicacion == '') {
			$strUbicacion = ' SIN UBICAR';
		}
		if (trim($strTamano) == '') {
			$strTamano = ' Sin tamaÑo';
		}

		if (!$blnEstado) {
			echo "<div class='text-center w-100'><h2>No se encuentra el producto</h2>
				<input type='button' class='btn btnSisve-primary' onclick='$(\"#ModalImg\").modal(\"hide\");' value='Aceptar'></div>";
			return;
		}
		if ($strUnidadMedida == 'UND') {
			$intCantidadPQT = 1;
		} else if ($strUnidadMedida == 'DOC') {
			$intCantidadPQT = 12;
		}
		//Contar cantidad de un producto en el pedido por compra
		$intCantidadReferenciaEnPedido = $this->ContarNroDeReferenciaEnPedido($strCodigo, $intNroPedido);
		$strMensajeAlerta = '<div><br><br><br><br></div>';
		if ($intCantidadReferenciaEnPedido != 0) {
			$strMensajeAlerta = "<div class='alert alert-warning' role='alert'><strong>La referencia 
				" . $strCodigo . "  ya se encuentra ingresada en el pedido con un total de "
				. $intCantidadReferenciaEnPedido . " " . $strUnidadMedida . ".</strong></div>";
		}
		//Obtenemos el estado de los productos para bloquear en ingreso del producto si ya esta finalizado
		//Obtener estado del pedido 1 0 2
		$intEstadoPedido = $this->GetEstadoPedido($intNroPedido);
		$strBloqueoBtn = "disabled=''";
		if ($intEstadoPedido == 1) {
			$strBloqueoBtn = "";
		}


		$strCantidad = "<div><label><strong>Cantidad Por Empaque:</strong></label>" .
			"<label id='txtCantidadProducto'> " . $intCantidadPQT . "</label></div>";
			$src = '';
			if (file_exists("../../../../../ownCloud/fotos_nube/" . strtoupper($this->strReferencia) . ".jpg")) {
				/*copy("../../../../../ownCloud/fotos_nube/".strtoupper($this->strReferencia).".jpg","../../../../../PedidosSisve/Referencia.jpg");
					$this->Rutaimg="../../../../../PedidosSisve/Referencia.jpg";*/
				$src = "../../../../../ownCloud/fotos_nube/" . strtoupper($this->strReferencia) . ".jpg";
			} else
				if (file_exists("../../../../../ownCloud/fotos_nube/FOTOS  POR SECCION CON PRECIO/" . utf8_decode($clasificacion) . "" . strtoupper($strCodigo) . ".jpg")) {
				/*copy("../../../../../ownCloud/fotos_nube/".$clasificacion."".strtoupper($strCodigo).".jpg","../../../../../PedidosSisve/Referencia.jpg");
					$this->Rutaimg="../../../../../PedidosSisve/Referencia.jpg";*/
				$src = "../../../../../ownCloud/fotos_nube/FOTOS  POR SECCION CON PRECIO/" . utf8_decode($clasificacion) . "" . strtoupper($strCodigo) . ".jpg";
			} else
				if (file_exists("../../../../../ownCloud/fotos_nube/FOTOS  POR SECCION CON PRECIO/" . utf8_decode($clasificacion) . "" . strtoupper($strCodigo) . "/" . strtoupper($strCodigo) . "$1.jpg")) {
				/*copy("../../../../../ownCloud/fotos_nube/".$clasificacion."".strtoupper($strCodigo).".jpg","../../../../../PedidosSisve/Referencia.jpg");
					$this->Rutaimg="../../../../../PedidosSisve/Referencia.jpg";*/
				$src = "../../../../../ownCloud/fotos_nube/FOTOS  POR SECCION CON PRECIO/" . utf8_decode($clasificacion) . "" . strtoupper($strCodigo) . "/" . strtoupper($strCodigo) . "$1.jpg";
			}
	


		/* Prueba imagenes ------------------------------------------------*/
		
			
			
		$rutaImagenes = '';
		$arrRutaImagenes =explode('/',$src);
		$indexRuta = 0;

		foreach ($arrRutaImagenes as $ruta) {
			if(count($arrRutaImagenes)-1!=$indexRuta){
				$rutaImagenes .= $ruta.'/';
			}
			$rutaImagenes = $rutaImagenes++;
			$indexRuta++;
		}

		$i = 0;
		$j = 1;
		
		if(is_dir($rutaImagenes)){
			$root = scandir($rutaImagenes);
			foreach($root as $value) 
			{ 
				
				if(is_file("$rutaImagenes/$value")) {
					$returnarr[] = $value;
					$rutaImagenes.$value;
					echo "<li value='".$value	."'></li>";
					echo "<img alt='' id='foto ".$j."' src='" . ($rutaImagenes.$returnarr[$i]) ."' width='460' style='border-radius: 20px;display: none;'  height='460'>";
					$i++;
					$j++;
				}
			}
		}
		
		/* Fin prueba imagenes */
		echo 	"<div class='col-lg-8'>
			<div id='CtnProductoEnPedido'>
			" . $strMensajeAlerta . "</div>
			<input type='hidden' value='" . $blnEstadoPrecio . "' id='blnEstadoPrecio'>
			<input type='hidden' value='" . $strPrecio . "' id='txtPrecioProducto'>
			<input type='hidden' value='" . $intPrecioCinco . "' id='txtPrecioCinco'>
			<div><h3 class='text-center'><strong>Foto <label id='lblLinea'> </label></strong></h3></div>
			<div class='row'>
			" .
			"<div  class='col-lg-1' >
			<button onclick='MdIzquierda()' id='btnDesplazar' class='btn btn-default btnflecha'><strong><</strong></button>
			<button onclick='volver()' id='btnDesplazars' class='btn btn-default btnflecha' ><strong><</strong></button>
			</div>
			<div id='img-contenedor' class='col-lg-10'>
			<img alt='' id='imgProductos' src='" . ($src) . "?nocache=" . time() . "' width='460' style='border-radius:20px;' height='460'>
			<img alt='' id='imgProductos1' src='" . ($rutaImagenes.$returnarr[0])."' width='460' style='border-radius:20px;' height='460'>
			
			</div>
			<div  class='col-lg-1' >
			<button onclick='cambiar()' id='btnDesplazar2s' class='btn btn-default btnflecha'><strong>></strong></button>
			<button onclick='MdDerecha()' id='btnDesplazar2' class='btn btn-default btnflecha'><strong>></strong></button>" .

			"</div></div></div>" .
			"<div class='col-lg-4'>
			Sección:<label id='lblPrecio'><strong>" . $intTipoPrecio . "</strong></label>
			Ubicación:<label><strong  id='lblUbicacion'> " . $strUbicacion . "</strong></label>
			<img  src='..\..\img\logo_empresa.png' style='width:50%;'><hr>" .
			"
			<div  style='text-align:left;'>
			<label><strong>Referencia: </strong></label>
			<label id='txtReferenciaAgregar'> " . strtoupper($strCodigo) . "</label><br>" .

			"<label><strong>Descripción: </strong></label>" .
			"<label id='txtDescripcion'> " . strtoupper($strNombre) . "</label><br>" .

			"<label><strong>Medida:</strong></label>" .
			"<label id='txtUnidadMedida'> " . strtoupper($strUnidadMedida) . "</label><br>" .

			"<label><strong >Tamaño:</strong></label>" .
			"<label id='txtTamano'>" . strtoupper($strTamano) . "</label>" .
			$strCantidad .
			"</div><hr class='m-1'>" .
			"<div class='row'><div class='col-lg-12'>" .
			"<label><strong>Precio</strong><label style='display:" . $strDisplay . "'><strong>*****</strong></label></label>" .
			"<input type='text' id='txtN'  value='" . $strPrecio . "' class='form-control' ></div>" .

			"<div class='col-lg-6' style='display:none'><label ><strong>Estilo</strong></label>" .
			"<input type='text' id='txtEstilo' maxlength='10' style='display:none' class='form-control' placeholder='Estilo'></div></div>" .
			"<div class='row'><div class='col-lg-6'>" .
			"" . "<label><strong>Color</strong></label>" .
			"<input   type='text' id='txtColor' class='form-control' placeholder='Color'></div>" .
			"<div class='col-lg-6'><label><strong>Cantidad</strong></label>" .
			" <input type='number' " . $strBloqueoBtn . " onkeypress='EventoAgregarProducto(event);' min='1' id='txtCantidad' class='form-control' placeholder='Cantidad'></div></div>
			<label id='lblNotificacion' style='display:none;'></label>" .
			"<label><strong>Observación</strong></label><br><textarea id='txtAreaProducto' " . $strBloqueoBtn . " class='form-control' style='resize: none;' maxlength='50' onkeydown='BloqueaChar(event)'></textarea>" .
			" <hr>" .
			"<button class='btn btnSisve-primary' " . $strBloqueoBtn . " id='btnAgregarReferencia' onclick='AgregarPedido();'><i class='glyphicon glyphicon-shopping-cart'></i> Agregar</button>" .
			"&nbsp;<button class='btn btnSisve-primary' onload='$('#txtCantidad').value='hola';' onclick='Ocultar();'><i class='glyphicon glyphicon-remove-circle'></i> Cancelar</button>" .
			"</div>";
	}

	
		
		public function ContarNroDeReferenciaEnPedido($strReferencia,$strNroPedido){
			$intCantidad=0;
			$fp = fopen("../../../../../PedidosSisve/PedidoDetalle".$strNroPedido.".txt","r");
			while(!feof($fp)) {
				$linea = fgets($fp);
				$Datos=explode("%",$linea);	
				if(trim($Datos[0])!=""){
					if($Datos[0]==$strReferencia){
						$intCantidad=$intCantidad+$Datos[4];
					}
				}	
			}
			fclose($fp);
			return $intCantidad;
		}



	//inicio segunda parte Por Foto
		public function GenerarArbolDeCapetas($ruta, $carpetaEspecial = false){

			if (is_dir($ruta)) 
			{ 
				if ($dh = opendir($ruta)) 
				{ 

					while (($file = readdir($dh)) !== false) 
					{ 

						if ((!is_file($file))and($file!='.')and($file!='..')) 
						{
							if ( filetype($ruta . $file) != "dir")
							{
								if(explode('.', $file)[1]=='jpg' || explode('.', $file)[1]=='png'){
									$array = explode("$", $file);
									if(sizeof($array) == 2){
										$display = 'none';
										if($array[1] == '1.jpg'){
											$display = 'inline-block';
										}
										$enc = mb_detect_encoding($ruta);
										//$ruta =mb_convert_encoding($ruta, "ASCII", $enc);
										echo "<div value='".$file."'  class='image' onclick='ModalGaleria(\"".$this->j."\");' style='display:".$display." ;height: 150px;width: 150px; padding :5px; overflow:hidden;' ondblclick=''>
												<img  id='ms".$this->j."' src='".(str_replace('?', 'Ñ',mb_convert_encoding($ruta, "ASCII", $enc))."".$file)."' style='border : 1px solid #bcbcbc;-webkit-border-radius: 5px 5px;height: 100px;cursor:pointer;'><br>
												<label id='lb".$this->j."' style='width:100px; overflow:hidden;'>".trim($file)."</label>
												</div>";
												$this->j++;
									}else{
										$enc = mb_detect_encoding($ruta);
										echo "<div value='".$file."'  class='image' onclick='ModalGaleria(\"".$this->j."\");' style='display:inline-block ;height: 150px;width: 150px; padding :5px; overflow:hidden;' ondblclick=''>
									<img  id='ms".$this->j."' src='".(str_replace('?', 'Ñ',mb_convert_encoding($ruta, "ASCII", $enc))."".$file)."' style='border : 1px solid #bcbcbc;-webkit-border-radius: 5px 5px;height: 100px;cursor:pointer;'><br>
									<label id='lb".$this->j."' style='width:100px; overflow:hidden;'>".trim($file)."</label>
									</div>";
									$this->j++;
									}
									
								}
							}
						}
						if (is_dir($ruta . $file) && $file!="." && $file!="..")
						{
							//validar si esta en las carpetas de 700,1000,2000,5000
							if($carpetaEspecial == 1){
								$this->strContenido.="
								<button style='cursor:pointer,border-radius25px;margin-bottom:5px;' class='btn btnSisve-primary' onclick='BuscarCategoria(\"".trim(str_replace(' ','', $file))."\")'>".$file."</button> ";
								echo '<hr class="linea">';
								echo "<div class='title-line' >
								<input type='text' disabled id='".trim(str_replace(' ','', $file))."' style='color:#fff;width:90%;border:none; background: #3a4a96; font-weight: bold;' value='".trim($file)."'/></div>";

								$this->GenerarArbolDeCapetas($ruta . $file . "/", $carpetaEspecial); 
								/*$arrayLineas = $this->ConsultarLineasAsociados();
								if(in_array(trim($file), $arrayLineas, true) || $this->ban != 0){
									$this->ban = 1;
									$this->strContenido.="
									<button style='cursor:pointer,border-radius25px;margin-bottom:5px;' class='btn btnSisve-primary' onclick='BuscarCategoria(\"".trim(str_replace(' ','', $file))."\")'>".$file."</button> ";
									echo '<hr class="linea">';
									echo "<div class='title-line' >
									<input type='text' disabled id='".trim(str_replace(' ','', $file))."' style='color:#fff;width:90%;border:none; background: #3a4a96; font-weight: bold;' value='".trim($file)."'/></div>";

									$this->GenerarArbolDeCapetas($ruta . $file . "/", $carpetaEspecial); 
									echo ''; 
								}*/
							}
							else{
								$doc = fopen("../../dataCryptLineas.txt","r");
								$cont = fread($doc, filesize("../../dataCryptLineas.txt"));
								if(strpos($cont, $file) === false){
								}else{

								$this->ban = 1;
								$this->strContenido.="
								<button style='cursor:pointer,border-radius25px;margin-bottom:5px;' class='btn btnSisve-primary' onclick='BuscarCategoria(\"".trim(str_replace(' ','', $file))."\")'>".$file."</button> ";
									echo '<hr class="linea">';
									echo "<div class='title-line' >
									<input type='text' disabled id='".trim(str_replace(' ','', $file))."' style='color:#fff;width:90%;border:none; background: #3a4a96; font-weight: bold;' value='".trim($file)."'/></div>";
								}
								$this->GenerarArbolDeCapetas($ruta . $file . "/", $carpetaEspecial); 
									echo '';
								
								 
							}

							

						} 
					} 
					closedir($dh); 
				} 
			}
			else{ 
				echo "<br>No es ruta valida"; 
			}
		}

	


		function ObtenerCedulaVendedor(){
			$fp = fopen($this->strConfFile,"r");
			while(!feof($fp)) {
				$lineaCsv= fgets($fp);
				$vendedorCsv=explode("%",str_replace('"', "", $lineaCsv));
				return $vendedorCsv[0];
			}
		}

		function ConsultarLineasAsociados(){
			$cedulaVendedor = $this->ObtenerCedulaVendedor();
			$arrayLineas = Array();
			//Validar las lineas que tiene asociadas
			$fp = fopen("../../dataCryptLi.txt","r");
			while(!feof($fp)) {
				$lineaCsv= fgets($fp);
				$csvLineas=explode(",",$lineaCsv);
				
				if(array_key_exists(2,$csvLineas)){
					
					if(in_array($cedulaVendedor, $csvLineas)){
						if(in_array(trim($csvLineas[2]), $arrayLineas) == false){
							array_push($arrayLineas,trim($csvLineas[2]));
						}
					}
				}
			}
			return $arrayLineas;
		}

		function ValidarSuperUsuario(){
			$fp = fopen($this->strConfFile,"r");
			while(!feof($fp)) {
				$lineaCsv= fgets($fp);
				$vendedorCsv=explode("%",str_replace('"', "", $lineaCsv));
				return trim($vendedorCsv[3]);
			}
		}

		function ListarArchivos(){
			$view="";
			$user = $this->ValidarSuperUsuario();
			if($user == "*"){
				$Dir=scandir($_SESSION['StrDir']);	
				$size=sizeof($Dir);
				for ($j=2; $j < $size  ; $j++) { 
					$buscar = '/700|1000|2000|3000|5000/';
					$rpta = preg_match($buscar, $Dir[$j]);
					if(is_dir($_SESSION['StrDir']."/".$Dir[$j]) && $rpta!=1){
						$view.="<div  id='ms".$j."' class='folder' onclick='fijo(\"ms".$j."\"); Expandir(\"lb".$j."\");'>
						<!--<img  src='./img/carpeta.png' style='height: 100px''>-->

						<b><div id='lb".$j."' class='Linea' >".trim($Dir[$j])."</div></b>

						</div>";		
					}
				}
			}else{
				$arrayLineas = $this->ConsultarLineasAsociados();
				
				//var_dump($arrayLineas);
				$Dir=scandir($_SESSION['StrDir']);
				$size=sizeof($Dir);
				for ($j=2; $j < $size  ; $j++) { 
					if(is_dir($_SESSION['StrDir']."/".$Dir[$j])){
						
						$buscar = '/700|1000|2000|3000|5000/';
						$rpta = preg_match($buscar, $Dir[$j]);
						if(in_array(trim($Dir[$j]), $arrayLineas, true) || $rpta!=1){
							if($_SESSION['blnEstado']=='true'){
								$view.="<div  id='ms".$j."' class='folder' onclick='fijo(\"ms".$j."\"); Expandir(\"lb".$j."\");'>
								<!--<img  src='./img/carpeta.png' style='height: 100px''>-->
		
								<b><div id='lb".$j."' class='Linea' >".trim($Dir[$j])."</div></b>
		
								</div>";					
							}
						}
					}
				}
			}

			
			$_SESSION['blnEstado']='false';
			echo $view;
		}
		function DbClick($file){
			if(is_dir($_SESSION['StrDir']."/".$file)){
				$buscar = '/700|1000|2000|3000|5000/';
				$carpetaEspecial = preg_match($buscar, $file);
				$this->GenerarArbolDeCapetas($_SESSION['StrDir']."/".$file."/", $carpetaEspecial);
				echo "<input type='hidden' value='".($this->j-1)."' id='intCantidadImagenes'/>";
				echo "<label id='txtCarpetas' style='display:none'>".( $this->strContenido)."</label>";		
				$this->ListarArchivos();
			}else{
				$this->ListarArchivos();
			}		
		}

			function Files1($src){
				$listar = null;
				$direc = opendir("../owncloud/fotos_nube/FOTOS  POR SECCION CON PRECIO/");
				

				while($elemento = readdir($direc)){
					if($elemento != '.' && $elemento != '..'){
						if(is_dir("../owncloud/fotos_nube/FOTOS  POR SECCION CON PRECIO/".$elemento)){
							$listar .= "<li><a href='../owncloud/fotos_nube/FOTOS  POR SECCION CON PRECIO/$elemento' target=_Blank>$elemento/</a></li>";
						}else{
							$listar .= "<li><a href='../owncloud/fotos_nube/FOTOS  POR SECCION CON PRECIO/$elemento' target=_Blank >$elemento</a></li>";
						}
					}

					
				}
			}
		
		
		


		
		function BusquedaGaleriaProductos(){
			$this->strReferencia=trim($_POST['strReferencia']);
			$intTipoPrecio=trim($_POST['intTipoPrecio']);
			$intNroPedido=trim($_POST['intNroPedido']);
			$intPosicion=3;
			switch ($intTipoPrecio) {
				case 1:
				$intPosicion=3;
				break;
				case 2:
				$intPosicion=4;
				break;
				case 3:
				$intPosicion=5;
				break;
				case 4:
				$intPosicion=6;
				break;
				case 5:
				$intPosicion=3;
				break;
				case 0:
				$intPosicion=3;
				break;
			}
			$fp = fopen("../../dataCryptPr.txt","r");
			$strCodigo='No disponible.';
			$strNombre='No disponible.';
			$strPrecio='No disponible.';
			$strUnidadMedida='';
			$strTamano='';
			$strUbicacion='';
			$strCantidad='';
			$strPrecioIM='';	
			while(!feof($fp)) {
				$linea = fgets($fp);
				$Datos=explode(";",$linea);
				if ( @(trim(str_replace('"','',$Datos[0]))===$this->strReferencia) ||  @trim(str_replace('"','',$Datos[1]))===$this->strReferencia){
					$strCodigo=trim(str_replace('"','',$Datos[1]));
					$strNombre=trim(str_replace('"','',$Datos[2]));
					$strPrecio=trim(str_replace('"','',$Datos[$intPosicion]));
					$strUnidadMedida=trim(str_replace('"','',$Datos[8]));
					$strTamano=trim(str_replace('"','',$Datos[9]));
					$strCantidad=trim(str_replace('"','',$Datos[10]));
					$strUbicacion=trim(str_replace('"','',$Datos[11]));
					$strPrecioIM=trim(str_replace('"','',$Datos[7]));
				}
			}
			fclose($fp);
			if($strUnidadMedida=='UND'){
				$strCantidad=1;
			}else if($strUnidadMedida=='DOC'){
				$strCantidad=12;
			}
			if($strUbicacion==''){
				$strUbicacion='SIN UBICAR';
			}
			if($strTamano==''){
				$strTamano='SIN TAMAÑO';
			}
			$intCantidadReferenciaEnPedido=$this->ContarNroDeReferenciaEnPedido($strCodigo,$intNroPedido);
			$strMensajeAlerta='<div><br><br><br><br></div>';
			if($intCantidadReferenciaEnPedido!=0){
				$strMensajeAlerta="<div class='alert alert-warning' role='alert'><strong>La referencia 
				".$strCodigo."  ya se encuentra ingresada en el pedido con un total de "
				.$intCantidadReferenciaEnPedido." ".$strUnidadMedida.".</strong></div>";
			}
			echo $strCodigo.'%'.$strNombre.'%'.$strPrecio.'%'.$strUnidadMedida.'%'.$strTamano."%".$strCantidad.'%'.$strUbicacion."%".$strPrecioIM.'%'.$strMensajeAlerta;
		}
		function BuscarCliente()
		{

			$strClientes="<tr><td><button class='btn btnSisve-primary' onclick='SeleccionarCliente(1)'><i class='fas fa-check'></i></button></td><td>0</td><td>GENERAL</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td style='display: none;' >1</td><td style='display:none'>0</td><td ><b style='color:rgb(236, 78, 164)'>Actualizar</b></td></tr>";
			$strCiudad=trim($_POST['strCiudad']);
			$fp = fopen("../../dataCryptCl.txt","r");
			$i=2;

			while(!feof($fp)) {
				$linea = fgets($fp);
				$Datos=explode(",",$linea);
				if($strCiudad===trim(str_replace('"', "", @$Datos[10]))){
					$Marcado='';
					if(@trim($Datos[11])=='0'){
						$Marcado="<b>Actualizar</b>";
					}
					@$strClientes.="<tr><td><button class='btn btnSisve-primary' onclick='SeleccionarCliente(".$i.")'><i class='fas fa-check'></i></button></td><td>".str_replace('"', "", $Datos[0])."</td><td>".str_replace('"', "", $Datos[1])."</td><td>".str_replace('"', "", $Datos[3])."</td><td>".str_replace('"', "", $Datos[4])."</td><td>".str_replace('"', "", $Datos[5])."</td><td>".str_replace('"', "", $Datos[6])."</td><td>".str_replace('"', "", $Datos[7])."</td><td>".(str_replace('"', "", $Datos[8]))."</td><td>".(str_replace('"', "", $Datos[9]))."</td><td style='display: none;' >".str_replace('"', "",$Datos[2])."</td><td style='display:none'>".$Datos[11]."</td><td>".str_replace('0', "<b style='color:rgb(236, 78, 164)'>Actualizar</b>", str_replace('1', "", $Datos[11]))."</td></tr>";
					$i++;
					if($i==100){
						break;
					}
				}
			}
			fclose($fp);
			echo $strClientes;
		}

		function CrearSelectZonas(){
			$Array= array();
			$strSelectZonas='';
			$blnEstado=true;
			$j=0;
			$fpZona = fopen($this->strConfFile,"r");
			while(!feof($fpZona)) {
				$linea = fgets($fpZona);
				if($linea != ""){
					$strZona=explode("%",$linea);
					if($strZona[2] == '*'){
						$fp = fopen("../../dataCryptZnVd.txt","r");
						while(!feof($fp)) {
							$lineaCsv= fgets($fp);
							$csvZona=explode(",",str_replace('"', "", $lineaCsv));
							if($csvZona[0] != "" && $csvZona[0] != '11' && $csvZona[0] != '12' && $csvZona[0] != '15' && $csvZona[0] != '16' && $csvZona[0] != '17'){
								$strSelectZonas.="<option value='".str_replace('"', "",$csvZona[0])."'>".$csvZona[1]."</option>";
							}
						}
						fclose($fp);
					}elseif($strZona){
						//Obteniendo las zonas del vendedor
						$strCedulaVendedor = trim($strZona[0]);	
						$ArrayZonasAsignadas=array();
						$fp = fopen("../../dataCryptZnAVd.txt","r");
						while(!feof($fp)){
							$lineaTxt= trim(fgets($fp));
							if($lineaTxt!=''){
								$strData=explode(",", $lineaTxt);
								if($strData[0]==$strCedulaVendedor){
									array_push($ArrayZonasAsignadas,$strData[2]);
								}
							}
						}
						fclose($fp);
						if(sizeof($ArrayZonasAsignadas)!=0){
							$fp = fopen("../../dataCryptZnVd.txt","r");
							while(!feof($fp)) {
								$lineaTxt= trim(fgets($fp));
								if($lineaTxt!=''){
									$strData=explode(",", $lineaTxt);
									for($i=0;$i<=sizeof($ArrayZonasAsignadas)-1;$i++){
										echo $ArrayZonasAsignadas[$i];
										if($strData[0]==$ArrayZonasAsignadas[$i]){
											$strSelectZonas.="<option value='".$strData[0]."'>".$strData[1]."</option>";
										}
									}
								}
							}
						}
						fclose($fp);
					}
				}
			}
			fclose($fpZona);
			echo $strSelectZonas;
		}
		
		function CrearSelectCiudades($intZona)
		{
			
			$fp = fopen("../../dataCryptZn.txt","r");
			$Array = array('');
			while(!feof($fp)) {
				$lineaCsv= fgets($fp);
				$csvZona=explode(",",str_replace('"', "", $lineaCsv));

				if(trim($intZona)!=''){

					if(@trim($intZona)==@trim($csvZona[1])){

						for($i=0;$i<=sizeof($Array)-1;$i++){
							if(trim($Array[$i])==@trim(str_replace('"', "", $csvZona[0]))){
								$blnEstado=false;
								break;	
							}
						}
						if($blnEstado){
							@$Array[$j]=trim(str_replace('"', "", $csvZona[0]));
							$j++;	
							$strCiudad=$this->BuscarCiudad(str_replace('"', "",$csvZona[0]));
							$strSelectCiudades.="<option value='".str_replace('"', "",$csvZona[0])."'>".$strCiudad."</option>";
						}
					}
					$blnEstado=true;
				}
			}
			fclose($fp);
			echo $strSelectCiudades;
		}
			function BuscarCiudad($strCiudad){
				$fp = fopen("../../dataCryptCi.txt","r");
				while(!feof($fp)) {
					$linea = fgets($fp);
					$strCiudades=explode(",",str_replace('"', "", $linea));
					if($strCiudades[0]!=''){
						if(trim($strCiudades[0])==trim($strCiudad)){
							fclose($fp);
							return $strCiudades[1];
						}
					}	
				}
				return 'Sin ciudad';
				fclose($fp);
			}
			function AsociarPedidoCliente(){
				if(!file_exists("../../../../../PedidosSisve/PedidosEncabezado.txt")){
					$fl=fopen("../../../../../PedidosSisve/PedidosEncabezado.txt","w");
					fclose($fl);
					$fl=fopen("../../../../../PedidosSisve/PedidosEncabezadoAuxiliar.txt","w");
					fclose($fl);
				}
				$intNroPedido=trim($_POST['intNroPedido']);
				$strNombre=$this->quitar_tildes(trim($_POST['strNombre']));
				$strCedula=trim($_POST['strCedula']);
				$strCartera=trim($_POST['strCartera']);
				$intTelefono1=trim($_POST['intTelefono1']);
				$intTelefono2=trim($_POST['intTelefono2']);
				$strDireccion1=$this->quitar_tildes(trim($_POST['strDireccion1']));
				$strDireccion2=$this->quitar_tildes(trim($_POST['strDireccion2']));
				$strCiudad=$this->quitar_tildes(trim($_POST['strCiudad']));
				$strCupo=trim($_POST['strCupo']);
				$intPrecio=trim($_POST['intPrecio']);
				$intMarcado=trim($_POST['intMarcado']);
				$fl=fopen("../../../../../PedidosSisve/PedidosEncabezado.txt","a");
				fwrite($fl,$intNroPedido."%".$strCedula."%".$strNombre."%".$strCartera."%".$intTelefono1."%".$intTelefono2."%".$strDireccion1."%".$strDireccion2."%".$strCiudad."%".$strCupo."%".$intPrecio."%1%01-01-01%0%N%01-01-01%Sin Observacion%".$intMarcado.PHP_EOL);
				fclose($fl);
			}

			function EliminarAsociado(){

				$intNroPedido=trim($_POST['intNroPedido']);
				$fp = fopen("../../../../../PedidosSisve/PedidosEncabezado.txt","r");
				$fp2 = fopen("../../../../../PedidosSisve/PedidosEncabezadoAuxiliar.txt","w");
				$i=0;
				while(!feof($fp)) {
					$linea = fgets($fp);
					if(trim($linea)!=""){
						$Datos=explode("%",$linea);
						if(trim($Datos[0])!=$intNroPedido){
							fwrite($fp2,$Datos[0]."%".$Datos[1]."%".$Datos[2]."%".$Datos[3]."%".$Datos[4]."%".$Datos[5]."%".$Datos[6]."%".$Datos[7]."%".$Datos[8]."%".$Datos[9]."%".$Datos[10]."%".$Datos[11]."%".$Datos[12]."%".$Datos[13]."%".$Datos[14]."%".$Datos[15]."%".$Datos[16]."%".$Datos[17].PHP_EOL);
						}
					}
				}
				fclose($fp);
				fclose($fp2);
				$fp2 = fopen("../../../../../PedidosSisve/PedidosEncabezadoAuxiliar.txt","r");
				$fp = fopen("../../../../../PedidosSisve/PedidosEncabezado.txt","w");
				while(!feof($fp2)) {
					$linea = fgets($fp2);
					$Datos=explode("%",$linea);	
					if(trim($Datos[0])!=""){
						fwrite($fp,$Datos[0]."%".$Datos[1]."%".$Datos[2]."%".$Datos[3]."%".$Datos[4]."%".$Datos[5]."%".$Datos[6]."%".$Datos[7]."%".$Datos[8]."%".$Datos[9]."%".$Datos[10]."%".$Datos[11]."%".$Datos[12]."%".$Datos[13]."%".$Datos[14]."%".$Datos[15]."%".$Datos[16]."%".$Datos[17].PHP_EOL);	
					}	
				}
				fclose($fp);
				fclose($fp2);
				echo 'Cliente desvinculado del pedido.';
			}	
			function BuscarClientePrecioPedido($intNroPedido){		
				$fp = fopen("../../../../../PedidosSisve/PedidosEncabezado.txt","r");
				$intPrecio='0';
				while(!feof($fp)) {
					$linea = fgets($fp);
					$Datos=explode("%",$linea);	
					if(trim($Datos[0])!=""){
						if(@$Datos[0]==$intNroPedido){
							$intPrecio=$Datos[10];
							break;
						}	
					}	
				}
				fclose($fp);

				return $intPrecio;
			}
			function BuscarClienteAsociadoPedido(){
				$intNroPedido=trim($_POST['intNroPedido']);
				$fp = fopen("../../../../../PedidosSisve/PedidosEncabezado.txt","r");
				$strContenido='';
				while(!feof($fp)) {
					$linea = fgets($fp);
					$Datos=explode("%",$linea);	

					if(trim($Datos[0])!=""){
						if(@$Datos[0]==$intNroPedido){
							@$strContenido=$Datos[1]."%".$Datos[2]."%".$Datos[3]."%".$Datos[4]."%".$Datos[5]."%".$Datos[6]."%".$Datos[7]."%".$Datos[8]."%".$Datos[9]."%".$Datos[10]."%".$Datos[11]."%".$Datos[16]."%".$Datos[17];
							break;
						}	
					}	
				}
				fclose($fp);
				echo $strContenido;
			}
			function BuscarClienteTabla(){
				$strCliente=trim($_POST['strCliente']);
				$intIdCiudad=trim($_POST['intIdCiudad']);
				$fp = fopen("../../dataCryptCl.txt","r");
				$strContenido='';
				$i=1;
				while(!feof($fp)) {
					$linea = fgets($fp);
					$Datos=explode(",",str_replace('"', "",$linea));	
					if(trim($Datos[0])!=""){
						if(@trim($Datos[10])==$intIdCiudad){
							if(@strstr(trim($Datos[1]),strtoupper($strCliente))){

								@$strContenido.="<tr><td><button class='btn btnSisve-primary' onclick='SeleccionarCliente(".$i.")'><i class='fas fa-check'></i></button></td><td>".$Datos[0]."</td><td>".$Datos[1]."</td><td>".$Datos[3]."</td><td>".$Datos[4]."</td><td>".$Datos[5]."</td><td>".$Datos[6]."</td><td>".$Datos[7]."</td><td>".@($Datos[8])."</td><td>".@($Datos[9])."</td><td style='display: none;' >".$Datos[2]."</td><td style='display:none'>".$Datos[11]."</td><td>".str_replace('0', "<b style='color:rgb(236, 78, 164)'>Actualizar</b>", str_replace('1', "", $Datos[11]))."</td></tr>";
								$i++;

							}
						}	
					}	
				}
				fclose($fp);
				echo $strContenido;
			}
			/* Buscar referencia en el pedido */
			public function BuscarReferenciaPedido(){
				$strDato=trim($_POST['strDato']);
				$this->nroPedido=trim($_POST['strNroPedido']);
				$strFile=file("../../../../../PedidosSisve/PedidoDetalle".$this->nroPedido.".txt");
				$h=0;
				$strAcumulador='';
				//Obtener estado del pedido 1 0 2
				$intEstadoPedido=$this->GetEstadoPedido($this->nroPedido);
				$strBloqueoBtn="disabled=''";
				if($intEstadoPedido==1){
					$strBloqueoBtn="";
				}
				for($i=count($strFile)-1;$i>=0;$i--){
					$Datos=explode("%",$strFile[$i]);
					if(trim($Datos[0])!=""){
						if ((strpos(trim($Datos[0]),strtoupper($strDato))!== false) || (strpos(trim($Datos[2]),strtoupper($strDato))!== false)){
							if($h==20){
								break;
							}
							$strAcumulador.="<tr id='cell".$i."'><td>".($h+1)."</td><td>".$Datos[0]."</td><td><label style='width:100%;' class='labelProductos' id='Esti".($h+1)."' >".$Datos[1]."</label></td><td>".$Datos[2]."</td><td>".$Datos[9]."</td><td><label class='labelProductos' style='width:100%;' id='Col".($h+1)."' >".$Datos[3]."</label></td><td><input type='number' value='".$Datos[4]."' class='form-control w-50 ml-auto mr-auto txtCantProducto'
							onchange='ModificarProductoPd(".trim($Datos[6]).")' id='txtCantProducto".trim($Datos[6])."' ".$strBloqueoBtn." ></td><td><label class='labelProductos' style='width:100%;' id='Prec".($h+1)."'>".$Datos[5]."</label></td><td><button class='btn btnSisve-primary btnEliminar' ".$strBloqueoBtn." onclick='Eliminar(\"".($h)."\");'><i class='fas fa-trash'></i></button></td><td style='display:none' id='indice".$i."'>".$Datos[6]."</td></tr>" ;
							$h++;
						}	
					}
				}
				$fp = fopen("../../../../../PedidosSisve/PedidoDetalle".$this->nroPedido.".txt","r");
				$this->ObtenerTotal();
				echo $strAcumulador;
			}
			/*---------------------- GENERAL ----------------------------*/
			private function CerrarTxt($flArchivo){
				fclose($flArchivo);
			}
			private function AbrirArchivoTxt($fl,$strTipo){
				return fopen($fl.'.txt',$strTipo);
			}
			private function EliminarTxt($strFl){
				unlink($strFl.'.txt'); 
			}
			private function ContrarNroItemsTxt($strFl){
				return count(file($strFl.".txt"));
			}
			//Metodo para obtener estado del pedido 1 o 2
			private function GetEstadoPedido($intNroPedido){
				$fl = $this->AbrirArchivoTxt($this->strRutaFlEncabezado,'r');
				$intEstado=0;
				while(!feof($fl)){
					$strLinea = fgets($fl);
					$strDatos=explode("%",trim($strLinea));	
					if(($strDatos[0])!=""){
						if($strDatos[0]==$intNroPedido){
							$intEstado=$strDatos[11];
							break;
						}
					}
				}

				return $intEstado;
			}

			/* --------------------- SECCIÓN PEDIDOS --------------------------------------*/

			public function FinalizarPedido(){
				$intNroPedido=trim($_POST['intNroPedido']);
				$strTotalPedido=trim($_POST['strTotalPedido']);
				$fl = $this->AbrirArchivoTxt($this->strRutaFlEncabezado,'r');
				$flAlterno = $this->AbrirArchivoTxt($this->strRutaFlEncabezado.'Auxiliar','w');
				//Data al auxiliar
				while(!feof($fl)){
					$strLinea = fgets($fl);
					$strDatos=explode("%",trim($strLinea));	
					if(($strDatos[0])!=""){
						if($strDatos[0]!=$intNroPedido){
							fwrite($flAlterno,$strDatos[0]."%".$strDatos[1]."%".$strDatos[2]."%".$strDatos[3]."%".$strDatos[4]."%".$strDatos[5]."%".$strDatos[6]."%".$strDatos[7]."%".$strDatos[8]."%".$strDatos[9]."%".$strDatos[10]."%".$strDatos[11]."%".$strDatos[12]."%".$strDatos[13]."%".$strDatos[14]."%".$strDatos[15]."%".$strDatos[16]."%".$strDatos[17].PHP_EOL);
						}else{
							$intNroItems=$this->ContrarNroItemsTxt($this->strRutaFlDetalle.$strDatos[0]);
							fwrite($flAlterno,$strDatos[0]."%".$strDatos[1]."%".$strDatos[2]."%".$strDatos[3]."%".$strDatos[4]."%".$strDatos[5]."%".$strDatos[6]."%".$strDatos[7]."%".$strDatos[8]."%".$strDatos[9]."%".$strDatos[10]."%2%".date('d-m-Y H:i:s')."%".$intNroItems."%".$strTotalPedido."%".$strDatos[15]."%".$strDatos[16]."%".$strDatos[17].PHP_EOL);
						}
					}	
				}
				//Cerrarmos los archivos txt
				$this->CerrarTxt($fl);
				$this->CerrarTxt($flAlterno);
				//Abrimos nuevamente los archivos para pasar la información
				$fl = $this->AbrirArchivoTxt($this->strRutaFlEncabezado,'w');
				$flAlterno = $this->AbrirArchivoTxt($this->strRutaFlEncabezado.'Auxiliar','r');
				while(!feof($flAlterno)){
					$strLinea = fgets($flAlterno);
					$strDatos=explode("%",trim($strLinea));	
					if(($strDatos[0])!=""){
						fwrite($fl,$strDatos[0]."%".$strDatos[1]."%".$strDatos[2]."%".$strDatos[3]."%".$strDatos[4]."%".$strDatos[5]."%".$strDatos[6]."%".$strDatos[7]."%".$strDatos[8]."%".$strDatos[9]."%".$strDatos[10]."%".$strDatos[11]."%".$strDatos[12]."%".$strDatos[13]."%".$strDatos[14]."%".$strDatos[15]."%".$strDatos[16]."%".$strDatos[17].PHP_EOL);
					}	
				}
				//Cerrarmos los archivos txt
				$this->CerrarTxt($fl);
				$this->CerrarTxt($flAlterno);
				//$this->EliminarTxt($this->strRutaFlEncabezado.'Auxiliar');
			}
			//Metodo para cambiar estado del pedido de 2 a 1
			public function QuitarFinalizadoPedido(){
				$intNroPedido=trim($_POST['intNroPedido']);
				$fl = $this->AbrirArchivoTxt($this->strRutaFlEncabezado,'r');
				$flAlterno = $this->AbrirArchivoTxt($this->strRutaFlEncabezado.'Auxiliar','w');
				//Data al auxiliar
				while(!feof($fl)){
					$strLinea = fgets($fl);
					$strDatos=explode("%",trim($strLinea));	
					if(($strDatos[0])!=""){
						if($strDatos[0]!=$intNroPedido){
							fwrite($flAlterno,$strDatos[0]."%".$strDatos[1]."%".$strDatos[2]."%".$strDatos[3]."%".$strDatos[4]."%".$strDatos[5]."%".$strDatos[6]."%".$strDatos[7]."%".$strDatos[8]."%".$strDatos[9]."%".$strDatos[10]."%".$strDatos[11]."%".$strDatos[12]."%".$strDatos[13]."%".$strDatos[14]."%".$strDatos[15]."%".$strDatos[16]."%".$strDatos[17].PHP_EOL);
						}else{
							fwrite($flAlterno,$strDatos[0]."%".$strDatos[1]."%".$strDatos[2]."%".$strDatos[3]."%".$strDatos[4]."%".$strDatos[5]."%".$strDatos[6]."%".$strDatos[7]."%".$strDatos[8]."%".$strDatos[9]."%".$strDatos[10]."%1%".$strDatos[12]."%".$strDatos[13]."%N%".$strDatos[15]."%".$strDatos[16]."%".$strDatos[17].PHP_EOL);
						}
					}	
				}
				//Cerrarmos los archivos txt
				$this->CerrarTxt($fl);
				$this->CerrarTxt($flAlterno);
				//Abrimos nuevamente los archivos para pasar la información
				$fl = $this->AbrirArchivoTxt($this->strRutaFlEncabezado,'w');
				$flAlterno = $this->AbrirArchivoTxt($this->strRutaFlEncabezado.'Auxiliar','r');
				while(!feof($flAlterno)){
					$strLinea = fgets($flAlterno);
					$strDatos=explode("%",trim($strLinea));	
					if(($strDatos[0])!=""){
						fwrite($fl,$strDatos[0]."%".$strDatos[1]."%".$strDatos[2]."%".$strDatos[3]."%".$strDatos[4]."%".$strDatos[5]."%".$strDatos[6]."%".$strDatos[7]."%".$strDatos[8]."%".$strDatos[9]."%".$strDatos[10]."%".$strDatos[11]."%".$strDatos[12]."%".$strDatos[13]."%".$strDatos[14]."%".$strDatos[15]."%".$strDatos[16]."%".$strDatos[17].PHP_EOL);
					}	
				}
				//Cerrarmos los archivos txt
				$this->CerrarTxt($fl);
				$this->CerrarTxt($flAlterno);
				//$this->EliminarTxt($this->strRutaFlEncabezado.'Auxiliar');
				echo 'Pedido Nro '.$intNroPedido.' quitado de la lista de finalizados.';
			}
			//Metodo para modificar producto del pd
			public function ModificarProductoPedido(){
				$intNroPedido=trim($_POST['intNroPedido']);
				$intNroIndiceProducto=trim($_POST['intNroIndiceProducto']);
				$intCantProducto=trim($_POST['intCantProducto']);
				$fl = $this->AbrirArchivoTxt($this->strRutaFlDetalle.$intNroPedido,'r');
				$flAlterno = $this->AbrirArchivoTxt($this->strRutaFlDetalle.'Auxiliar'.$intNroPedido,'w');
				//Modificando el producto
				while(!feof($fl)){
					$strLinea = fgets($fl);
					$strDatos=explode("%",trim($strLinea));	
					if(($strDatos[0])!=""){
						if($strDatos[6]!=$intNroIndiceProducto){
							fwrite($flAlterno,$strDatos[0]."%".$strDatos[1]."%".$strDatos[2]."%".$strDatos[3]."%".$strDatos[4]."%".$strDatos[5]."%".$strDatos[6]."%".$strDatos[7]."%".$strDatos[8]."%".$strDatos[9].PHP_EOL);
						}else{
							fwrite($flAlterno,$strDatos[0]."%".$strDatos[1]."%".$strDatos[2]."%".$strDatos[3]."%".$intCantProducto."%".$strDatos[5]."%".$strDatos[6]."%".$strDatos[7]."%".$strDatos[8]."%".$strDatos[9].PHP_EOL);
						}
					}	
				}
				//Cerrarmos los archivos txt
				$this->CerrarTxt($fl);
				$this->CerrarTxt($flAlterno);
				//Abrimos nuevamente los archivos para pasar la información
				$fl = $this->AbrirArchivoTxt($this->strRutaFlDetalle.$intNroPedido,'w');
				$flAlterno = $this->AbrirArchivoTxt($this->strRutaFlDetalle.'Auxiliar'.$intNroPedido,'r');
				while(!feof($flAlterno)){
					$strLinea = fgets($flAlterno);
					$strDatos=explode("%",trim($strLinea));	
					if(($strDatos[0])!=""){
						fwrite($fl,$strDatos[0]."%".$strDatos[1]."%".$strDatos[2]."%".$strDatos[3]."%".$strDatos[4]."%".$strDatos[5]."%".$strDatos[6]."%".$strDatos[7]."%".$strDatos[8]."%".$strDatos[9].PHP_EOL);
					}	
				}
				//Cerrarmos los archivos txt
				$this->CerrarTxt($fl);
				$this->CerrarTxt($flAlterno);
				echo 'Producto editado con éxito.';
			}
			//Metodo para agregar la observación al pedido
			public function AgregarObservacionPedido(){
				$intNroPedido=trim($_POST['intNroPedido']);
				$strObservacion=trim($_POST['strObservacion']);
				$fl = $this->AbrirArchivoTxt($this->strRutaFlEncabezado,'r');
				$flAlterno = $this->AbrirArchivoTxt($this->strRutaFlEncabezado.'Auxiliar','w');
				//Data al auxiliar
				while(!feof($fl)){
					$strLinea = fgets($fl);
					$strDatos=explode("%",trim($strLinea));	
					if(($strDatos[0])!=""){
						if($strDatos[0]!=$intNroPedido){
							fwrite($flAlterno,$strDatos[0]."%".$strDatos[1]."%".$strDatos[2]."%".$strDatos[3]."%".$strDatos[4]."%".$strDatos[5]."%".$strDatos[6]."%".$strDatos[7]."%".$strDatos[8]."%".$strDatos[9]."%".$strDatos[10]."%".$strDatos[11]."%".$strDatos[12]."%".$strDatos[13]."%".$strDatos[14]."%".$strDatos[15]."%".$strDatos[16]."%".$strDatos[17].PHP_EOL);
						}else{
							fwrite($flAlterno,$strDatos[0]."%".$strDatos[1]."%".$strDatos[2]."%".$strDatos[3]."%".$strDatos[4]."%".$strDatos[5]."%".$strDatos[6]."%".$strDatos[7]."%".$strDatos[8]."%".$strDatos[9]."%".$strDatos[10]."%".$strDatos[11]."%".$strDatos[12]."%".$strDatos[13]."%".$strDatos[14]."%".$strDatos[15]."%".$strObservacion."%".$strDatos[17].PHP_EOL);
						}
					}	
				}
				//Cerrarmos los archivos txt
				$this->CerrarTxt($fl);
				$this->CerrarTxt($flAlterno);
				//Abrimos nuevamente los archivos para pasar la información
				$fl = $this->AbrirArchivoTxt($this->strRutaFlEncabezado,'w');
				$flAlterno = $this->AbrirArchivoTxt($this->strRutaFlEncabezado.'Auxiliar','r');
				while(!feof($flAlterno)){
					$strLinea = fgets($flAlterno);
					$strDatos=explode("%",trim($strLinea));	
					if(($strDatos[0])!=""){
						fwrite($fl,$strDatos[0]."%".$strDatos[1]."%".$strDatos[2]."%".$strDatos[3]."%".$strDatos[4]."%".$strDatos[5]."%".$strDatos[6]."%".$strDatos[7]."%".$strDatos[8]."%".$strDatos[9]."%".$strDatos[10]."%".$strDatos[11]."%".$strDatos[12]."%".$strDatos[13]."%".$strDatos[14]."%".$strDatos[15]."%".$strDatos[16]."%".$strDatos[17].PHP_EOL);
					}	
				}
				//Cerrarmos los archivos txt
				$this->CerrarTxt($fl);
				$this->CerrarTxt($flAlterno);
			}
			//Metodo para duplicar un pedido
			public function DuplicarPedido(){
				$intNroPedido=trim($_POST['intNroPedido']);
				//Datos del cliente para duplicar
				$strClienteCedula = trim($_POST['strCedula']);
				$strClienteNombre =  trim($_POST['strNombre']);
				$strClienteCiudad =  trim($_POST['strCiudad']);
				$strClienteTelefono = trim($_POST['intTelefono1']);
				$strClienteCelular =  trim($_POST['intTelefono2']);
				$strClienteDireccion1 =   trim($_POST['strDireccion1']);
				$strClienteDireccion2 =  trim($_POST['strDireccion2']);
				$strClienteCartera =  trim($_POST['strCartera']);
				$strClienteCupo =  trim($_POST['strCupo']);
				$strClientePrecio= trim($_POST['intPrecio']);
				//Obteniendo el consecutivo del txt
				$fl=file($this->strRutaFlConsecutivo.'.txt');
				$intConsecutivo=trim((string)end($fl));
				//Validando si el consecutivo tiene items para crear el detalle o crear el consecutivo con su detalle
				$flDetalle=file($this->strRutaFlDetalle.$intConsecutivo.'.txt');
				$strDetallePd=end($flDetalle);
				if(trim($strDetallePd)==''){
					//Eliminamos el archivo detalle
					unlink($this->strRutaFlDetalle.$intConsecutivo.'.txt');
					//Copiamos el detalle actual al nuevo
					copy($this->strRutaFlDetalle.$intNroPedido.'.txt', $this->strRutaFlDetalle.$intConsecutivo.'.txt');
					//Modificamos el encabezado del pedido siempre y cuando este en la lista si no se asocia normal al pedido
					if($this->ActualizarEncabezadoDuplicandoPedido($intConsecutivo,$strClienteCedula,$strClienteNombre,$strClienteCiudad,$strClienteTelefono,$strClienteCelular,$strClienteDireccion1,$strClienteDireccion2,$strClienteCartera,$strClienteCupo,$strClientePrecio)==0){
						//Asociar cliente al pedido cuando no se encuentre en el archivo txt encabezado
						$_POST['intNroPedido']=$intConsecutivo;
						$this->AsociarPedidoCliente();
					}
					echo $intConsecutivo;
				}else{
					//Creando el siguiente consecutivo cuando el detalle tiene items
					$intNroPedidoSiguiente=($intConsecutivo+1);
					$fl = $this->AbrirArchivoTxt($this->strRutaFlConsecutivo,'w');
					fwrite($fl,$intNroPedidoSiguiente.PHP_EOL);
					$this->CerrarTxt($fl);
					copy($this->strRutaFlDetalle.$intNroPedido.'.txt', $this->strRutaFlDetalle.$intNroPedidoSiguiente.'.txt');
					$fp = fopen("../../../../../PedidosSisve/PedidoAuxiliarDetalle".$intNroPedidoSiguiente.".txt","w");
					fclose($fp);
					chmod("../../../../../PedidosSisve/PedidoDetalle".$intNroPedidoSiguiente.".txt",0777);
					chmod("../../../../../PedidosSisve/PedidoAuxiliarDetalle".$intNroPedidoSiguiente.".txt",0777);
					$_POST['intNroPedido']=$intNroPedidoSiguiente;
					$this->AsociarPedidoCliente();
					
					echo $intNroPedidoSiguiente;
				}
			}
			private function ActualizarEncabezadoDuplicandoPedido($intNroPedido,$strClienteCedula,$strClienteNombre,$strClienteCiudad,$strClienteTelefono1,$strClienteTelefono2,$strClienteDireccion1,$strClienteDireccion2,$strClienteCartera,$strClienteCupo,$strClientePrecio){
				$blnEstadoActualizarCliente=0;
				$fl = $this->AbrirArchivoTxt($this->strRutaFlEncabezado,'r');
				$flAlterno = $this->AbrirArchivoTxt($this->strRutaFlEncabezado.'Auxiliar','w');
				//Data al auxiliar
				while(!feof($fl)){
					$strLinea = fgets($fl);
					$strDatos=explode("%",trim($strLinea));	
					if(($strDatos[0])!=""){
						if($strDatos[0]!=$intNroPedido){
							fwrite($flAlterno,$strDatos[0]."%".$strDatos[1]."%".$strDatos[2]."%".$strDatos[3]."%".$strDatos[4]."%".$strDatos[5]."%".$strDatos[6]."%".$strDatos[7]."%".$strDatos[8]."%".$strDatos[9]."%".$strDatos[10]."%".$strDatos[11]."%".$strDatos[12]."%".$strDatos[13]."%".$strDatos[14]."%".$strDatos[15]."%".$strDatos[16]."%".$strDatos[17].PHP_EOL);
						}else{
							fwrite($flAlterno,$strDatos[0]."%".$strClienteCedula."%".$strClienteNombre."%".$strClienteCartera."%".$strClienteTelefono1."%".$strClienteTelefono2."%".$strClienteDireccion1."%".$strClienteDireccion2."%".$strClienteCiudad."%".$strClienteCupo."%".$strClientePrecio."%1%01-01-01%0%N%01-01-01%Sin Observación".PHP_EOL);
							$blnEstadoActualizarCliente=1;
						}
					}	
				}
				//Cerrarmos los archivos txt
				$this->CerrarTxt($fl);
				$this->CerrarTxt($flAlterno);
				//Abrimos nuevamente los archivos para pasar la información
				$fl = $this->AbrirArchivoTxt($this->strRutaFlEncabezado,'w');
				$flAlterno = $this->AbrirArchivoTxt($this->strRutaFlEncabezado.'Auxiliar','r');
				while(!feof($flAlterno)){
					$strLinea = fgets($flAlterno);
					$strDatos=explode("%",trim($strLinea));	
					if(($strDatos[0])!=""){
						fwrite($fl,$strDatos[0]."%".$strDatos[1]."%".$strDatos[2]."%".$strDatos[3]."%".$strDatos[4]."%".$strDatos[5]."%".$strDatos[6]."%".$strDatos[7]."%".$strDatos[8]."%".$strDatos[9]."%".$strDatos[10]."%".$strDatos[11]."%".$strDatos[12]."%".$strDatos[13]."%".$strDatos[14]."%".$strDatos[15]."%".$strDatos[16]."%".$strDatos[17].PHP_EOL);
					}	
				}
				//Cerrarmos los archivos txt
				$this->CerrarTxt($fl);
				$this->CerrarTxt($flAlterno);
				return $blnEstadoActualizarCliente;
			}
}



