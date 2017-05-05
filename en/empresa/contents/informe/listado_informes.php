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
$ano_actual = date('Y', time());

//24_02_2017$row_informes = mysql_query("SELECT informe_general.*, trim1.total_trim1, trim2.total_trim2, trim3.total_trim3, trim4.total_trim4, ROUND(SUM(trim1.total_trim1 + trim2.total_trim2 + trim3.total_trim3 + trim4.total_trim4), 2) AS 'balance_final' FROM informe_general LEFT JOIN trim1 ON informe_general.trim1 = trim1.idtrim1 LEFT JOIN trim2 ON informe_general.trim2 = trim2.idtrim2 LEFT JOIN trim3 ON informe_general.trim3 = trim3.idtrim3 LEFT JOIN trim4 ON informe_general.trim4 = trim4.idtrim4 WHERE informe_general.idempresa = $idempresa", $dspp) or die(mysql_error());
$row_informes = mysql_query("SELECT informe_general.*, trim1.cuota_uso_trim1, trim2.cuota_uso_trim2, trim3.cuota_uso_trim3, trim4.cuota_uso_trim4 FROM informe_general LEFT JOIN trim1 ON informe_general.trim1 = trim1.idtrim1 LEFT JOIN trim2 ON informe_general.trim2 = trim2.idtrim2 LEFT JOIN trim3 ON informe_general.trim3 = trim3.idtrim3 LEFT JOIN trim4 ON informe_general.trim4 = trim4.idtrim4 WHERE informe_general.idempresa = $idempresa", $dspp) or die(mysql_error());

//$row_informes = mysql_query("SELECT informe_general.*, trim1.cuota_uso_trim1, trim2.cuota_uso_trim2, trim3.cuota_uso_trim3, trim4.cuota_uso_trim4 FROM informe_general INNER JOIN trim1 ON informe_general.trim1 = trim1.idtrim1 INNER JOIN trim2 ON informe_general.trim2 = trim2.idtrim2 INNER JOIN trim3 ON informe_general.trim3 = trim3.idtrim3 INNER JOIN trim4 ON informe_general.trim4 = trim4.idtrim4 WHERE informe_general.idempresa = $idempresa", $dspp) or die(mysql_error());
$numero_informes = mysql_num_rows($row_informes);


echo "<h4>Number of current reports: $numero_informes</h4>";
?>
<table class="table table-bordered" style="font-size:12px;">
	<thead>
		<tr>
			<th>Status</th>
			<th>Id general report</th>
			<th>Year general report</th>
			<th>Quarter 1</th>
			<th>Quarter 2</th>
			<th>Quarter 3</th>
			<th>Quarter 4</th>
			<th>Total</th>
		</tr>
	</thead>
	<tbody>
		<?php
		if($numero_informes >= 1){
			while($listado = mysql_fetch_assoc($row_informes)){
				$balance_final = $listado['cuota_uso_trim1'] + $listado['cuota_uso_trim2'] + $listado['cuota_uso_trim3'] + $listado['cuota_uso_trim4'];
			?>
			<tr>
				<td><?php echo $listado['estado_informe']; ?></td>
				<td><?php echo $listado['idinforme_general']; ?></td>
				<td><?php echo date('Y',$listado['ano']); ?></td>
				<td><?php echo number_format($listado['cuota_uso_trim1'],2); ?></td>
				<td><?php echo number_format($listado['cuota_uso_trim2'],2); ?></td>
				<td><?php echo number_format($listado['cuota_uso_trim3'],2); ?></td>
				<td><?php echo number_format($listado['cuota_uso_trim4'],2); ?></td>
				<td><?php echo number_format(round($balance_final,2),2); ?></td>
				
			</tr>
			<?php
			}
		}else{
			echo "<tr><td colspan='8'>No records found</td></tr>";
		}
		?>
	</tbody>
</table>