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

$idempresa = $_SESSION['idempresa'];
$fecha_actual = time();
$ano_actual = date('Y', time());
//IFC = Informe General Compras

if(isset($_POST['crear_informe'])){
	if($_POST['crear_informe'] == 'YES'){
		//IFP = InForme Producto Empresa = IFPE
		$ano = date('Y', time());
		$idinforme_general_producto = 'IFPE-'.$idempresa.'-'.$ano;
		$estado_informe = "ACTIVO";

		$insertSQL = sprintf("INSERT INTO informe_general_producto(idinforme_general_producto, idempresa, ano, estado_informe) VALUES (%s, %s, %s, %s)",
			GetSQLValueString($idinforme_general_producto, "text"),
			GetSQLValueString($idempresa, "int"),
			GetSQLValueString($fecha_actual, "int"),
			GetSQLValueString($estado_informe, "text"));
		$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

		echo "<script>alert('The quarterly report $idinforme_general_producto has been created Se ha creado el informe $idinforme_general_producto, corresponding to the year $ano');</script>";
	}else{
		echo "<script>alert('No new report created');</script>";
	}
}
if(isset($_POST['informe_trimestral'])){
	if($_POST['informe_trimestral'] == 'YES'){
		// Trimestre Empresa = TE
		$idinforme_general_producto = $_POST['idinforme_general_producto'];
		$ano = date('Y', time());
		$idtrim1_producto = 'TE1-'.$ano.'-'.$idempresa;
		$estado_trim1_producto = "FINALIZADO"; // se finaliza el trimestre 1, ya que el despliegue sera en abril, por lo tanto se empezara desde el segundo trimestre

		$total_trim1 = 0;
		$valor_contrato_trim1 = 0;
		$cuota_uso_trim1 = 0;
		$estatus_factura_trim1 = 'PAGADA';
		$estatus_comprobante_trim1 = 'APROBADO';

		$insertSQL = sprintf("INSERT INTO trim1_producto (idtrim1_producto, idempresa, fecha_inicio, fecha_fin, total_trim1, estado_trim1, valor_contrato_trim1, cuota_uso_trim1, estatus_factura_trim1, estatus_comprobante_trim1) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
			GetSQLValueString($idtrim1_producto, "text"),
			GetSQLValueString($idempresa, "int"),
			GetSQLValueString($fecha_actual, "int"),
			GetSQLValueString($fecha_actual, "int"),
			GetSQLValueString($total_trim1, "int"),
			GetSQLValueString($estado_trim1_producto, "text"),
			GetSQLValueString($valor_contrato_trim1, "int"),
			GetSQLValueString($cuota_uso_trim1, "int"),
			GetSQLValueString($estatus_factura_trim1, "text"),
			GetSQLValueString($estatus_comprobante_trim1, "text"));
		$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

		$updateSQL = sprintf("UPDATE informe_general_producto SET trim1_producto = %s WHERE idinforme_general_producto = %s",
			GetSQLValueString($idtrim1_producto, "text"),
			GetSQLValueString($idinforme_general_producto, "text"));
		$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());
		echo "<script>alert('A new quarterly format has been created - $idtrim1_producto');</script>";
	}else{
		echo "<script>alert('No');</script>";
	}
}

