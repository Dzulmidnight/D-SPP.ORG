<?php
		require_once("../../dompdf/dompdf_config.inc.php");
		require_once('../Connections/dspp.php');

	$query_opp = "SELECT *, opp.nombre AS 'nombreOPP', status.idstatus, status.nombre AS 'nombreStatus' FROM opp LEFT JOIN status ON opp.estado = status.idstatus ORDER BY opp.nombre ASC";
	$opp = mysql_query($query_opp) or die(mysql_error());

?>



<?

$codigoHTML='
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin título</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="6" bgcolor="skyblue"><CENTER><strong>REPORTE DE LA TABLA USUARIOS</strong></CENTER></td>
  </tr>
  <tr bgcolor="red">
    <td><strong>CODIGO</strong></td>
    <td><strong>NOMBRE</strong></td>
    <td><strong>APELLIDO</strong></td>
    <td><strong>PAIS</strong></td>
    <td><strong>EDAD</strong></td>
    <td><strong>DNI</strong></td>
  </tr>
  



	<tr>
		<td>nombre 1</td>
		<td>apellido</td>
		<td>pais</td>
		<td>email</td>
		<td>estatus</td>										
	</tr>';
	

$codigoHTML.='
</table>
</body>
</html>';
$codigoHTML=utf8_encode($codigoHTML);
$dompdf=new DOMPDF();
$dompdf->load_html($codigoHTML);
ini_set("memory_limit","128M");
$dompdf->render();
$dompdf->stream("Reporte_tabla_usuarios.pdf");
?>