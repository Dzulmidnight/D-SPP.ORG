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
	$query_com = "SELECT * FROM com where idoc='".$_SESSION['idoc']."' ORDER BY nombre ASC";
}else{
	$query_com = "SELECT * FROM com where idoc='".$_SESSION['idoc']."' ORDER BY nombre ASC";
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
<div class="panel panel-default">
  <div class="panel-heading">Lista Empresas</div>
  <div class="panel-body">
    <table class="table table-bordered table-hover" style="font-size:12px;">
      <thead>
        <tr>
          <th>IDF</th>
          <!--<th>Nombre</th>-->
          <!--<th>Abreviación</th>-->
          <th>Información</th>
          <!--<th>Email</th>
          <th>Teléfono Oficinas</th>
          <th>País</th>-->
          <!--<th>OC</th>-->
          <th>Dirección Oficinas</th>
          <th>Dirección Fiscal</th>
          <th>RFC</th>
          <th>RUC</th>
          <th>Eliminar</th>
        </tr>
      </thead>
      <tbody>
        <?php $cont=0; while ($row_com = mysql_fetch_assoc($com)) {$cont++; ?>
          <tr class="text-justify" style="font-size:12px;">
            <td>
              <a class="btn btn-sm btn-primary" style="width:100%" href="?COM&amp;detail&amp;idcom=<?php echo $row_com['idcom']; ?>&contact">Consultar<br><?php echo $row_com['idf']; ?></a><hr style="padding-bottom:0;margin:10px"><?php echo "<p>".$row_com['nombre'].", <u>".$row_com['abreviacion']."</u></p>"; ?>
            </td>
            <!--<td><?php echo $row_com['nombre'].", <u>".$row_com['abreviacion']."</u>"; ?></td>-->
            <!--<td><?php echo $row_com['abreviacion']; ?></td>-->
            <td>
              <?php 
                echo "<p>".$row_com['sitio_web']."</p><p>".$row_com['email']."</p><p>".$row_com['telefono']."</p>".$row_com['pais']; 
              ?>
            </td>
            <!--<td><?php echo $row_com['telefono']; ?></td>
            <td><?php echo $row_com['email']; ?></td>
            <td><?php echo $row_com['pais']; ?></td>-->


            <td><?php echo $row_com['direccion']; ?></td>
            <td><?php echo $row_com['direccion_fiscal']; ?></td>
            <td><?php echo $row_com['rfc']; ?></td>
            <td><?php echo $row_com['ruc']; ?></td>
            <td>
            <form action="" method="post">
            <button class="btn btn-danger" type="submit" value="Eliminar">
              <span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Eliminar
            </button>
            
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
      </tbody>
    </table>
  </div>
</div>

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
