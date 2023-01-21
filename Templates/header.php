<!-- SIDEBAR -->
<?php
$strRuta='../../../../../PedidosSisve/Sisve.cnf';
$src='../../../../../ownCloud/fotos_nube/FOTOS  POR SECCION CON PRECIO/';
if(file_exists($strRuta)){
  $strNombreVendedor=
  $fp = fopen($strRuta, "r");
  while(!feof($fp)){
    $strLinea=fgets($fp);
    if($strLinea){
      $strLinea = explode("%",$strLinea);
      break;
    }  
  }
}

?>
<div id="contenedor-sidebar"  onclick="OcultarSidebarPedidos()"></div>
<div id="sidebar-pedidos">
  <div class="text-center p-1 mt-2" >
    <img src="..\..\..\Sisve\img\profile2.png" class="img-fluid border-radius-50" width="50"><br>
    <label><span class="badge badge-secondary" id='lblVendedorSidebar' style="font-size: 10px;"><?php echo  $strLinea[1];?></span></label>
  </div>
  <hr class="mb-0">
  <ul>
    <li>
      <a href="../Pedidos/" ><i class="fas fa-file-invoice"></i> Pedidos</a>
    </li>
    <li>
      <a href="../EstadoPedidos/"><i class="fas fa-grip-horizontal"></i> Estado Pedidos</a>
    </li>
    <li>
      <a href="../Configuracion/"><i class="fas fa-cogs"></i> Configuración</a>
    </li>
  </ul>
</div>

<nav class="text-center bg-white bb-2" id='nav'>
  <img src="..\..\img\logo_empresa.png" class="img-fluid" width="150">
  <button class="float-left btn mt-3 ml-3 btnSisve-primary" onclick='MostrarSidebarPedidos()'>
    <i class="fas fa-bars"></i>
  </button>
</nav>