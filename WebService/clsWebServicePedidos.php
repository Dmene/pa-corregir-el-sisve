<?php
require_once('../../Librerias/Nusoap/nusoap.php');
class clsPedidosWebService 
{
	private $urlWebService;
	private $strRespuestaWs;
	private $strParametros;
	function __construct()
	{
		$this->urlWebService='http://181.205.133.91:8888/WebServiceInmodaExterno/WebService/WebServicePedido.php?wsdl';
		$this->strRespuestaWs='';
		$this->strParametros='';
	}
	public function ConsultarWebService($strMetodo,$blnParametros){
		$wsCliente='';
		$strWsRespuesta='';
		try {
			if($blnParametros){
				$wsCliente = new nusoap_client($this->urlWebService, 'wsdl');
				$strWsRespuesta=$wsCliente->call($strMetodo,$this->strParametros);
			}else{
				$wsCliente = new SoapClient($this->urlWebService);
			    $strWsRespuesta=$wsCliente->$strMetodo();
			}
			return $strWsRespuesta;
		} catch (Exception $e) {
			return 22222;
		}
		
	}
	public function GetRespuestaWs(){
		return $this->strRespuestaWs;
	}
	public function EnviarWsPedidos($strJsonPedido){
		$this->strParametros= array('strJsonPedido'=>$strJsonPedido,'US'=>base64_encode('InmodaFantasy$%&'),'CC'=>base64_encode('InmodaFantasy2019*.*$%'));
		$this->strRespuestaWs=$this->ConsultarWebService('CapturarJsonPedidosVendedor',true);
	}
}