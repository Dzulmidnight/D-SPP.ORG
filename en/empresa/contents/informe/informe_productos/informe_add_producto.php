<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php');

mysql_select_db($database_dspp, $dspp);

if (!isset($_SESSION)) {
  session_start();
	
	$redireccion = "../index.php?EMPRESA";

	if(!$_SESSION["autentificado"]){
		header("Location:".$redireccion);
	}
}

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

$idempresa = $_SESSION['idempresa'];
$idtrim = $_GET['idtrim'];
$anio_actual = date('Y',time());
$row_configuracion = mysql_query("SELECT * FROM porcentaje_ajuste WHERE anio = $anio_actual", $dspp) or die(mysql_error());
$configuracion = mysql_fetch_assoc($row_configuracion);

if(isset($_POST['agregar_formato']) && $_POST['agregar_formato'] == 1){
	
	$fecha_registro = time();
	$tipo_moneda = 'USD';


		if(isset($_POST['pais'])){
			$pais = $_POST['pais'];
		}else{
			$pais = NULL;
		}
		if(isset($_POST['ventas_totales'])){
			$ventas_totales = $_POST['ventas_totales'];
		}else{
			$ventas_totales = NULL;
		}
		if(isset($_POST['tipo_moneda'])){
			$tipo_moneda = $_POST['tipo_moneda'];
		}else{
			$tipo_moneda = NULL;
		}

		//Iniciamos insertar formato_producto_empresa
			$insertSQL = sprintf("INSERT INTO formato_producto_empresa(idtrim, idempresa, pais, ventas_totales, tipo_moneda, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s)",
				GetSQLValueString($idtrim, "text"),
				GetSQLValueString($idempresa, "int"),
				GetSQLValueString($pais, "text"),
				GetSQLValueString($ventas_totales, "text"),
				GetSQLValueString($tipo_moneda, "text"),
				GetSQLValueString($fecha_registro, "int"));
			$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
			//$idformato_producto_empresa = mysql_insert_id($dspp);
		//Termina insertar formato compras
}
if(isset($_POST['eliminar_registro']) && $_POST['eliminar_registro'] != 0){
	$idregistro = $_POST['eliminar_registro'];

	$deleteSQL = sprintf("DELETE FROM formato_producto_empresa WHERE idformato_producto_empresa = $idregistro",
		GetSQLValueString($idregistro, "int"));
	$eliminar = mysql_query($deleteSQL, $dspp) or die(mysql_error());
}

?>

<div class="row">
	<div class="col-lg-12">
 		<?php 
 		if(isset($mensaje)){
 		?>
			<div class="alert alert-success alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<?php echo $mensaje; ?>
			</div>
		<?php
 		}
 		 ?>
	</div>
	<?php 
	$txt_trim = 'trim'.$_GET['trim'].'_producto';
	$txt_idtrim = 'idtrim'.$_GET['trim'].'_producto';
	$txt_estatus = 'estado_trim'.$_GET['trim'];
	$row_trim = mysql_query("SELECT * FROM $txt_trim WHERE $txt_idtrim = '$idtrim'", $dspp) or die(mysql_error());
	$trim = mysql_fetch_assoc($row_trim);
	?>
	<div class="col-md-12">
	<?php
	if($trim[$txt_estatus] == 'FINALIZADO'){
		echo "<p class='alert alert-danger'><span class='glyphicon glyphicon-ban-circle' aria-hidden='true'></span> You can no longer add more record to the <b>Quarterly Format $idtrim</b>, since it was completed.</p>";
	}else{
	?>
			<p class="alert alert-danger" style="margin-bottom:0px;padding:5px;"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> The total sales value must be expressed in US Dollars (USD)</p>
		 	<table class="table table-bordered table-condensed" style="font-size:11px;" id="tablaInforme">
		 		<thead>
		 			<tr class="success">
		 				<th class="text-center">
							#
		 				</th>
						<th class="text-center">Country of destination of finished product</th>
						<th class="text-center">Total sales value SPP</th>
						<th class="text-center">Type of currency</th>
		 			</tr>
		 		</thead>
		 		<tbody>

		 				<?php 
		 					switch ($_GET['trim']) {
		 						case '1':
		 							$num_trim = 'trim1_producto';
		 							break;
		 						case '2':
		 							$num_trim = 'trim2_producto';
		 							break;
		 						case '3':
		 							$num_trim = 'trim3_producto';
		 							break;
		 						case '4':
		 							$num_trim = 'trim4_producto';
		 							break;

		 						
		 						default:
		 							# code...
		 							break;
		 					}
							$row_registro = mysql_query("SELECT formato_producto_empresa.* FROM formato_producto_empresa WHERE formato_producto_empresa.idtrim  = '$informe_general_producto[$num_trim]'");
							$contador = 1;
							while($formato = mysql_fetch_assoc($row_registro)){
							?>
								<form action="" method="POST">
									<tr class="active">
										<td>
											<?php echo $contador; ?> <button type="submit" class="btn btn-sm btn-danger" data-toggle="tooltip" title="Delete record" name="eliminar_registro" value="<?php echo $formato['idformato_producto_empresa']; ?>" onclick="return confirm('Are you sure to delete the record?');"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
										</td>
										<td><?php echo $formato['pais']; ?></td>
										<td><?php echo $formato['ventas_totales']; ?></td>
										<td><?php echo $formato['tipo_moneda']; ?></td>
									</tr>
								</form>
							<?php
							$contador++;
							}
		 				 ?>
		<form class="form-horizontal" method="POST">
					<tr class="success">
						<td class="warning"></td> <!-- # -->

						<td class="warning"><!-- pais de la opp proveedora -->
			              <select name="pais" id="pais" class="form-control" required>
			                <option value="">Destination country</option>
			                <?php 
			                $row_pais = mysql_query("SELECT * FROM paises", $dspp) or die(mysql_error());
			                while($pais = mysql_fetch_assoc($row_pais)){
			                  echo "<option value='".utf8_encode($pais['nombre'])."'>".utf8_encode($pais['nombre'])."</option>";
			                }
			                 ?>
			              </select>
						</td>
						<td>
							<input name="ventas_totales" type="number" step="any" class="form-control" placeholder="TOTAL SALES" required>
						</td>
						<td>
							<input name="tipo_moneda" type="text" class="form-control" value="USD" readonly>
						</td>
					</tr>
		 			<tr>
		 				<td colspan="6"><button class="btn btn-primary" type="submit" style="width:100%" name="agregar_formato" value="1">Save Record</button></td>
		 			</tr>
		 		</tbody>
		 	</table>
		</form>
	<?php
	}
	?>	
	</div>
</div>