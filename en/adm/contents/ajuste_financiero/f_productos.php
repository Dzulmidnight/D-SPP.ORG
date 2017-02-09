FORMATOS TRIMESTRALES DE PRODUCTOS TERMINADOS

<hr style="margin-bottom:0px;">
<div class="btn-group" role="group" aria-label="..." style="padding-top:0px;">
	<a class="btn btn-sm <?php if($_GET['f_producto'] == 'opp'){ echo 'btn-success';}else{ echo 'btn-default';} ?>" href="?FINANZAS&f_producto=opp"><span class="glyphicon glyphicon-apple" aria-hidden="true"></span> Formato Productos Terminados(OPP)</a>
	<a class="btn btn-sm <?php if($_GET['f_producto'] == 'empresa'){ echo 'btn-success';}else{ echo 'btn-default';} ?>" href="?FINANZAS&f_producto=empresa"><span class="glyphicon glyphicon-apple" aria-hidden="true"></span> Formato Productos Terminados(COM)</a>
</div>

<?php 
if($_GET['f_producto'] == 'opp'){
	include('trim_producto_opp.php');
}else if($_GET['f_producto'] == 'empresa'){
	include('trim_producto_empresa.php');
}
 ?>