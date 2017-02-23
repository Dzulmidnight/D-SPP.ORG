<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php');

mysql_select_db($database_dspp, $dspp);

if (!function_exists("GetSQLValueString")) {
	function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
	{
	  if (PHP_VERSION < 6) {
	    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
	  }

	  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

	  switch ($theType) {
	    case "text":
	      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
	      break;    
	    case "long":
	    case "int":
	      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
	      break;
	    case "double":
	      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
	      break;
	    case "date":
	      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
	      break;
	    case "defined":
	      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
	      break;
	  }
	  return $theValue;
	}
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$row_informes = mysql_query("SELECT informe_general.*, empresa.abreviacion FROM informe_general INNER JOIN empresa ON informe_general.idempresa = empresa.idempresa", $dspp) or die(mysql_error());
$total_informes = mysql_num_rows($row_informes);
$plataformas_spp = array('Ecuador', 'Perú', 'Colombia', 'Guatemala');

function redondear_dos_decimal($valor) { 
   $float_redondeado=round($valor * 100) / 100; 
   return $float_redondeado; 
}
?>

<h4 style="color:#2c3e50">Resumen</h4>
<div class="row">
	<div class="col-md-6">
		<table class="table table-bordered table-condensed" style="font-size:12px;">
			<thead>
				<tr class="warning">
					<th>	
						Año: 
						<select name="anio_reporte">
							<option>Todos</option>
							<option>2017</option>
							<option>2016</option>
						</select>
					</th>
					<th class="text-center" colspan="4">EMPRESAS</th>
				</tr>
				<tr>
					<th class="text-center">Año</th>
					<th class="text-center">ID informe general</th>
					<th class="text-center">Estado</th>
					<th class="text-center">Empresa</th>
					<th class="text-center">Total a pagar</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$total = 0;
				if($total_informes == 0){
					echo '<tr><td colspan="5">No se encontraron registros</td></tr>';
				}else{
					while($informes = mysql_fetch_assoc($row_informes)){
						$total += $informes['total_informe'];
						echo "<tr>";
							echo '<td>'.date('Y',$informes['ano']).'</td>';
							echo '<td><a href="?REPORTES&informe_compras='.$informes['idinforme_general'].'"><span class="glyphicon glyphicon-list-alt"></span>'.$informes['idinforme_general'].'</a></td>';
							echo '<td>'.$informes['estado_informe'].'</td>';
							echo '<td>'.$informes['abreviacion'].'</td>';
							echo '<td>'.$informes['total_informe'].' USD</td>';
						echo '</tr>';
					}
					echo '<tr><td colspan="5"><b>Suma total: <span style="color:#e74c3c">'.$total.'</span> USD</b></td></tr>';
				}
				 ?>
			</tbody>
		</table>
	</div>
	<div class="col-md-6">

		<?php 
		if(!empty($_GET['informe_compras'])){
			$idinforme_general = $_GET['informe_compras'];
			//$row_informe = mysql_query("SELECT * FROM formato_compras WHERE idinforme_general = '$idinforme_general'", $dspp) or die(mysql_error());

		?>
			<table class="table table-bordered table-condensed" style="font-size:12px;">
				<thead>
					<th>	
						Año: 
						<select name="anio_reporte">
							<option>Todos</option>
							<option>2017</option>
							<option>2016</option>
						</select>
					</th>
					<tr>
						<th colspan="2"><h4>Distribución plataformas SPP</h4></th>
					</tr>
					<tr>
						<th>Plataforma SPP</th>
						<th>VENTAS TOTALES</th>
						<th>REEMBOLSO</th>
					</tr>
				</thead>
				<tbody>
				<?php 
				foreach ($plataformas_spp as  $value) {
					$row_reembolso = mysql_query("SELECT * FROM formato_compras WHERE pais = '$value'", $dspp) or die(mysql_error());
					$total_reembolso = mysql_num_rows($row_reembolso);
				?>
					<tr>
						<td><?php echo $value; ?></td>
						<td><?php echo $total_reembolso; ?></td>
						<td><?php echo $value; ?></td>
					</tr>
				<?php
				}
				 ?>
				</tbody>
			</table>
		<?php
		}else{
		?>
			<table class="table table-bordered table-condensed" style="font-size:12px;">
				<thead>
					<tr class="info">
						<th>	
							Año: 
							<select name="anio_reporte">
								<option>Todos</option>
								<option>2017</option>
								<option>2016</option>
							</select>
						</th>
						<th colspan="3">Distribución plataformas SPP</th>
					</tr>
					<tr>
						<th class="text-center">Plataforma SPP</th>
						<th class="text-center">Nº transacciones</th>
						<th class="text-center">Compras totales</th>
						<th class="text-center">Reembolso(10%)</th>
					</tr>
				</thead>
				<tbody>
				<?php
				$total_compras = 0;
				$total_reembolso = 0;
				foreach ($plataformas_spp as  $value) {
					$row_reembolso = mysql_query("SELECT COUNT(idformato_compras) AS 'total_formatos', pais, SUM(total_a_pagar) AS 'compras_totales' FROM formato_compras WHERE pais = '$value'", $dspp) or die(mysql_error());
					$reembolso = mysql_fetch_assoc($row_reembolso);
					$transacciones = mysql_num_rows($row_reembolso);
				?>
					<tr>
						<td><?php echo $value; ?></td>
						<td><?php echo $reembolso['total_formatos']; ?></td>
						<td><?php if(empty($reembolso['compras_totales'])){ echo 0; }else{ echo $reembolso['compras_totales']; } ?> USD</td>
						<td><?php echo round(($reembolso['compras_totales'] * 0.10), 2); ?> USD</td>
					</tr>
				<?php
				$total_compras += $reembolso['compras_totales'];

				}
				 ?>
				 	<tr>
				 		<td></td>
				 		<td></td>
				 		<td><?php echo $total_compras; ?> USD</td>
				 		<td><?php echo round(($total_compras * 0.10),2); ?> USD</td>
				 	</tr>
				</tbody>
			</table>

		<?php
		}
		 ?>
	</div>
</div>
