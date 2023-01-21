<?php
date_default_timezone_set('America/Bogota');
require_once('../../WebService/clsWebServicePedidos.php');
$clsObjEstPedidos = new clsEstadoPedidos();

if(isset($_POST['CmdGetPedidos'])){
	$clsObjEstPedidos->GetPedidos();
}
if(isset($_POST['CmdGetDetallePedido'])){
	$clsObjEstPedidos->GetDetallePedido();
}
if(isset($_POST['CmdQuitarFinalizadoPedido'])){
	$clsObjEstPedidos->QuitarFinalizadoPedido();
}
if(isset($_POST['CmdEnvioDePedidoWs'])){
	$clsObjEstPedidos->EnvioDePedidoWs();
}

class clsEstadoPedidos
{
	
	//Variables Globales

	//Constructor
	function __construct()
	{
		$this->strRutaFlEncabezado="../../../../../PedidosSisve/PedidosEncabezado";
		$this->strRutaFlDetallePedido="../../../../../PedidosSisve/PedidoDetalle";
	}
	//Metodos Privados
	private function CerrarTxt($flArchivo){
		fclose($flArchivo);
	}
	private function AbrirArchivoTxt($fl,$strTipo){
		return fopen($fl.'.txt',$strTipo);
	}
	private function EliminarTxt($strFl){
		unlink($strFl.'.txt'); 
	}
	//Metodo Publicos

