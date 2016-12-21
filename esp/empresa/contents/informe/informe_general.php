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
	if($_POST['crear_informe'] == 'SI'){

		$ano = date('Y', time());
		$idinforme_general = 'IFC-'.$idempresa.'-'.$ano;
		$estado_informe = "ACTIVO";

		$insertSQL = sprintf("INSERT INTO informe_general(idinforme_general, idempresa, ano, estado_informe) VALUES (%s, %s, %s, %s)",
			GetSQLValueString($idinforme_general, "text"),
			GetSQLValueString($idempresa, "int"),
			GetSQLValueString($fecha_actual, "int"),
			GetSQLValueString($estado_informe, "text"));
		$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

		echo "<script>alert('Se ha creado el informe $idinforme_general, correspondiente al año $ano');</script>";
	}else{
		echo "<script>alert('no se ha creado un nuevo informe');</script>";
	}
}
if(isset($_POST['informe_trimestral'])){
	if($_POST['informe_trimestral'] == 'SI'){
		$ano = date('Y', time());
		$idtrim1 = 'T1-'.$ano.'-'.$idempresa;
		$estado_trim1 = "ACTIVO";

		$insertSQL = sprintf("INSERT INTO trim1 (idtrim1, idempresa, fecha_inicio, estado_trim1) VALUES (%s, %s, %s, %s)",
			GetSQLValueString($idtrim1, "text"),
			GetSQLValueString($idempresa, "int"),
			GetSQLValueString($fecha_actual, "int"),
			GetSQLValueString($estado_trim1, "text"));
		$insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
		echo "<script>alert('Se ha creado un nuevo formato trimestral $idtrim1');</script>";
	}else{
		echo "<script>alert('No');</script>";
	}
}

?>
<div class="row">
	<div class="col-md-12">
	<?php 
	$row_informe = mysql_query("SELECT * FROM informe_general WHERE idempresa = $idempresa", $dspp) or die(mysql_error());
	$total_informes = mysql_num_rows($row_informe);

	if($total_informes != 0){
		$row_trim = mysql_query("SELECT * FROM trim1 WHERE idempresa = $idempresa", $dspp) or die(mysql_error());
		$total_trim1 = mysql_num_rows($row_trim);

		if($total_trim1 == 1){ // SE YA SE HA INICADO TRIM1, SE MOSTRARAN LAS OPCIONES PARA PODER VISUALIZAR LOS DEMAS TRIM(s)
		?>
			<div class="row">
				<?php 
				 ?>
				<div class="col-md-12">
					<div class="btn-group" role="group" aria-label="...">
						<div class="btn-group">
						  <a type="button" <?php if(isset($_GET['trim1'])){ echo "class='btn btn-sm btn-success'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> href="?INFORME&general_detail&trim=1" ><span class="glyphicon glyphicon-file" aria-hidden="true"></span> Trimestre 1</a>
						  <button type="button" <?php if(isset($_GET['trim1'])){ echo "class='btn btn-sm btn-success'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						    <span class="caret"></span>
						    <span class="sr-only">Toggle Dropdown</span>
						  </button>
						  <ul class="dropdown-menu">
						    <li><a href="?INFORME&general_detail&trim=1&add">Agregar</a></li>
						    <li><a href="?INFORME&general_detail&trim=1&edit">Editar</a></li>
						  </ul>
						</div>

						<div class="btn-group">
						  <a type="button" <?php if(isset($_GET['trim2'])){ echo "class='btn btn-sm btn-success'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> href="?INFORME&general_detail&trim=2" ><span class="glyphicon glyphicon-file" aria-hidden="true"></span> Trimestre 2</a>
						  <button type="button" <?php if(isset($_GET['trim2'])){ echo "class='btn btn-sm btn-success'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						    <span class="caret"></span>
						    <span class="sr-only">Toggle Dropdown</span>
						  </button>
						  <ul class="dropdown-menu">
						    <li><a href="?INFORME&general_detail&trim=2&add">Agregar</a></li>
						    <li><a href="?INFORME&general_detail&trim=2&edit">Editar</a></li>
						  </ul>
						</div>

						<div class="btn-group">
						  <a type="button" <?php if(isset($_GET['trim3'])){ echo "class='btn btn-sm btn-success'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> href="?INFORME&general_detail&trim=3" ><span class="glyphicon glyphicon-file" aria-hidden="true"></span> Trimestre 3</a>
						  <button type="button" <?php if(isset($_GET['trim3'])){ echo "class='btn btn-sm btn-success'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						    <span class="caret"></span>
						    <span class="sr-only">Toggle Dropdown</span>
						  </button>
						  <ul class="dropdown-menu">
						    <li><a href="?INFORME&general_detail&trim=3&add">Agregar</a></li>
						    <li><a href="?INFORME&general_detail&trim=3&edit">Editar</a></li>
						  </ul>
						</div>

						<div class="btn-group">
						  <a type="button" <?php if(isset($_GET['trim4'])){ echo "class='btn btn-sm btn-success'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> href="?INFORME&general_detail&trim=4" ><span class="glyphicon glyphicon-file" aria-hidden="true"></span> Trimestre 4</a>
						  <button type="button" <?php if(isset($_GET['trim4'])){ echo "class='btn btn-sm btn-success'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						    <span class="caret"></span>
						    <span class="sr-only">Toggle Dropdown</span>
						  </button>
						  <ul class="dropdown-menu">
						    <li><a href="?INFORME&general_detail&trim=4&add">Agregar</a></li>
						    <li><a href="?INFORME&general_detail&trim=4&edit">Editar</a></li>
						  </ul>
						</div>

					</div>
				</div>			
				<div class="col-md-12">
					<?php 
					include('trim.php');
					 ?>
				</div>
			</div>
		<?php
		}else{ // SI NO SE HA INICIADO TRIM 1, SE DEBE MOSTRAR LA OPCIÓN PARA QUE EL USUARIO PUEDA CREAR TRIM1, ESTO USUALMENTE DESPUES DE CREAR EL INFORME_GENERAL
		?>
			<form action="" method="POST">
				<p class="alert alert-info">
				No se ha iniciado ningun <b style="color:red">"Formato Trimestral"</b> en el Informe general <b style="color:red"><?php echo $ano_actual ?></b> , <strong>¿Desea crear un nuevo Formato para Informe Trimestral?</strong>
				<input class="btn btn-success" type="submit" name="informe_trimestral" value="SI">
				<input class="btn btn-danger" type="submit" name="informe_trimestral" value="NO">
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
			Paso 1: No se encontraron informes, <strong>¿Desea crear un nuevo Informe General?</strong>
			<input class="btn btn-success" type="submit" name="crear_informe" value="SI">
			<input class="btn btn-danger" type="submit" name="crear_informe" value="NO">
			</p>
		</form>
	<?php
	}
	 ?>
	</div>
</div>