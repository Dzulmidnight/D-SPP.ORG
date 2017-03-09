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
	$txt_num_trim = 'trim'.$_GET['trim'];
	$txt_idtrim = 'idtrim'.$_GET['trim'];
	$txt_estatus_trim = 'estado_trim'.$_GET['trim'];
	$idinforme_general = $_POST['idinforme_general'];
	$ano = date('Y', time());
	$idtrim = 'T'.$_GET['trim'].'-'.$ano.'-'.$idempresa;
	$estado_trim = "ACTIVO";

	$insertSQL = sprintf("INSERT INTO $txt_num_trim ($txt_idtrim, idempresa, fecha_inicio, $txt_estatus_trim) VALUES (%s, %s, %s, %s)",
		GetSQLValueString($idtrim, "text"),
		GetSQLValueString($idempresa, "int"),
		GetSQLValueString($fecha_actual, "int"),
		GetSQLValueString($estado_trim, "text"));
	$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

	$updateSQL = sprintf("UPDATE informe_general SET $txt_num_trim = %s WHERE idinforme_general = %s",
		GetSQLValueString($idtrim, "text"),
		GetSQLValueString($idinforme_general, "text"));
	$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

	echo "<script>alert('Se ha creado un nuevo formato trimestral $idtrim');</script>";
}
if(isset($_POST['finalizar_trim']) && $_POST['finalizar_trim'] == 'SI'){
	if(isset($_POST['suma_cuota_uso']) || $_POST['suma_cuota_uso'] != 0 || $_POST['suma_cuota_uso'] != NULL){
		$suma_cuota_uso = $_POST['suma_cuota_uso'];
	}else{
		$suma_cuota_uso = 0;
	}
	if(isset($_POST['suma_valor_contrato']) || $_POST['suma_valor_contrato'] != 0 || $_POST['suma_valor_contrato'] != NULL){
		$suma_valor_contrato = $_POST['suma_valor_contrato'];
	}else{
		$suma_valor_contrato = 0;
	}

	$row_informe = mysql_query("SELECT total_informe, total_cuota_uso, total_valor_contrato FROM informe_general WHERE idinforme_general = '$informe_general[idinforme_general]'", $dspp) or die(mysql_error());
	$informe = mysql_fetch_assoc($row_informe);

	$total_cuota_uso = $informe['total_cuota_uso'] + $suma_cuota_uso;
	$total_valor_contrato = $informe['total_valor_contrato'] + $suma_valor_contrato;
	$txt_idtrim = 'idtrim'.$_GET['trim'];
	$txt_numero_trim = 'trim'.$_GET['trim'];
	$txt_estado_trim = 'estado_'.$txt_numero_trim;
	$txt_total_trim = 'total_'.$txt_numero_trim;
	$txt_valor_contrato = 'valor_contrato_'.$txt_numero_trim;
	$txt_cuota_uso = 'cuota_uso_'.$txt_numero_trim;
	$estatus_trim = 'FINALIZADO';
	

	$updateSQL = sprintf("UPDATE $txt_numero_trim SET $txt_estado_trim = %s, fecha_fin = %s, $txt_total_trim = %s, $txt_valor_contrato = %s, $txt_cuota_uso = %s WHERE $txt_idtrim = %s",
		GetSQLValueString($estatus_trim, "text"),
		GetSQLValueString($_POST['fecha'], "int"),
		GetSQLValueString($suma_cuota_uso, "double"),
		GetSQLValueString($suma_valor_contrato, "double"),
		GetSQLValueString($suma_cuota_uso, "double"),
		GetSQLValueString($_POST['idtrim'], "text"));
	$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

	$updateSQL = sprintf("UPDATE informe_general SET total_valor_contrato = %s, total_cuota_uso = %s WHERE idinforme_general = %s",
		GetSQLValueString($total_valor_contrato, "double"),
		GetSQLValueString($total_cuota_uso, "double"),
		GetSQLValueString($informe_general['idinforme_general'], "text"));
	$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

	
	if($txt_numero_trim == 'trim4'){
		//revisamos si el trim4 ha finalizado, entonces cambiamos el estatus del INFORME GENERAL a FINALIZADO ya que se han concluido los 4 trimestres
		//Tambien se agregar el monto de los 4 informes trimestrales dentro del TOTAL DEL INFORME GENERAL
		$updateSQL = sprintf("UPDATE informe_general SET estado_informe = %s WHERE idinforme_general = %s",
			GetSQLValueString('FINALIZADO', "text"),
			GetSQLValueString($informe_general['idinforme_general'], "text"));
		$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());
	}
}

