FORMATO DE INFORMES TRIMESTRALES COMPRA-VENTA
<hr style="margin-bottom:0px;">
<div class="btn-group" role="group" aria-label="..." style="padding-top:0px;">
	<a class="btn btn-sm <?php if($_GET['f_informe'] == 'ventas'){ echo 'btn-success';}else{ echo 'btn-default';} ?>" href="?FINANZAS&f_informe=ventas"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Formato Trimestral Ventas(OPP)</a>
	<a class="btn btn-sm <?php if($_GET['f_informe'] == 'compras'){ echo 'btn-success';}else{ echo 'btn-default';} ?>" href="?FINANZAS&f_informe=compras"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Formato Trimestral Compras(COM)</a>
</div>

<?php 
if($_GET['f_informe'] == 'ventas'){
	include('informe_trim_ventas.php');
}else if($_GET['f_informe'] == 'compras'){
	include('informe_trim_compras.php');
}
 ?>