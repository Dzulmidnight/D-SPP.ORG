<?
if(isset($_GET['CRM'])){
	include('http://d-spp.org/crm/');
	//include ("..contents/crm/crm.php");
}else if(isset($_GET['MEMBRESIAS'])){
	include ("contents/membresias/membresias.php");
}else if(isset($_GET['REPORTES'])){
	include ("contents/reportes_comerciales/menu.php");
}else if(isset($_GET['FINANZAS'])){
	include ("contents/ajuste_financiero/ajuste_financiero.php");
}else if(isset($_GET['ESTADISTICAS'])){
	include ("contents/estadisticas/estadisticas.php");
}else if(isset($_GET['SOLICITUD'])){
	include ("contents/solicitud/solicitud.php");
}else if(isset($_GET['CORREO'])){
	include ("contents/correo/correo.php");
}else if(isset($_GET['DOCUMENTACION'])){
	include ("contents/documentacion/documentacion.php");
}else if(isset($_GET['OPP'])){
	include ("contents/opp/opp.php");
}else if(isset($_GET['OC'])){
	include ("contents/oc/oc.php");
}else if(isset($_GET['EMPRESAS'])){
	include ("contents/empresas/empresa.php");
}else{
	include("contents/main.php");
}

?>