if(isset($_GET['trim'])){

	$num_trim = "trim".$_GET['trim'];
	$ano_actual = date('Y', time());
	$row_trim = mysql_query("SELECT * FROM $num_trim WHERE idempresa = $idempresa AND FROM_UNIXTIME(fecha_inicio, '%Y') = $ano_actual", $dspp) or die(mysql_error());
	$total_trim = mysql_num_rows($row_trim);
	$trim = mysql_fetch_assoc($row_trim);
	$idtrim = "id".$num_trim;
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
					<input type='hidden' name='idtrim' value='".$trim[$idtrim]."'>
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
		$query_formato = "SELECT formato_producto_empresa.* FROM formato_producto_empresa WHERE formato_producto_empresa.idtrim = '$trim[$idtrim]' AND idempresa = $idempresa";
		$row_formato = mysql_query($query_formato, $dspp) or die(mysql_error());

		if(isset($_GET['add'])){
			include('informe_add.php');
		}else{
		?>
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
						<th class="text-center">Valor de ventas totales SPP</th>
						<th class="text-center">Tipo moneda</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$contador = 1;
					$suma_cuota_uso = '';
					$suma_valor_contrato = 0;
					while($formato = mysql_fetch_assoc($row_formato)){
					?>
						<tr>
							<td><?php echo $contador; ?></td>
							<td><?php echo $formato['pais']; ?></td>
							<td style="background-color:#e74c3c;color:#ecf0f1;"><?php echo $formato['ventas_totales']; ?></td>
							<td><?php echo $formato['tipo_moneda']; ?></td>
						</tr>
					<?php
					$suma_cuota_uso = $formato['ventas_totales'] + $suma_cuota_uso;
					//$suma_valor_contrato = $formato['valor_total_contrato'] + $suma_valor_contrato; 
					$contador++;
					}
						
						//echo "<tr class='info'>
							/*<td colspan='18'></td>
							<td class='text-right'><b style='color:red'>$suma_valor_contrato USD</b></td>
							<td></td>
							<td class='text-right'><b style='color:red'>$suma_cuota_uso USD</b></td>
						</tr>";
						//EL TOTAL A PAGAR AL FINALIZAR EL TRIMESTRE
						echo "<input type='hidden' name='suma_cuota_uso' value='$suma_cuota_uso'>";
						echo "<input type='hidden' name='suma_valor_contrato' value='$suma_valor_contrato'>";*/
					 ?>
				</tbody>
			</table>

		</form>
		<?php
		}
	?>


	<?php
	}else{
		echo "<p class='alert alert-danger'><span class='glyphicon glyphicon-ban-circle' aria-hidden='true'></span> AUN NO SE PUEDE INICIAR ESTE INFORME TRIMESTRAL, <b>DEBE FINALIZAR EL INFORME ANTERIOR</b></p>";
	}

	/////
	$row_trim1 = mysql_query("SELECT idtrim1, estado_trim1 FROM trim1 WHERE idempresa = $idempresa AND FROM_UNIXTIME(fecha_inicio, '%Y') = $ano_actual", $dspp) or die(mysql_error());
	$trim1 = mysql_fetch_assoc($row_trim1);

	if(isset($trim1['idtrim1'])){ //confirmamos que se ha creado el primer trim (TRIM1)
		$num_trim = $_GET['trim'];
		$trim_actual = 'trim'.$num_trim;
		$txt_idtrim = 'idtrim'.$num_trim;

		if($trim1['estado_trim1'] == 'FINALIZADO'){ // SI EL TRIM1 HA FINALIZADO, REVISAREMOS QUE LOS TRIMS SIGUIENTES CONCLUYAN PARA PODER CREAR UNO NUEVO
			if($num_trim != 1){
				//checamos que el trim anterior haya finalizado
				//// iniciamos VARIABLES DEL TRIM ANTERIOR
					$trim_anterior = 'trim'.($num_trim - 1); //restamos 1 al trim actual para poder consultar el anterio
					$idtrim_anterior = 'idtrim'.($num_trim - 1);
					$estado_trim = 'estado_trim'.($num_trim - 1);
				// terminamos VARIABLES DEL TRIM ANTERIOR


				$row_trim_anterior = mysql_query("SELECT * FROM $trim_anterior WHERE idempresa = $idempresa AND FROM_UNIXTIME(fecha_inicio, '%Y') = $ano_actual", $dspp) or die(mysql_error());
				$informacion_trim_anterior = mysql_fetch_assoc($row_trim_anterior);

				if(isset($informacion_trim_anterior[$idtrim_anterior]) && $informacion_trim_anterior[$estado_trim] == 'FINALIZADO'){ /// SI EL TRIM ANTERIOR HA FINALIZADO, MOSTRAREMOS LA OPCIÓN PARA PODER CREAR UN NUEVO TRIM
					$num_trim_actual = 'trim'.$_GET['trim'];
					$row_trim_actual = mysql_query("SELECT * FROM $trim_actual WHERE idempresa = $idempresa AND FROM_UNIXTIME(fecha_inicio, '%Y') = $ano_actual", $dspp) or die(mysql_error());
					$total_trim_actual = mysql_num_rows($row_trim_actual);
					if($total_trim_actual != 1){ // si ya se ha iniciado el nuevo trim, ya no mostraremos la opción
						echo '
							<form action="" method="POST">
								<p class="alert alert-info">
									<strong>¿Desea crear un nuevo Formato para Informe Trimestral?</strong>
									<input class="btn btn-success" type="submit" name="nuevo_trim" value="SI">
									<input type="hidden" name="idinforme_general" value="'.$informe_general['idinforme_general'].'">
								</p>
							</form>
						';
					}
				}
				$row_trim = mysql_query("SELECT * FROM $trim_actual WHERE idempresa AND FROM_UNIXTIME(fecha_inicio, '%Y') = $ano_actual",$dspp) or die(mysql_error());
				$informacion_trim = mysql_fetch_assoc($row_trim);
			}
		}
	}
}
 ?>