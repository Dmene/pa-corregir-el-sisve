<?php
use Mpdf\Mpdf;
require_once '../../vendor/autoload.php';

$mpdf = new Mpdf(['margin_top' => 5,
    'margin_left' => 5,
    'margin_right' => 5,
    'margin_bottom' => 15,
    'mirrorMargins' => true,
    'default_font' => 'Century Gothic',
    'format' => 'Letter']);

$mpdf->AliasNbPages();


$mpdf->WriteHTML('<sethtmlpageheader name="firstpage" value="on" show-this-page="1" />');
$mpdf->WriteHTML('<sethtmlpageheader name="otherpages" value="on" />');


//Obtener encabezado
$intNroPedido=$_GET['intNroPedido'];
$fp = fopen("../../../../../PedidosSisve/PedidosEncabezado.txt","r");
$ArrayEncabezado=array();
while(!feof($fp)) {
    $strLineaTxt = trim(fgets($fp));
    if(trim($strLineaTxt)!=""){
        $strData=explode("%",$strLineaTxt);
        if($strData[0]===$intNroPedido){
            array_push($ArrayEncabezado,array("intNroPedido"=>$strData[0],"strIdentificacion"=>$strData[1],"strNombreTercero"=>$strData[2],"strCiudad"=>$strData[8],"strObservacion"=>$strData[16]));
            break;
        } 
    }   
}
fclose($fp);

//Validar si el pedido ya tiene asociado un cliente.
if(sizeof($ArrayEncabezado)==0){
    header("Location:/ownCloud/datapr/Sisve/View/Pedidos/?Error=0");
}
//titulo del PDF
$mpdf->setTitle('Pedido_'.$ArrayEncabezado[0]['strNombreTercero']);


//OBTENER DETALLEPEDIDO
$intNroPedido=$_GET['intNroPedido'];
$fp = fopen("../../../../../PedidosSisve/PedidoDetalle".$ArrayEncabezado[0]['intNroPedido'].".txt","r");
$strDetallePedido='';
$j=1;
$intTotalPedido=0;
while(!feof($fp)) {
    $strLineaTxt = trim(fgets($fp));
    if(trim($strLineaTxt)!=""){
        $strData=explode("%",$strLineaTxt);
        $ArrayPrecioLetra= array('N','Z','Y','W','V','U','S','R','P','O');
        for($i=0;$i<=9;$i++){
            $strData[5]=str_replace($ArrayPrecioLetra[$i],$i, $strData[5]);
        }
        $strDetallePedido.=
        "
        <tr>
            <td>".$j."</td>
            <td>".$strData[0]."</td>
            <td>".$strData[2]."</td>
            <td>".$strData[9]."</td>
            <td>".$strData[3]."</td>
            <td>".$strData[4]."</td>
            <td>".$strData[8]."</td>
            <td>".number_format($strData[5])."</td>
        </tr>
        ";
        $intTotalPedido+=($strData[5]*$strData[4]);
        $j++;
        $mpdf->SetFooter('
            <table width="100%">
                <tr style="font-size:10;">
                    <td width="33%">{DATE j-m-Y}</td>
                    <td width="33%" style="text-align: right;">{PAGENO}/{nbpg}</td>
                </tr>
            </table>');
    }   
}
fclose($fp);
//OBTENER DATOS DEL VENDEDOR
$fp = fopen("../../../../../PedidosSisve/Sisve.cnf","r");
$ArrayVendedor=array();
while(!feof($fp)) {
    $strLineaTxt = trim(fgets($fp));
    if(trim($strLineaTxt)!=""){
        $strData=explode("%",$strLineaTxt);
        array_push($ArrayVendedor,array("strCedula"=>$strData[1]));
    }   
}
fclose($fp);

//PDF
$mpdf->WriteHTML('
    <style type="text/css">
    .Contendio-EncabezadoDetalleProductos{
        background: #ddd;
    }
    .w-15{
        width:14%;
    }
    .w-5{
        width:4%;
    }
    .w-100{
        width:100%;
    }
    .CtnDetalleProductos tr td{
        border-bottom:1px solid #ddd;
        font-size:11px;
    }
    </style>
    <table style="width: 100%;">
    <tr>
    <td style="width: 50%;text-align: left;">
    <b>Identificación:</b>'.$ArrayEncabezado[0]["strIdentificacion"].'<br>
    <b>Cliente:</b>'.$ArrayEncabezado[0]["strNombreTercero"].'<br>
    <b>Ciudad:</b>'.$ArrayEncabezado[0]["strCiudad"].'
    </td>
    <td style="width: 50%;text-align: right;">
    <b>Nro Pedido:</b>'.$ArrayEncabezado[0]["intNroPedido"].'<br>  
    <b>Fecha Pedido:</b>'.date('Y-m-d').'<br>
    <b>Vendedor:</b>'.$ArrayVendedor[0]["strCedula"].'
    </td>
    </tr>
    </table>
    <hr>
    <table class="CtnDetalleProductos" style="width: 100%;margin: auto;" cellspacing="0">   
    <tr class="Contendio-EncabezadoDetalleProductos">
    <td class="w-5"><b>#</b></td>
    <td class="w-14"><b>Referencia</b></td>
    <td class="w-14"><b>Descripción</b></td>
    <td class="w-14"><b>Observacion</b></td>
    <td class="w-14"><b>Color</b></td>
    <td class="w-14"><b>Cantidad</b></td>
    <td class="w-14"><b>Und</b></td>
    <td class="w-14"><b>Precio</b></td>
    </tr>
    '.$strDetallePedido.'
    </table>
    <hr>
    <div class="w-100" style="text-align: center;font-size: 20px;">
    **'.$ArrayEncabezado[0]["strObservacion"].'**
    </div>
    <div style="text-align: right;">Total: '.number_format($intTotalPedido).'</div>');




$mpdf->Output('Pedido'.date('Y-m-d').'_'.$ArrayEncabezado[0]['strNombreTercero'].'.pdf','I');


?>