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

if(isset($_POST['com_delete'])){
	$query=sprintf("delete from com where idcom = %s",GetSQLValueString($_POST['idcom'], "text"));
	$ejecutar=mysql_query($query,$dspp) or die(mysql_error());
}

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_com = 20;
$pageNum_com = 0;
if (isset($_GET['pageNum_com'])) {
  $pageNum_com = $_GET['pageNum_com'];
}
$startRow_com = $pageNum_com * $maxRows_com;

mysql_select_db($database_dspp, $dspp);
if(isset($_GET['query'])){
	$query_com = "SELECT * FROM com where idoc='".$_GET['query']."' ORDER BY nombre ASC";
}else{
	$query_com = "SELECT * FROM com ORDER BY nombre ASC";
}
$query_limit_com = sprintf("%s LIMIT %d, %d", $query_com, $startRow_com, $maxRows_com);
$com = mysql_query($query_limit_com, $dspp) or die(mysql_error());
//$row_com = mysql_fetch_assoc($com);

if (isset($_GET['totalRows_com'])) {
  $totalRows_com = $_GET['totalRows_com'];
} else {
  $all_com = mysql_query($query_com);
  $totalRows_com = mysql_num_rows($all_com);
}
$totalPages_com = ceil($totalRows_com/$maxRows_com)-1;

$queryString_com = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_com") == false && 
        stristr($param, "totalRows_com") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_com = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_com = sprintf("&totalRows_com=%d%s", $totalRows_com, $queryString_com);
?>
<h4>Consulta com</h4>
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
  <?php $cont=0; while ($row_com = mysql_fetch_assoc($com)) {$cont++; ?>
    <tr>
      <td><a class="btn btn-primary" style="width:100%" href="?com&amp;detail&amp;idcom=<?php echo $row_com['idcom']; ?>&contact"><?php echo $row_com['idf']; ?></a></td>
      <td><?php echo $row_com['nombre']; ?></td>
      <td><?php echo $row_com['abreviacion']; ?></td>
      <td><?php echo $row_com['sitio_web']; ?></td>
      <td><?php echo $row_com['telefono']; ?></td>
      <td><?php echo $row_com['email']; ?></td>
      <td><?php echo $row_com['pais']; ?></td>
      <td>
			
      <?
$query_tcom = "SELECT abreviacion FROM oc where idoc='".$row_com['idoc']."'";
$tcom = mysql_query($query_tcom, $dspp) or die(mysql_error());
$row_tcom = mysql_fetch_assoc($tcom);
			?>
      <a class="btn btn-info" style="width:100%" href="?OC&amp;detail&amp;idoc=<?php echo $row_com['idoc']; ?>&contact">
			<?php  echo $row_tcom['abreviacion']; ?></a></td>
      <td><?php echo $row_com['razon_social']; ?></td>
      <td><?php echo $row_com['direccion_fiscal']; ?></td>
      <td><?php echo $row_com['rfc']; ?></td>
      <td>
      <form action="" method="post">
      <input class="btn btn-danger" type="submit" value="Eliminar" />
      <input type="hidden" value="com eliminado correctamente" name="mensaje" />
      <input type="hidden" value="1" name="com_delete" />
      <input type="hidden" value="<?php echo $row_com['idcom']; ?>" name="idcom" />
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
<td width="20"><?php if ($pageNum_com > 0) { // Show if not first page ?>
<a href="<?php printf("%s?pageNum_com=%d%s", $currentPage, 0, $queryString_com); ?>">
<span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span>
</a>
<?php } // Show if not first page ?></td>
<td width="20"><?php if ($pageNum_com > 0) { // Show if not first page ?>
<a href="<?php printf("%s?pageNum_com=%d%s", $currentPage, max(0, $pageNum_com - 1), $queryString_com); ?>">
<span class="glyphicon glyphicon-backward" aria-hidden="true"></span>
</a>
<?php } // Show if not first page ?></td>
<td width="20"><?php if ($pageNum_com < $totalPages_com) { // Show if not last page ?>
<a href="<?php printf("%s?pageNum_com=%d%s", $currentPage, min($totalPages_com, $pageNum_com + 1), $queryString_com); ?>">
<span class="glyphicon glyphicon-forward" aria-hidden="true"></span>
</a>
<?php } // Show if not last page ?></td>
<td width="20"><?php if ($pageNum_com < $totalPages_com) { // Show if not last page ?>
<a href="<?php printf("%s?pageNum_com=%d%s", $currentPage, $totalPages_com, $queryString_com); ?>">
<span class="glyphicon glyphicon-fast-forward" aria-hidden="true"></span>
</a>
<?php } // Show if not last page ?></td>
</tr>
</table>
<?php
mysql_free_result($com);
?>
