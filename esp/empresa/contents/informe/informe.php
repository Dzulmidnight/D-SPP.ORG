<div class="btn-group" role="group" aria-label="...">
	<a <?php if(isset($_GET['select'])){ echo "class='btn btn-sm btn-primary'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> href="?INFORME&select"><span class="glyphicon glyphicon-book" aria-hidden="true"></span> Informe General</a>
	<a <?php if(isset($_GET['contacts'])){ echo "class='btn btn-sm btn-primary'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> href="?INFORME&contacts"><span class="glyphicon glyphicon-file" aria-hidde="true"></span> Nuevo Formato</a>
</div>


<?php /*

if(isset($_GET['select'])){
	include ("empresa_select.php");
}
else if(isset($_GET['add'])){
	include ("empresa_add.php");
}
else if(isset($_GET['detail'])){
	include ("empresa_detail.php");
}else if(isset($_GET['contacts'])){
	include("empresa_contacts.php");
}*/

?>

<h3>INFORME TRIMESTRAL GENERAL</h3>

<table border="1" style="font-size:11px;">
	<thead>
		<tr>
			<th class="text-center">#</th>
			<th class="text-center">OPP</th>
			<th class="text-center">País de la OPP</th>
			<th class="text-center">Fecha de Compra</th>
			<th class="text-center">Primer Intermediario</th>
			<th class="text-center">Segundo Intermediario</th>
			<th class="text-center">Tipo de Producto</th>
			<th class="text-center">Referencia Contrato Original con OPP</th>
			<th class="text-center">Producto Especifico de acuerdo al contrato original</th>
			<th class="text-center">Cantidad Total Conforme Contrato</th>
			<th class="text-center">Peso Total Conforme Unidad de Medida Reglamento de Uso</th>
			<th class="text-center">Precio Total Unitario</th>
			<th class="text-center">Precio Sustentable Minimo</th>
			<th class="text-center">Reconocimiento Orgánico</th>
			<th class="text-center">Incentivo SPP</th>
			<th class="text-center">Valor Total Contrato</th>
			<th class="text-center">Cuota de Uso Reglamento</th>
			<th class="text-center">Total a Pagar</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
	</tbody>
</table>