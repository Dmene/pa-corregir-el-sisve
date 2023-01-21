<?php
date_default_timezone_set('America/Bogota');
$clsObjConfiguracion = new clsConfiguracion();

if(isset($_POST['CmdValidarConfiguracion'])){
	$clsObjConfiguracion->ValidarConfiguracion();
}
if(isset($_POST['CmdGetVendedores'])){
    $clsObjConfiguracion->GetVendedores();
}
if(isset($_POST['CmdAceptarVendConfiguracion'])){
    $clsObjConfiguracion->GuardarConfiguracionVendedor();
}
if(isset($_POST['CmdValidarClaveAccesoCf'])){
    $clsObjConfiguracion->ValidarClaveAcceso();
}
class clsConfiguracion
{
    private $strConfFile;

    function __construct()
    {
       $this->strConfFile='../../../../../PedidosSisve/Sisve.cnf';
    }   
    //Listar vendedores para la respectiva configuración
    public function GetVendedores(){
        $fileVendedores='../../dataCryptVd.txt';
        $fp = fopen($fileVendedores,'r');
        $strCtnVendedores=array();
        $j=0;
        while(!feof($fp)){
            $strLinea = fgets($fp);
            $strCtnVendedores[$j]=$strLinea;
            $j++;
        }
        echo json_encode($strCtnVendedores);
    }
    //Validar configuración vendedor
    public function ValidarConfiguracion(){
        if(!file_exists("../../../../../PedidosSisve")){
            mkdir("../../../../../PedidosSisve", 0777, true);
        }
        if (file_exists($this->strConfFile)){
            echo 'true';
        }else{
            echo 'false';
        }
    }
    //Guardar configuración vendedor 
    public function GuardarConfiguracionVendedor(){
        $strCedulaVd=trim($_POST['strCedulaVd']);
        $strNombreVd=trim($_POST['strNombreVd']);
        $strVendedor=explode(",",$this->GetVendedorFl($strCedulaVd));
        $fp = fopen($this->strConfFile,"w");
        fwrite($fp,$strVendedor[0]."%".$strVendedor[1]."%".$strVendedor[2]."%".$strVendedor[3].PHP_EOL);
        fclose($fp);
        echo 1;
    }
    //Get Vendedor
    public function GetVendedorFl($strCedulaVd){
        $fileVendedores='../../dataCryptVd.txt';
        $fp = fopen($fileVendedores,'r');
        $strCtnVendedores=array();
        $j=0;
        while(!feof($fp)){
            $strLinea = fgets($fp);
            $strVendedores=(explode(",",trim($strLinea)));
            $strZonas = '0';
            $strLineas = '0';
            if(($strVendedores)!=""){
                if(trim($strVendedores[0])==$strCedulaVd){
                    return $strVendedores[0].",".$strVendedores[1].",". $strZonas.",".$strLineas;
                }
            }
        }
    }
    //Validar Clave de acceso
    public function ValidarClaveAcceso(){
        $strClave=trim($_POST['strClave']);
        if( date("d-m-Y").'im123'===$strClave){
            echo "true";
        }else{
            echo "false";
        }
    }
}