?>
<div class="row">
	<div class="col-md-12">
	<?php
	$row_informe = mysql_query("SELECT informe_general_producto.*, trim1_producto.total_trim1, trim2_producto.total_trim2, trim3_producto.total_trim3, trim4_producto.total_trim4, ROUND(SUM(trim1_producto.total_trim1 + trim2_producto.total_trim2 + trim3_producto.total_trim3 + trim4_producto.total_trim4), 2) AS 'balance_final' FROM informe_general_producto LEFT JOIN trim1_producto ON informe_general_producto.trim1_producto = trim1_producto.idtrim1_producto LEFT JOIN trim2_producto ON informe_general_producto.trim2_producto = trim2_producto.idtrim2_producto LEFT JOIN trim3_producto ON informe_general_producto.trim3_producto = trim3_producto.idtrim3_producto LEFT JOIN trim4_producto ON informe_general_producto.trim4_producto = trim4_producto.idtrim4_producto WHERE informe_general_producto.idempresa = $idempresa AND FROM_UNIXTIME(informe_general_producto.ano, '%Y') = '$ano_actual'", $dspp) or die(mysql_error());
	//$row_informe = mysql_query("SELECT * FROM informe_general_producto WHERE idempresa = $idempresa AND FROM_UNIXTIME(ano, '%Y') = $ano_actual", $dspp) or die(mysql_error());
	$informe_general_producto = mysql_fetch_assoc($row_informe);
	$total_informes = mysql_num_rows($row_informe);

	if($informe_general_producto['idinforme_general_producto']){
		
		$row_trim = mysql_query("SELECT * FROM trim1_producto WHERE idempresa = $idempresa AND FROM_UNIXTIME(fecha_inicio, '%Y') = $ano_actual", $dspp) or die(mysql_error());
		$total_trim1_producto = mysql_num_rows($row_trim);
		$informacion_trim = mysql_fetch_assoc($row_trim);

		if($total_trim1_producto == 1){ // SE YA SE HA INICADO TRIM1, SE MOSTRARAN LAS OPCIONES PARA PODER VISUALIZAR LOS DEMAS TRIM(s)
		?>
			<div class="row">
				<div class="col-md-12">
					<div class="btn-group" role="group" aria-label="...">
						<div class="btn-group">
						  <a type="button" <?php if(isset($_GET['trim1_producto'])){ echo "class='btn btn-sm btn-success'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> href="?INFORME&producto&trim=1" ><span class="glyphicon glyphicon-file" aria-hidden="true"></span> Quarter 1</a>
						  
						  <!--15_03_2017<button type="button" <?php if(isset($_GET['trim1_producto'])){ echo "class='btn btn-sm btn-success'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						    <span class="caret"></span>
						    <span class="sr-only">Toggle Dropdown</span>
						  </button>
						  <ul class="dropdown-menu">
						    <li><a href="?INFORME&producto&trim=1&add_producto&idtrim=<?php echo $informacion_trim['idtrim1_producto']; ?>">Agregar</a></li>
						    <li><a href="?INFORME&producto&trim=1&edit&idtrim=<?php echo $informacion_trim['idtrim1_producto']; ?>">Editar</a></li>
						  </ul>15_03_2017-->
						</div>

						<div class="btn-group">
						  <a type="button" <?php if(isset($_GET['trim2_producto'])){ echo "class='btn btn-sm btn-success'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> href="?INFORME&producto&trim=2" ><span class="glyphicon glyphicon-file" aria-hidden="true"></span> Quarter 2</a>
						  <!--15_03_2017<button type="button" <?php if(isset($_GET['trim2_producto'])){ echo "class='btn btn-sm btn-success'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						    <span class="caret"></span>
						    <span class="sr-only">Toggle Dropdown</span>
						  </button>
						  <ul class="dropdown-menu">
						  	<?php 
						  	$row_trim2_producto = mysql_query("SELECT * FROM trim2_producto WHERE idempresa = $idempresa AND FROM_UNIXTIME(fecha_inicio, '%Y') = $ano_actual");
						  	$informacion_trim2_producto = mysql_fetch_assoc($row_trim2_producto);

						  	if(isset($informacion_trim2_producto['idtrim2_producto'])){
						  		echo '<li><a href="?INFORME&producto&trim=2&add_producto&idtrim='.$informacion_trim2_producto['idtrim2_producto'].'">Agregar</a></li>';
						  		echo '<li><a href="?INFORME&producto&trim=2&edit&idtrim='.$informacion_trim2_producto['idtrim2_producto'].'">Editar</a></li>';
						  	}
						  	 ?>
						  </ul>15_03_2017-->
						</div>

						<div class="btn-group">
						  <a type="button" <?php if(isset($_GET['trim3_producto'])){ echo "class='btn btn-sm btn-success'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> href="?INFORME&producto&trim=3" ><span class="glyphicon glyphicon-file" aria-hidden="true"></span> Quarter 3</a>
						  <!--15_03_2017<button type="button" <?php if(isset($_GET['trim3_producto'])){ echo "class='btn btn-sm btn-success'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						    <span class="caret"></span>
						    <span class="sr-only">Toggle Dropdown</span>
						  </button>
						  <ul class="dropdown-menu">
						  	<?php 
						  	$row_trim3_producto = mysql_query("SELECT * FROM trim3_producto WHERE idempresa = $idempresa AND FROM_UNIXTIME(fecha_inicio, '%Y') = $ano_actual");
						  	$informacion_trim3_producto = mysql_fetch_assoc($row_trim3_producto);

						  	if(isset($informacion_trim3_producto['idtrim3_producto'])){
						  		echo '<li><a href="?INFORME&producto&trim=3&add_producto&idtrim='.$informacion_trim3_producto['idtrim3_producto'].'">Agregar</a></li>';
						  		echo '<li><a href="?INFORME&producto&trim=3&edit&idtrim='.$informacion_trim3_producto['idtrim3_producto'].'">Editar</a></li>';
						  	}
						  	 ?>
						  </ul>15_03_2017-->
						</div>

						<div class="btn-group">
						  <a type="button" <?php if(isset($_GET['trim4_producto'])){ echo "class='btn btn-sm btn-success'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> href="?INFORME&producto&trim=4" ><span class="glyphicon glyphicon-file" aria-hidden="true"></span> Quarter 4</a>
						  <!--15_03_2017<button type="button" <?php if(isset($_GET['trim4_producto'])){ echo "class='btn btn-sm btn-success'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						    <span class="caret"></span>
						    <span class="sr-only">Toggle Dropdown</span>
						  </button>
						  <ul class="dropdown-menu">
						  	<?php 
						  	$row_trim4_producto = mysql_query("SELECT * FROM trim4_producto WHERE idempresa = $idempresa AND FROM_UNIXTIME(fecha_inicio, '%Y') = $ano_actual");
						  	$informacion_trim4_producto = mysql_fetch_assoc($row_trim4_producto);

						  	if(isset($informacion_trim4_producto['idtrim4_producto'])){
						  		echo '<li><a href="?INFORME&producto&trim=4&add_producto&idtrim='.$informacion_trim4_producto['idtrim4_producto'].'">Agregar</a></li>';
						  		echo '<li><a href="?INFORME&producto&trim=4&edit&idtrim='.$informacion_trim4_producto['idtrim4_producto'].'">Editar</a></li>';
						  	}
						  	 ?>
						  </ul>15_03_2017-->
						</div>

					</div>
				</div>
	
				<div class="col-md-12">
					<?php 
					if(!isset($_GET['trim'])){
						include('informe_detail.php');
					}else{
						include('trim_producto.php');
					}
					?>
				</div>
			</div>
		<?php
		}else{ // SI NO SE HA INICIADO TRIM 1, SE DEBE MOSTRAR LA OPCIÓN PARA QUE EL USUARIO PUEDA CREAR TRIM1, ESTO USUALMENTE DESPUES DE CREAR EL INFORME_GENERAL
		?>
			<form action="" method="POST">
				<p class="alert alert-info">
				Step 2: No <b style="color:red">"Quarterly Format"</b> has been started in the <b style="color:red">"General Product Report - <?php echo $ano_actual ?>"</b>, <strong>Do you want to create a new Format for Quarterly Report?</strong>
				<input class="btn btn-success" type="submit" name="informe_trimestral" value="YES">
				<input class="btn btn-danger" type="submit" name="informe_trimestral" value="NO">
				<input type="hidden" name="idinforme_general_producto" value="<?php echo $informe_general_producto['idinforme_general_producto']; ?>">
				</p>
			</form>
		<?php
		}
	?>

	<?php
	}else{
	?>		
		<form action="" method="POST">
			<p class="alert alert-warning">
			Step 1: No finished Product Reports were found, <strong>Do you want to create a new Product Report?</strong>
			<input class="btn btn-success" type="submit" name="crear_informe" value="YES">
			<input class="btn btn-danger" type="submit" name="crear_informe" value="NO">
			</p>
		</form>
	<?php
	}
	 ?>
	</div>

</div>