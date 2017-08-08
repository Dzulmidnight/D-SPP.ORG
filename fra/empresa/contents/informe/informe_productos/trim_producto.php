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
if(isset($_POST['nuevo_trim']) && $_POST['nuevo_trim'] == 'SI'){
	$txt_num_trim = 'trim'.$_GET['trim'].'_producto';
	$txt_idtrim_producto = 'idtrim'.$_GET['trim'].'_producto';
	$txt_estatus_trim = 'estado_trim'.$_GET['trim'];
	$idinforme_general_producto = $_POST['idinforme_general_producto'];
	$ano = date('Y', time());
	$idtrim_producto = 'TE'.$_GET['trim'].'-'.$ano.'-'.$idempresa;
	$estado_trim = "ACTIVO";

	$insertSQL = sprintf("INSERT INTO $txt_num_trim ($txt_idtrim_producto, idempresa, fecha_inicio, $txt_estatus_trim) VALUES (%s, %s, %s, %s)",
		GetSQLValueString($idtrim_producto, "text"),
		GetSQLValueString($idempresa, "int"),
		GetSQLValueString($fecha_actual, "int"),
		GetSQLValueString($estado_trim, "text"));
	$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

	$updateSQL = sprintf("UPDATE informe_general_producto SET $txt_num_trim = %s WHERE idinforme_general_producto = %s",
		GetSQLValueString($idtrim_producto, "text"),
		GetSQLValueString($idinforme_general_producto, "text"));
	$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

	echo "<script>alert('Se ha creado un nuevo formato trimestral $idtrim_producto');</script>";
}
if(isset($_POST['finalizar_trim']) && $_POST['finalizar_trim'] == 'SI'){
	if(isset($_POST['suma_total']) || $_POST['suma_total'] != 0 || $_POST['suma_total'] != NULL){
		$suma_total = $_POST['suma_total'];
	}else{
		$suma_total = 0;
	}


	$row_informe = mysql_query("SELECT total_informe FROM informe_general_producto WHERE idinforme_general_producto = '$informe_general_producto[idinforme_general_producto]'", $dspp) or die(mysql_error());
	$informe = mysql_fetch_assoc($row_informe);

	$total_informe = $informe['total_informe'] + $suma_total;
	$txt_idtrim_producto = 'idtrim'.$_GET['trim'].'_producto';
	$txt_numero_trim = 'trim'.$_GET['trim'].'_producto';
	$num_trim = 'trim'.$_GET['trim'];
	$txt_estado_trim = 'estado_'.$num_trim;
	$txt_total_trim = 'total_'.$num_trim;
	$estatus_trim = 'FINALIZADO';
	

	$updateSQL = sprintf("UPDATE $txt_numero_trim SET $txt_estado_trim = %s, fecha_fin = %s, $txt_total_trim = %s WHERE $txt_idtrim_producto = %s",
		GetSQLValueString($estatus_trim, "text"),
		GetSQLValueString($_POST['fecha'], "int"),
		GetSQLValueString($suma_total, "double"),
		GetSQLValueString($_POST['idtrim'], "text"));
	$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

	$updateSQL = sprintf("UPDATE informe_general_producto SET total_informe = %s WHERE idinforme_general_producto = %s",
		GetSQLValueString($total_informe, "double"),
		GetSQLValueString($informe_general_producto['idinforme_general_producto'], "text"));
	$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

	
	if($txt_numero_trim == 'trim4'){
		//revisamos si el trim4 ha finalizado, entonces cambiamos el estatus del INFORME GENERAL a FINALIZADO ya que se han concluido los 4 trimestres
		//Tambien se agregar el monto de los 4 informes trimestrales dentro del TOTAL DEL INFORME GENERAL
		$updateSQL = sprintf("UPDATE informe_general_producto SET estado_informe = %s WHERE idinforme_general_producto = %s",
			GetSQLValueString('FINALIZADO', "text"),
			GetSQLValueString($informe_general_producto['idinforme_general_producto'], "text"));
		$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());
	}
}

