<?
if(isset($_GET['SOLICITUD'])){
	include ("contents/solicitud/solicitud.php");
}else if(isset($_GET['INFORME'])){
	include ("contents/informe/informe.php");
}else if(isset($_GET['OPP'])){
	include ("contents/opp/opp.php");
}else{
	include("contents/main.php");
}
?>