<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php');

mysql_select_db($database_dspp, $dspp);

if (!isset($_SESSION)) {
  session_start();
	
	$redireccion = "../index.php?OPP";

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

$idopp = $_SESSION['idopp'];
$fecha_actual = time();
$ano_actual = date('Y', time());
//IFC = Informe General Compras

if(isset($_POST['crear_informe'])){
	if($_POST['crear_informe'] == 'YES'){

		$ano = date('Y', time());
		// Informe General Ventas = IGV
		$idinforme_general = 'IGV-'.$idopp.'-'.$ano;
		$estado_informe = "ACTIVO";

		$insertSQL = sprintf("INSERT INTO informe_general(idinforme_general, idopp, ano, estado_informe) VALUES (%s, %s, %s, %s)",
			GetSQLValueString($idinforme_general, "text"),
			GetSQLValueString($idopp, "int"),
			GetSQLValueString($fecha_actual, "int"),
			GetSQLValueString($estado_informe, "text"));
		$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

		echo "<script>alert('The report ".$idinforme_general." has been created, corresponding to the year ".$ano."');</script>";
	}else{
		echo "<script>alert('No new report created');</script>";
	}
}
if(isset($_POST['informe_trimestral'])){
	if($_POST['informe_trimestral'] == 'YES'){
		$idinforme_general = $_POST['idinforme_general'];
		$ano = date('Y', time());
		/// Trimestre Opp = TO
		$idtrim1 = 'TO1-'.$ano.'-'.$idopp;
		$estado_trim1 = "ACTIVO";

		$insertSQL = sprintf("INSERT INTO trim1 (idtrim1, idopp, fecha_inicio, estado_trim1) VALUES (%s, %s, %s, %s)",
			GetSQLValueString($idtrim1, "text"),
			GetSQLValueString($idopp, "int"),
			GetSQLValueString($fecha_actual, "int"),
			GetSQLValueString($estado_trim1, "text"));
		$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

		$updateSQL = sprintf("UPDATE informe_general SET trim1 = %s WHERE idinforme_general = %s",
			GetSQLValueString($idtrim1, "text"),
			GetSQLValueString($idinforme_general, "text"));
		$actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

		echo "<script>alert('A new quarterly format has been created - $idtrim1');</script>";
	}else{
		echo "<script>alert('No');</script>";
	}
}

?>
<div class="row">
	<div class="col-md-12">
	<?php
	$row_informe = mysql_query("SELECT informe_general.*, trim1.total_trim1, trim2.total_trim2, trim3.total_trim3, trim4.total_trim4, ROUND(SUM(trim1.total_trim1 + trim2.total_trim2 + trim3.total_trim3 + trim4.total_trim4), 2) AS 'balance_final' FROM informe_general LEFT JOIN trim1 ON informe_general.trim1 = trim1.idtrim1 LEFT JOIN trim2 ON informe_general.trim2 = trim2.idtrim2 LEFT JOIN trim3 ON informe_general.trim3 = trim3.idtrim3 LEFT JOIN trim4 ON informe_general.trim4 = trim4.idtrim4 WHERE informe_general.idopp = $idopp AND FROM_UNIXTIME(informe_general.ano, '%Y') = '$ano_actual'", $dspp) or die(mysql_error());
	//$row_informe = mysql_query("SELECT * FROM informe_general WHERE idopp = $idopp AND FROM_UNIXTIME(ano, '%Y') = $ano_actual", $dspp) or die(mysql_error());
	$informe_general = mysql_fetch_assoc($row_informe);
	$total_informes = mysql_num_rows($row_informe);

	if($informe_general['idinforme_general']){
		
		$row_trim = mysql_query("SELECT * FROM trim1 WHERE idopp = $idopp AND FROM_UNIXTIME(fecha_inicio, '%Y') = $ano_actual", $dspp) or die(mysql_error());
		$total_trim1 = mysql_num_rows($row_trim);
		$informacion_trim = mysql_fetch_assoc($row_trim);

		if($total_trim1 == 1){ // SE YA SE HA INICADO TRIM1, SE MOSTRARAN LAS OPCIONES PARA PODER VISUALIZAR LOS DEMAS TRIM(s)
		?>
			<div class="row">
				<div class="col-md-12">
					<div class="btn-group" role="group" aria-label="...">
						<div class="btn-group">
						  <a type="button" <?php if(isset($_GET['trim']) && $_GET['trim'] == 1){ echo "class='btn btn-sm btn-success'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> href="?INFORME&general_detail&trim=1" ><span class="glyphicon glyphicon-file" aria-hidden="true"></span> Quarter 1</a>
						  <!--<button type="button" <?php if(isset($_GET['trim1'])){ echo "class='btn btn-sm btn-success'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						    <span class="caret"></span>
						    <span class="sr-only">Toggle Dropdown</span>
						  </button>
						  <ul class="dropdown-menu">
						    <li><a href="?INFORME&general_detail&trim=1&add&idtrim=<?php echo $informacion_trim['idtrim1']; ?>">Agregar</a></li>
						    <li><a href="?INFORME&general_detail&trim=1&edit&idtrim=<?php echo $informacion_trim['idtrim1']; ?>">Editar</a></li>
						  </ul>-->
						</div>

						<div class="btn-group">
						  <a type="button" <?php if(isset($_GET['trim']) && $_GET['trim'] == 2){ echo "class='btn btn-sm btn-success'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> href="?INFORME&general_detail&trim=2" ><span class="glyphicon glyphicon-file" aria-hidden="true"></span> Quarter 2</a>
						  <!--13_03_2017<button type="button" <?php if(isset($_GET['trim2'])){ echo "class='btn btn-sm btn-success'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						    <span class="caret"></span>
						    <span class="sr-only">Toggle Dropdown</span>
						  </button>
						  <ul class="dropdown-menu">
						  	<?php 
						  	$row_trim2 = mysql_query("SELECT * FROM trim2 WHERE idopp = $idopp AND FROM_UNIXTIME(fecha_inicio, '%Y') = $ano_actual");
						  	$informacion_trim2 = mysql_fetch_assoc($row_trim2);

						  	if(isset($informacion_trim2['idtrim2'])){
						  	/*	echo '<li><a href="?INFORME&general_detail&trim=2&add&idtrim='.$informacion_trim2['idtrim2'].'">Agregar</a></li>';
						  		echo '<li><a href="?INFORME&general_detail&trim=2&edit&idtrim='.$informacion_trim2['idtrim2'].'">Editar</a></li>';
						  	*/}
						  	 ?>
						  </ul>13_03_2017-->
						</div>

						<div class="btn-group">
						  <a type="button" <?php if(isset($_GET['trim']) && $_GET['trim'] == 3){ echo "class='btn btn-sm btn-success'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> href="?INFORME&general_detail&trim=3" ><span class="glyphicon glyphicon-file" aria-hidden="true"></span> Quarter 3</a>
						  <!--13:_03_2017<button type="button" <?php if(isset($_GET['trim3'])){ echo "class='btn btn-sm btn-success'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						    <span class="caret"></span>
						    <span class="sr-only">Toggle Dropdown</span>
						  </button>
						  <ul class="dropdown-menu">
						  	<?php 
						  	$row_trim3 = mysql_query("SELECT * FROM trim3 WHERE idopp = $idopp AND FROM_UNIXTIME(fecha_inicio, '%Y') = $ano_actual");
						  	$informacion_trim3 = mysql_fetch_assoc($row_trim3);

						  	if(isset($informacion_trim3['idtrim3'])){ /*
						  		echo '<li><a href="?INFORME&general_detail&trim=3&add&idtrim='.$informacion_trim3['idtrim3'].'">Agregar</a></li>';
						  		echo '<li><a href="?INFORME&general_detail&trim=3&edit&idtrim='.$informacion_trim3['idtrim3'].'">Editar</a></li>';
						  	*/}
						  	 ?>
						  </ul>13_03_2017-->
						</div>


						<div class="btn-group">
						  <a type="button" <?php if(isset($_GET['trim']) && $_GET['trim'] == 4){ echo "class='btn btn-sm btn-success'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> href="?INFORME&general_detail&trim=4" ><span class="glyphicon glyphicon-file" aria-hidden="true"></span> Quarter 4</a>
						  <!--13_03_2017<button type="button" <?php if(isset($_GET['trim4'])){ echo "class='btn btn-sm btn-success'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						    <span class="caret"></span>
						    <span class="sr-only">Toggle Dropdown</span>
						  </button>
						  <ul class="dropdown-menu">
						  	<?php 
						  	$row_trim4 = mysql_query("SELECT * FROM trim4 WHERE idopp = $idopp AND FROM_UNIXTIME(fecha_inicio, '%Y') = $ano_actual");
						  	$informacion_trim4 = mysql_fetch_assoc($row_trim4);

						  	if(isset($informacion_trim4['idtrim4'])){/*
						  		echo '<li><a href="?INFORME&general_detail&trim=4&add&idtrim='.$informacion_trim4['idtrim4'].'">Agregar</a></li>';
						  		echo '<li><a href="?INFORME&general_detail&trim=4&edit&idtrim='.$informacion_trim4['idtrim4'].'">Editar</a></li>';
						  	*/}
						  	 ?>
						  </ul>13_03_2017-->
						</div>

					</div>
				</div>
	
				<div class="col-md-12">
					<?php 
					if(!isset($_GET['trim'])){
						include('informe_detail.php');
					}else{
						include('trim.php');
					}
					?>
				</div>
			</div>
		<?php
		}else{ // SI NO SE HA INICIADO TRIM 1, SE DEBE MOSTRAR LA OPCIÓN PARA QUE EL USUARIO PUEDA CREAR TRIM1, ESTO USUALMENTE DESPUES DE CREAR EL INFORME_GENERAL
		?>
			<form action="" method="POST">
				<p class="alert alert-info">
				Step 2: No  <b style="color:red">"Quarterly Format"</b> has been started in the <b style="color:red">"General Sales Report"</b>, <strong>Do you want to create a new Format for Quarterly Report?</strong>
				<input class="btn btn-success" type="submit" name="informe_trimestral" value="YES">
				<input class="btn btn-danger" type="submit" name="informe_trimestral" value="NO">
				<input type="hidden" name="idinforme_general" value="<?php echo $informe_general['idinforme_general']; ?>">
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
			Step 1: No General Sales Reports were found, <strong> Do you want to create a new General Sales Report?</strong>
			<input class="btn btn-success" type="submit" name="crear_informe" value="YES">
			<input class="btn btn-danger" type="submit" name="crear_informe" value="NO">
			</p>
		</form>
	<?php
	}
	 ?>
	</div>

</div>