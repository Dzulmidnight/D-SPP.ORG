<?php require_once('../Connections/dspp.php'); ?>
<?php
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

if(isset($_POST['opp_delete'])){
	$query=sprintf("delete from opp where idopp = %s",GetSQLValueString($_POST['idopp'], "text"));
	$ejecutar=mysql_query($query,$dspp) or die(mysql_error());
}

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_opp = 20;
$pageNum_opp = 0;
if (isset($_GET['pageNum_opp'])) {
  $pageNum_opp = $_GET['pageNum_opp'];
}
$startRow_opp = $pageNum_opp * $maxRows_opp;

mysql_select_db($database_dspp, $dspp);
if(isset($_GET['query'])){
	$query_opp = "SELECT * FROM opp where idoc='".$_GET['query']."' ORDER BY nombre ASC";
}else{
	$query_opp = "SELECT * FROM opp ORDER BY nombre ASC";
}
$query_limit_opp = sprintf("%s LIMIT %d, %d", $query_opp, $startRow_opp, $maxRows_opp);
$opp = mysql_query($query_limit_opp, $dspp) or die(mysql_error());
//$row_opp = mysql_fetch_assoc($opp);

if (isset($_GET['totalRows_opp'])) {
  $totalRows_opp = $_GET['totalRows_opp'];
} else {
  $all_opp = mysql_query($query_opp);
  $totalRows_opp = mysql_num_rows($all_opp);
}
$totalPages_opp = ceil($totalRows_opp/$maxRows_opp)-1;

$queryString_opp = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_opp") == false && 
        stristr($param, "totalRows_opp") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_opp = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_opp = sprintf("&totalRows_opp=%d%s", $totalRows_opp, $queryString_opp);
?>
<h4>Consulta OPP</h4>
<table class="table table-bordered">
  <tr>
    <th>IDF</th>
    <th>Nombre</th>
    <th>Abreviación</th>
    <th>Sitio WEB</th>
    <th>Email</th>
    <th>Teléfono Oficinas</th>
    <th>País</th>
    <th>OC</th>
    <th>Razón social</th>
    <th>Dirección fiscal</th>
    <th>RFC</th>
    <th>Eliminar</th>
  </tr>
  <?php $cont=0; while ($row_opp = mysql_fetch_assoc($opp)) {$cont++; ?>
    <tr>
      <td><a class="btn btn-primary" style="width:100%" href="?OPP&amp;detail&amp;idopp=<?php echo $row_opp['idopp']; ?>&contact"><?php echo $row_opp['idf']; ?></a></td>
      <td><?php echo $row_opp['nombre']; ?></td>
      <td><?php echo $row_opp['abreviacion']; ?></td>
      <td><?php echo $row_opp['sitio_web']; ?></td>
      <td><?php echo $row_opp['telefono']; ?></td>
      <td><?php echo $row_opp['email']; ?></td>
      <td><?php echo $row_opp['pais']; ?></td>
      <td>
			
      <?
$query_topp = "SELECT abreviacion FROM oc where idoc='".$row_opp['idoc']."'";
$topp = mysql_query($query_topp, $dspp) or die(mysql_error());
$row_topp = mysql_fetch_assoc($topp);
			?>
      <a class="btn btn-info" style="width:100%" href="?OC&amp;detail&amp;idoc=<?php echo $row_opp['idoc']; ?>&contact">
			<?php  echo $row_topp['abreviacion']; ?></a></td>
      <td><?php echo $row_opp['razon_social']; ?></td>
      <td><?php echo $row_opp['direccion_fiscal']; ?></td>
      <td><?php echo $row_opp['rfc']; ?></td>
      <td>
      <form action="" method="post">
      <input class="btn btn-danger" type="submit" value="Eliminar" />
      <input type="hidden" value="OPP eliminado correctamente" name="mensaje" />
      <input type="hidden" value="1" name="opp_delete" />
      <input type="hidden" value="<?php echo $row_opp['idopp']; ?>" name="idopp" />
      </form>
      </td>
    </tr>
    <?php }  ?>
    <? if($cont==0){?>
    <tr><td colspan="11" class="alert alert-info" role="alert">No se encontraron registros</td></tr>
    <? }?>
</table>
<table>
<tr>
<td width="20"><?php if ($pageNum_opp > 0) { // Show if not first page ?>
<a href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, 0, $queryString_opp); ?>">
<span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span>
</a>
<?php } // Show if not first page ?></td>
<td width="20"><?php if ($pageNum_opp > 0) { // Show if not first page ?>
<a href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, max(0, $pageNum_opp - 1), $queryString_opp); ?>">
<span class="glyphicon glyphicon-backward" aria-hidden="true"></span>
</a>
<?php } // Show if not first page ?></td>
<td width="20"><?php if ($pageNum_opp < $totalPages_opp) { // Show if not last page ?>
<a href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, min($totalPages_opp, $pageNum_opp + 1), $queryString_opp); ?>">
<span class="glyphicon glyphicon-forward" aria-hidden="true"></span>
</a>
<?php } // Show if not last page ?></td>
<td width="20"><?php if ($pageNum_opp < $totalPages_opp) { // Show if not last page ?>
<a href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, $totalPages_opp, $queryString_opp); ?>">
<span class="glyphicon glyphicon-fast-forward" aria-hidden="true"></span>
</a>
<?php } // Show if not last page ?></td>
</tr>
</table>
<?php
mysql_free_result($opp);
?>