if(isset($_GET['trim'])){

	$num_trim = "trim".$_GET['trim'].'_producto';
	$ano_actual = date('Y', time());
	$row_trim = mysql_query("SELECT * FROM $num_trim WHERE idempresa = $idempresa AND FROM_UNIXTIME(fecha_inicio, '%Y') = $ano_actual", $dspp) or die(mysql_error());
	$total_trim = mysql_num_rows($row_trim);
	$trim = mysql_fetch_assoc($row_trim);
	$idtrim_producto = "id".$num_trim;
	$estado_trim = "estado_trim".$_GET['trim'];
	$estatus = $trim[$estado_trim];
	if(isset($estatus)){
		if($estatus == 'ACTIVO'){
			$estatus = $trim[$estado_trim];
			$pregunta = "
				<p class='alert alert-info' style='padding:5px;margin-bottom:0px;'>
					<b style='color:red'>¿Desea concluir la captura de registros en el formato de trimestre actual? </b>
					<button class='' type='subtmit' value='SI'  name='finalizar_trim' data-toggle='tooltip' data-placement='top' title='Finalizar trimestre actual' onclick='return confirm(\"¿Desea finalizar la captura del trimestre actual?\");' >SI</button>
					<!--<input class='btn btn-success' type='submit' name='finalizar_trim' value='SI'>-->
					<input type='hidden' name='idtrim' value='".$trim[$idtrim_producto]."'>
					<input type='hidden' name='fecha' value='".time()."'>
					
				</p>
			";
		}else{
			$estatus = $trim[$estado_trim];
		}
	}else{
		$estatus = 'No Disponible';
	}
	$titulo_trim = '';


	switch ($_GET['trim']) {
		case '1':
			$titulo_trim = "<h4>PRIMER TRIMESTRE | <small>Estatus:  $estatus</small></h4>";
			break;
		case '2':
			$titulo_trim = "<h4>SEGUNDO TRIMESTRE | <small>Estatus: $estatus</small></h4>";
			break;
		case '3':
			$titulo_trim = "<h4>TERCER TRIMESTRE | <small>Estatus: $estatus</small></h4>";
			break;
		case '4':
			$titulo_trim = "<h4>CUARTO TRIMESTRE | <small>Estatus: $estatus</small></h4>";
			break;		
		default:
			$titulo_trim = "<h4>TRIMESTRE NO DISPONIBLE</small></h4>";
			break;
	}

	//echo $titulo_trim;
	if($total_trim == 1){
		$query_formato = "SELECT formato_producto_empresa.* FROM formato_producto_empresa WHERE formato_producto_empresa.idtrim = '$trim[$idtrim_producto]' AND idempresa = $idempresa";
		$row_formato = mysql_query($query_formato, $dspp) or die(mysql_error());

		$ano_actual = date('Y', time());
		$idtrim_txt = 'idtrim'.$_GET['trim'].'_producto';
		$txt_trim = 'trim'.$_GET['trim'].'_producto';
		$row_trim_menu = mysql_query("SELECT * FROM $txt_trim WHERE idempresa = $idempresa AND FROM_UNIXTIME(fecha_inicio, '%Y') = $ano_actual");
	  	$trim_options = mysql_fetch_assoc($row_trim_menu);
		if(isset($_GET['add'])){
			include('informe_add.php');
		}else{
		?>

		<div style="margin-top:10px;">
			<a class="btn btn-default" href="?INFORME&producto&trim=<?php echo $_GET['trim']; ?>&add_producto&idtrim=<?php echo $trim_options[$idtrim_txt]; ?>"><span class="glyphicon glyphicon-plus"></span> Agregar nuevos registros</a>
			<a class="btn btn-default" href="?INFORME&producto&trim=<?php echo $_GET['trim']; ?>&add_producto&idtrim=<?php echo $trim_options[$idtrim_txt]; ?>"><span class="glyphicon glyphicon-pencil"></span> Editar registro actuales</a>
		</div>

		<form action="" method="POST">
			<table class="table table-bordered" style="font-size:11px;">
				<thead>
					<tr>
						<th colspan="3">
						<?php 
						echo $titulo_trim;
						 ?>
						</th>
						<th colspan="4">
							<?php 
							if(isset($pregunta)){
								echo $pregunta;
							}
							?>			
						</th>
					</tr>
					<tr class="success">
						<th class="text-center">#</th>
						<th class="text-center">País destino del producto terminado</th>
						<th class="text-center">Tipo moneda</th>
						<th class="text-center">Valor de ventas totales SPP</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$contador = 1;
					$suma_total = 0;
					$suma_valor_contrato = 0;
					while($formato = mysql_fetch_assoc($row_formato)){
					?>
						<tr>
							<td><?php echo $contador; ?></td>
							<td><?php echo $formato['pais']; ?></td>
							<td><?php echo $formato['tipo_moneda']; ?></td>
							<td style="background-color:#e74c3c;color:#ecf0f1;"><?php echo number_format($formato['ventas_totales'],2); ?></td>
						</tr>
					<?php
					$suma_total = $formato['ventas_totales'] + $suma_total;
					//$suma_valor_contrato = $formato['valor_total_contrato'] + $suma_valor_contrato; 
					$contador++;
					}
						
						echo "<tr class='info'>
							<td></td>
							<td></td>
							<td class='text-right'><b style='color:red'>USD</b></td>
							<td class='text-left'><b style='color:red'>".number_format($suma_total,2)."</b></td>
						</tr>";
						//EL TOTAL A PAGAR AL FINALIZAR EL TRIMESTRE
						echo "<input type='hidden' name='suma_total' value='$suma_total'>";
						echo "<input type='hidden' name='suma_valor_contrato' value='$suma_valor_contrato'>";
					 ?>
				</tbody>
			</table>

		</form>
		<?php
		}
	?>


	<?php
	}

	/////
	$row_trim1_producto = mysql_query("SELECT idtrim1_producto, estado_trim1 FROM trim1_producto WHERE idempresa = $idempresa AND FROM_UNIXTIME(fecha_inicio, '%Y') = $ano_actual", $dspp) or die(mysql_error());
	$trim1_producto = mysql_fetch_assoc($row_trim1_producto);

	if(isset($trim1_producto['idtrim1_producto'])){ //confirmamos que se ha creado el primer trim (TRIM1)
		$num_trim = $_GET['trim'];
		$trim_actual = 'trim'.$num_trim.'_producto';
		$txt_idtrim_producto = 'idtrim'.$num_trim.'_producto';

		if($trim1_producto['estado_trim1'] == 'FINALIZADO'){ // SI EL TRIM1 HA FINALIZADO, REVISAREMOS QUE LOS TRIMS SIGUIENTES CONCLUYAN PARA PODER CREAR UNO NUEVO
			if($num_trim != 1){
				//checamos que el trim anterior haya finalizado
				//// iniciamos VARIABLES DEL TRIM ANTERIOR
					$trim_anterior = 'trim'.($num_trim - 1).'_producto'; //restamos 1 al trim actual para poder consultar el anterio
					$idtrim_producto_anterior = 'idtrim'.($num_trim - 1).'_producto';
					$estado_trim = 'estado_trim'.($num_trim - 1);
				// terminamos VARIABLES DEL TRIM ANTERIOR


				$row_trim_anterior = mysql_query("SELECT * FROM $trim_anterior WHERE idempresa = $idempresa AND FROM_UNIXTIME(fecha_inicio, '%Y') = $ano_actual", $dspp) or die(mysql_error());
				$informacion_trim_anterior = mysql_fetch_assoc($row_trim_anterior);

				if(isset($informacion_trim_anterior[$idtrim_producto_anterior]) && $informacion_trim_anterior[$estado_trim] == 'FINALIZADO'){ /// SI EL TRIM ANTERIOR HA FINALIZADO, MOSTRAREMOS LA OPCIÓN PARA PODER CREAR UN NUEVO TRIM
					$num_trim_actual = 'trim'.$_GET['trim'].'_producto';
					$row_trim_actual = mysql_query("SELECT * FROM $trim_actual WHERE idempresa = $idempresa AND FROM_UNIXTIME(fecha_inicio, '%Y') = $ano_actual", $dspp) or die(mysql_error());
					$total_trim_actual = mysql_num_rows($row_trim_actual);
					if($total_trim_actual != 1){ // si ya se ha iniciado el nuevo trim, ya no mostraremos la opción
						echo '
							<form action="" method="POST">
								<p class="alert alert-info">
									<strong>¿Desea crear un nuevo Formato para Informe Trimestral?</strong>
									<input class="btn btn-success" type="submit" name="nuevo_trim" value="SI">
									<input type="hidden" name="idinforme_general_producto" value="'.$informe_general_producto['idinforme_general_producto'].'">
								</p>
							</form>
						';
					}
				}else{
					echo "<p class='alert alert-danger'><span class='glyphicon glyphicon-ban-circle' aria-hidden='true'></span> AUN NO SE PUEDE INICIAR ESTE INFORME TRIMESTRAL, <b>DEBE FINALIZAR EL INFORME ANTERIOR</b></p>";
				}
				$row_trim = mysql_query("SELECT * FROM $trim_actual WHERE idempresa AND FROM_UNIXTIME(fecha_inicio, '%Y') = $ano_actual",$dspp) or die(mysql_error());
				$informacion_trim = mysql_fetch_assoc($row_trim);
			}
		}
	}
}
 ?>