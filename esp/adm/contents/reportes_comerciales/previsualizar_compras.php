<?php
mysql_select_db($database_dspp, $dspp);

$ano_actual = date('Y', time());

$codigo_trim = $_GET['previsualizar_compras'];

$valores = explode("-", $codigo_trim);
//// AÑO DEL INFORME
$ano_informe = $valores[1];
$idempresa = $valores[2];
/// NUMERO DE INFORME TRIMETRAL
switch ($valores[0]) {
	case 'T1':
		$trim = 'trim1';
		$titulo_informe = 'PRIMER TRIMESTRE COMPRAS <span style="color:red"><span style="color:red">'.$ano_informe.'</span>';
		break;
	case 'T2':
		$trim = 'trim2';
		$titulo_informe = 'SEGUNDO TRIMESTRE COMPRAS <span style="color:red">'.$ano_informe.'</span>';
		break;
	case 'T3':
		$trim = 'trim3';
		$titulo_informe = 'TERCER TRIMESTRE COMPRAS <span style="color:red">'.$ano_informe.'</span>';
		break;
	case 'T4':
		$trim = 'trim4';
		$titulo_informe = 'CUARTO TRIMESTRE COMPRAS <span style="color:red">'.$ano_informe.'</span>';
		break;
	default:

		break;
}



//$query_informe = "SELECT formato_compras.*, intermediarios.primero, intermediarios.segundo, referencia_contrato.clave, referencia_contrato.fecha AS 'fecha_contrato', cantidad_total_contrato.peso AS 'c_t_c_peso', cantidad_total_contrato.unidad AS 'c_t_c_unidad', peso_total_reglamento.peso AS 'p_t_r_peso', peso_total_reglamento.unidad AS 'p_t_r_unidad', precio_total_unitario.precio AS 'p_t_u_precio', precio_total_unitario.unidad AS 'p_t_u_unidad', reconocimiento_organico.precio AS 'r_o_precio', reconocimiento_organico.unidad AS 'r_o_unidad', precio_sustentable.precio AS 'p_s_precio', precio_sustentable.unidad AS 'p_s_unidad', incentivo_spp.precio AS 'i_spp_precio', incentivo_spp.unidad AS 'i_spp_unidad', cuota_uso_reglamento.cuota AS 'c_u_r_cuota', cuota_uso_reglamento.unidad AS 'c_u_r_unidad' FROM formato_compras INNER JOIN intermediarios ON formato_compras.idformato_compras = intermediarios.idformato_compras INNER JOIN referencia_contrato ON formato_compras.idformato_compras = referencia_contrato.idformato_compras INNER JOIN cantidad_total_contrato ON formato_compras.idformato_compras = cantidad_total_contrato.idformato_compras INNER JOIN peso_total_reglamento ON formato_compras.idformato_compras = peso_total_reglamento.idformato_compras INNER JOIN precio_total_unitario ON formato_compras.idformato_compras = precio_total_unitario.idformato_compras INNER JOIN reconocimiento_organico ON formato_compras.idformato_compras = reconocimiento_organico.idformato_compras INNER JOIN precio_sustentable ON formato_compras.idformato_compras = precio_sustentable.idformato_compras INNER JOIN incentivo_spp ON formato_compras.idformato_compras = incentivo_spp.idformato_compras INNER JOIN cuota_uso_reglamento ON formato_compras.idformato_compras = cuota_uso_reglamento.idformato_compras WHERE idempresa = $idempresa";

//$query_informe = "SELECT * FROM "
//$row_informe = mysql_query($query_informe, $dspp) or die(mysql_error());


$row_informe = mysql_query("SELECT informe_general.*, trim1.total_trim1, trim2.total_trim2, trim3.total_trim3, trim4.total_trim4, SUM(trim1.total_trim1 + trim2.total_trim2 + trim3.total_trim3 + trim4.total_trim4) AS 'balance_final' FROM informe_general LEFT JOIN trim1 ON informe_general.trim1 = trim1.idtrim1 LEFT JOIN trim2 ON informe_general.trim2 = trim2.idtrim2 LEFT JOIN trim3 ON informe_general.trim3 = trim3.idtrim3 LEFT JOIN trim4 ON informe_general.trim4 = trim4.idtrim4 WHERE informe_general.idempresa = $idempresa AND FROM_UNIXTIME(informe_general.ano, '%Y') = $ano_informe", $dspp) or die(mysql_error());
$informe_general = mysql_fetch_assoc($row_informe);
?>