	//Metodo para obtener los pedidos finalizados por año y mes finalizados
	public function GetPedidos(){
		//Tipo de pedido 0 = PdFinalizados 1= PdEnviados
		$intTipoGetPedidos=trim($_POST['intTipoGetPedido']);
		//Parametros para la busqueda de pedidos enviados por mes y año
		$strMes=trim($_POST['strMesPd']);
		$strAnno=trim($_POST['strAnnoPd']);
		$fl=$this->AbrirArchivoTxt($this->strRutaFlEncabezado,'r');
		$strContDatos=array();
		$i=0;
		while (!feof($fl)) {
			$strDatos=fgets($fl);
			if(trim($strDatos)!=''){
				$strEstadoPedidoFinalizado=explode("%",trim($strDatos))[11];
				switch ($intTipoGetPedidos) {
					case 0:
					//Obtenemos el estado del pedido solo pasaran los tipo 2(Finalizados);
					if($strEstadoPedidoFinalizado=='2'){
						$strContDatos[$i]=$strDatos;
						$i++;
					}
					break;
					case 1:
					//Obtenemos el estado del pedido solo pasaran los tipo 3(Enviados);
					if($strEstadoPedidoFinalizado=='3'){
						//Validar Mes y Año para la busqueda de los pedidos
						$strFechaEnvioPd=explode("-",explode(" ",explode("%",trim($strDatos))[12])[0]);
						if($strFechaEnvioPd[1]==$strMes && $strFechaEnvioPd[2]==$strAnno){
							$strContDatos[$i]=$strDatos;
							$i++;
						}
					}
					break;
				}
			}
		}
		$this->CerrarTxt($fl);
		echo json_encode($strContDatos);
	}
	//Obtener detalle de un pedido
	public function GetDetallePedido(){
		$intNroPedido=trim($_POST['intNroPedido']);
		$fl=$this->AbrirArchivoTxt($this->strRutaFlDetallePedido.$intNroPedido,'r');
		$strContDatos=array();
		$i=0;
		while (!feof($fl)) {
			$strDatos=fgets($fl);
			if(trim($strDatos)!=''){
				$strContDatos[$i]=$strDatos;
				$i++;
			}
		}
		$this->CerrarTxt($fl);
		echo json_encode($strContDatos);
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
		echo 'Pedido Nro '.$intNroPedido.' quitado de la lista de finalizados.';
	}
	//Envio de pedidos 
	public function EnvioDePedidoWs(){
		$intNroPedido=(array($_POST['intNroPedido']));
		$blnTipoDeEnvio=trim($_POST['blnTipoDeEnvio']);
		$strFactCorreo=trim($_POST['strFactCorreo']);
		$strFactCelular=trim($_POST['strFactCelular']);
		$strFactTelefono=trim($_POST['strFactTelefono']);
		$strFactCiudad=trim($_POST['strFactCiudad']);
		//Envio de pedido individual estado 0 todos en estado 1.
		switch ($blnTipoDeEnvio) {
			case 1:
			$intNroPedido=$this->GetIntNroPedidoFinalizados();
			break;
		}
		//Obtener Vendedor
		$strVendedor=explode(",",$this->GetVendedorFl());
		//Creamos el json de cada pedido con su respectivo detalle
		$ArrayDetallePd=array();
		$ArrayDataEnvioPd=array();
		$k=0;
		for($j=0;$j<=sizeof($intNroPedido)-1;$j++){
			$fl = $this->AbrirArchivoTxt($this->strRutaFlEncabezado,'r');
			//Crear el json para ser envia por el ws
			while(!feof($fl)){
				$strLineaEncabezado= fgets($fl);
				$strDatos=explode("%",trim($strLineaEncabezado));	
				if(trim($strDatos[0])!=""){
					if($strDatos[0]==$intNroPedido[$j]){
						if($strDatos[11]=='3'){
							echo "3";
							return;
						}
						$flA = $this->AbrirArchivoTxt($this->strRutaFlDetallePedido.$intNroPedido[$j],'r');
						$ArrayEncabezadoPd=array(trim($strLineaEncabezado)."%".$strVendedor[0]."%".$strVendedor[1]."%"."1"."%"."1"."%".$strFactCorreo."%".$strFactCelular."%".$strFactTelefono."%".$strFactCiudad);
						$i=0;
						while(!feof($flA)){
							$strLineaDetallePd = fgets($flA);
							if(trim($strLineaDetallePd)!=''){
								$ArrayDetallePd[$i]=array(trim($strLineaDetallePd));
								$i++;
							}
						}
						$ArrayDataEnvioPd[$k]= array("EncabezadoPedido"=>$ArrayEncabezadoPd, "DetallePedido" => array($ArrayDetallePd));
						$k++;
					}
				}	
			}
		}
		$this->CerrarTxt($fl);
		$this->CerrarTxt($flA);	
		//Envio al webservice
		$clsWsPedido = new clsPedidosWebService();
		//var_dump($ArrayDataEnvioPd);
		$clsWsPedido->EnviarWsPedidos(json_encode($ArrayDataEnvioPd));
		//Envio del ArrayDaraEnvioPd a InmodaFantasy
		if($clsWsPedido->GetRespuestaWs()==1){
			for($i=0;$i<=sizeof($intNroPedido)-1;$i++){
				$this->CambiarEstadoPdFinalizadoAPdEnviado($intNroPedido[$i]);
			}
		}
		echo ($clsWsPedido->GetRespuestaWs());
	}
	//Metodo para obtener un vector de los pedidos finalizados para posteriormente ser mandados a inmodafantasy
	private function GetIntNroPedidoFinalizados(){
		$fl = $this->AbrirArchivoTxt($this->strRutaFlEncabezado,'r');
		$ArrayIntNroPedidos=array();
		$i=0;
		while(!feof($fl)){
			$strLinea = fgets($fl);
			$strDatos=explode("%",trim($strLinea));	
			if($strDatos[0]!=''){
				if($strDatos[11]=='2'){
					$ArrayIntNroPedidos[$i]=$strDatos[0];
					$i++;
				}
			}
		}
		$this->CerrarTxt($fl);
		return $ArrayIntNroPedidos;
	}
	//Metodo para cambiar estado del pedido finalizado a enviado a inmodafantasy de 2 a 3
	private function CambiarEstadoPdFinalizadoAPdEnviado($intNroPedido){
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
					fwrite($flAlterno,$strDatos[0]."%".$strDatos[1]."%".$strDatos[2]."%".$strDatos[3]."%".$strDatos[4]."%".$strDatos[5]."%".$strDatos[6]."%".$strDatos[7]."%".$strDatos[8]."%".$strDatos[9]."%".$strDatos[10]."%3%".$strDatos[12]."%".$strDatos[13]."%".$strDatos[14]."%".date('d-m-Y H:i:s')."%".$strDatos[16]."%".$strDatos[17].PHP_EOL);
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
	 //Get Vendedor
	public function GetVendedorFl(){
		$fileVendedores='../../../../../PedidosSisve/Sisve.cnf';
		$fp = fopen($fileVendedores,'r');
		$j=0;
		while(!feof($fp)){
			$strLinea = fgets($fp);
			$strVendedores=(explode("%",trim($strLinea)));
			if(($strVendedores)!=""){
				return $strVendedores[0].",".$strVendedores[1];
			}
		}
	}
}