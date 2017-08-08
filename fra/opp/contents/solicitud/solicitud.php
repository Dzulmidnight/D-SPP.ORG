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



$tiempo_actual = time();
$idopp = $_SESSION['idopp'];
$ano_actual = date('Y', $tiempo_actual);
$min = 60;
$hr = 60;
$dia = 24;
//08_05_2017 echo $ano_actual;
//60 seg = 1min
//60 min = 1hr
//24 hr = 1dia

//consultamos las solicitudes que tiene en el año
$row_solicitudes = mysql_query("SELECT idsolicitud_certificacion, fecha_registro FROM solicitud_certificacion WHERE idopp = $idopp AND FROM_UNIXTIME(fecha_registro,'%Y') = $ano_actual", $dspp) or die(mysql_error());
$solicitud = mysql_fetch_assoc($row_solicitudes);
$total = mysql_num_rows($row_solicitudes);

$diferencia = $tiempo_actual - $solicitud['fecha_registro'];

$minutos = round($diferencia / $min);
$horas = round($minutos / $hr);
$dias = round($horas / $dia);

/*08_05_2017
echo "<br>EL TOTAL ES: ".$total;
echo "<br>LA DIFERENCIA DE TIEMPO ES: ".$diferencia;
echo "<br>MINUTOS: ".$minutos;
echo "<br>HORAS: ".$horas;
echo "<br>DIAS: ".$dias;

if($dias <= 100){
	echo "<br>Los dias son menos de 100";
}else{
	echo "<br>Mayor a 100 dias";
} 08_05_2017*/

?>

<ul class="nav nav-pills">
	<li role="presentation" <?php if(isset($_GET['select'])){ echo "class='active'"; } ?>>
		<a href="?SOLICITUD&select">
			<span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Liste des demandes
		</a>
	</li>
	<li role="presentation" <?php if(isset($_GET['add'])){ echo "class='active'"; } ?>>
		<a href="?SOLICITUD&add">
			<span class="glyphicon glyphicon-open-file" aria-hidden="true"></span> Nouvelle demande
		</a>
	</li>
	<?php 
	if(isset($_GET['detail'])){
	?>
		<li role="presentation" class="active">
			<a href="#">Détails</a>
		</li>
	<?php
	}
	?>
</ul>

<?php
 if(isset($_GET['mensaje'])){
?>
	<p>
		<div class="alert alert-success alert-dismissible" role="alert">
		  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		  <strong><? echo $_GET['mensaje'];?></strong>
		</div>
	</p>
<? }?>


<?
if(isset($_GET['select'])){
	include ("solicitud_select.php");
}else if(isset($_GET['add'])){
	include ("tipo_solicitud.php");
}else if(isset($_GET['detail']) && isset($_GET['idsolicitud'])){
	include ("solicitud_detail.php");
}else if(isset($_GET['detailBlock'])){
	include ("solicitud_detailBlock.php");
}else if(isset($_GET['ordinay'])){
	include('solicitud_ordinaria.php');
}else if(isset($_GET['collective'])){
	include('solicitud_colectiva.php');
}else if(isset($_GET['detail']) && isset($_GET['idsolicitud_colectiva'])){
	include('solicitud_detail_colectiva.php');
}
?>