<a class="btn btn-warning"  href="?REPORTES&ingresos=informe_compras"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Regresar</a> <h4 style="display:inline"><?php echo $titulo_informe; ?></h4>
<?php 
	if(!isset($informe_general['trim2'])){
		echo "<h4 class='alert alert-danger'><span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span> Por disposiciones de SPP Global debe iniciar la captura de transacciones en el \"Trimestre 2\".</h4>";
	}
 ?>
<table class="table table-bordered" style="font-size:11px;">
	<thead>
		<tr class="success">
			<th class="text-center">#</th>
			<th class="text-center">#SPP</th>
			<th class="text-center">Nombre OPP proovedora</th>
			<th class="text-center">País de OPP proveedora</th>
			<th class="text-center">Fecha de Facturación</th>
			<th class="text-center">Primer Intermediario</th>
			<th class="text-center">Segundo Intermediario</th>
			<th colspan="2" class="text-center">Referencia Contrato Original con OPP</th>
			<th class="text-center">Producto General</th>
			<th class="text-center">Producto Especifico</th>
			<th class="text-center">¿Producto terminado?</th>
			<th class="text-center">¿Se exporta?</th>
			<th class="text-center">Valor ingredientes</th>
			<th colspan="2" class="text-center">Cantidad Total Conforme Factura</th>
			<th class="text-center">Precio Sustentable Mínimo</th>
			<th class="text-center">Reconocimiento Orgánico</th>
			<th class="text-center">Incentivo SPP</th>
			<th class="text-center">Otros premios</th>
			<th class="text-center">Precio Total Unitario pagado</th>
			<th class="text-center">Valor Total Contrato</th>
			<th class="text-center">Cuota de Uso Reglamento</th>
			<th class="text-center">Total a pagar</th>
		</tr>
	</thead>
	<tbody>
		<?php 
		$balance_final = 0;


			$row_registro = mysql_query("SELECT formato_compras.* FROM formato_compras WHERE formato_compras.idtrim = '$informe_general[$trim]'", $dspp) or die(mysql_error());
			$total_contrato = 0;
			$contador = 1;
			$total_trim1 = 0;
			while($formato = mysql_fetch_assoc($row_registro)){
			?>
				<tr>
					<td><?php echo $contador; ?></td>
					<td><?php echo $formato['spp']; ?></td>
					<td><?php echo $formato['opp']; ?></td>
					<td><?php echo $formato['pais']; ?></td>
					<td><?php echo date('d/m/Y',$formato['fecha_facturacion']); ?></td>
					<td><?php echo $formato['primer_intermediario']; ?></td>
					<td><?php echo $formato['segundo_intermediario']; ?></td>
					<td><?php echo $formato['clave_contrato']; ?></td>
					<td>
						<?php 
						if(isset($formato['fecha_contrato'])){
							echo date('d/m/Y', $formato['fecha_contrato']);
						}
					
						?>
					</td>
					<td><?php echo $formato['producto_general']; ?></td>
					<td><?php echo $formato['producto_especifico']; ?></td>
					<td><?php echo $formato['producto_terminado']; ?></td>
					<td><?php echo $formato['se_exporta']; ?></td>
					<td><?php echo $formato['valor_ingredientes']; ?></td>
					<td><?php echo $formato['unidad_cantidad_factura']; ?></td>
					<td><?php echo number_format($formato['cantidad_total_factura'],2); ?></td>
					<td><?php echo $formato['precio_sustentable_minimo']; ?></td>
					<td><?php echo $formato['reconocimiento_organico']; ?></td>
					<td><?php echo $formato['incentivo_spp']; ?></td>
					<td><?php echo $formato['otros_premios']; ?></td>
					<td><?php echo $formato['precio_total_unitario']; ?></td>
					<td><?php echo number_format($formato['valor_total_contrato'],2); ?></td>
					<td><?php echo $formato['cuota_uso_reglamento']; ?></td>
					<td style="background-color:#e74c3c;color:#ecf0f1;"><?php echo number_format($formato['total_a_pagar'],2); ?></td>
				</tr>
			<?php
			$contador++;
			$total_contrato = $total_contrato + $formato['valor_total_contrato'];
			$total_trim1 = $total_trim1 + $formato['total_a_pagar'];
			}
			echo "<tr>
				<td colspan='21' class='text-right warning'><h5>PRIMER TRIMESTRE</h5></td>
				<td style='background-color:#2c3e50;color:#ecf0f1' class='danger'>".number_format($total_contrato,2)."</td>
				<td class='warning'></td>
				<td style='background-color:#2c3e50;color:#ecf0f1' class='danger'>".number_format($total_trim1,2)."</td>
			</tr>";
	
		?>
	</tbody>
</table>