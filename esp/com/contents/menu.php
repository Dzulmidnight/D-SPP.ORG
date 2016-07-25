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

$currentPage = $_SERVER["PHP_SELF"];


$maxRows_com = 30;
$pageNum_com = 0;
if (isset($_GET['pageNum_com'])) {
  $pageNum_com = $_GET['pageNum_com'];
}
$startRow_com = $pageNum_com * $maxRows_com;

mysql_select_db($database_dspp, $dspp);
if(isset($_GET['idsolicitud'])){
//  $query_com = "SELECT idsolicitud_registro, fecha_elaboracion, status FROM solicitud_registro WHERE idsolicitud_registro = $_GET[idsolicitud] ORDER BY fecha_elaboracion DESC";
  $query_com = "SELECT idsolicitud_registro, fecha_elaboracion FROM solicitud_registro WHERE idsolicitud_registro = $_GET[idsolicitud] ORDER BY fecha_elaboracion DESC";

}else{
//  $query_com = "SELECT idsolicitud_registro, fecha_elaboracion, status FROM solicitud_registro ORDER BY fecha_elaboracion DESC";
  $query_com = "SELECT idsolicitud_registro, fecha_elaboracion FROM solicitud_registro ORDER BY fecha_elaboracion DESC";

}

$query_limit_com = sprintf("%s LIMIT %d, %d", $query_com, $startRow_com, $maxRows_com);
$com = mysql_query($query_limit_com, $dspp) or die(mysql_error());
$row_com = mysql_fetch_assoc($com);

if (isset($_GET['totalRows_com'])) {
  $totalRows_com = $_GET['totalRows_com'];
} else {
  $all_com = mysql_query($query_com);
  $totalRows_com = mysql_num_rows($all_com);
}
$totalPages_com = $totalRows_com/$maxRows_com = 15;
$pageNum_com = 0;
if (isset($_GET['pageNum_com'])) {
  $pageNum_com = $_GET['pageNum_com'];
}
$startRow_com = $pageNum_com * $maxRows_com;



mysql_select_db($database_dspp, $dspp);
if(isset($_GET['idsolicitud'])){
//  $query_com = "SELECT solicitud_registro.idsolicitud_registro, solicitud_registro.fecha_elaboracion, solicitud_registro.status, oc.idoc, oc.abreviacion FROM solicitud_registro INNER JOIN oc ON solicitud_registro.idoc = oc.idoc WHERE solicitud_registro.idcom = $_SESSION[idcom] ORDER BY fecha_elaboracion DESC";
  $query_com = "SELECT solicitud_registro.idsolicitud_registro, solicitud_registro.fecha_elaboracion, solicitud_registro.status_interno, oc.idoc, oc.abreviacion FROM solicitud_registro INNER JOIN oc ON solicitud_registro.idoc = oc.idoc WHERE solicitud_registro.idcom = $_SESSION[idcom] ORDER BY fecha_elaboracion DESC";

}else{
//  $query_com = "SELECT solicitud_registro.idsolicitud_registro, solicitud_registro.fecha_elaboracion, solicitud_registro.status, oc.idoc, oc.abreviacion FROM solicitud_registro INNER JOIN oc ON solicitud_registro.idoc = oc.idoc WHERE solicitud_registro.idcom = $_SESSION[idcom] ORDER BY fecha_elaboracion DESC";
//  $query_com = "SELECT solicitud_registro.idsolicitud_registro, solicitud_registro.fecha_elaboracion,  oc.idoc, oc.abreviacion FROM solicitud_registro INNER JOIN oc ON solicitud_registro.idoc = oc.idoc WHERE solicitud_registro.idcom = $_SESSION[idcom] ORDER BY fecha_elaboracion DESC";
}
$query_limit_com = sprintf("%s LIMIT %d, %d", $query_com, $startRow_com, $maxRows_com);
$com = mysql_query($query_limit_com, $dspp) or die(mysql_error());
$row_com = mysql_fetch_assoc($com);




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


<ul class="nav nav-sidebar">
  <li <? if(isset($_GET['SOLICITUD'])){?> class="active" <? }?>>
    <a href="?SOLICITUD&select">Solicitudes</a>
  </li>
  <li <? if(isset($_GET['COM'])){?> class="active" <?}?>>
    <a href="?COM&detail">Información Empresa</a>
  </li>
  <li <? if(isset($_GET['.'])){?> class="active" <? }?>>
    <a href="#">---</a>
  </li>

  <li><a href="<?php echo $logoutAction ?>">Cerrar Sesión</a></li>

<? if(!isset($_GET['detail'])){?>
<? }else{?>

  <? if(isset($_GET['SOLICITUD'])){?>

    <table class="table">
      <tr>
      <th>Listado Solicitudes</th>
      </tr>
      <?php do { ?>
          <?php $fecha = $row_com['fecha_elaboracion']; ?>
        <tr>
        <td>
        <a  class="btn btn-<? if($_GET['idsolicitud']==$row_com['idsolicitud_registro']){echo "success";}else{echo "primary";}?>" style="width:100%" <?php if($row_com['status_interno'] != "2"){echo 'href="?SOLICITUD&detailBlock&idsolicitud';}else{echo 'href="?SOLICITUD&detail&idsolicitud';} ?>=<?php echo $row_com['idsolicitud_registro']; ?>&contact"><?php echo date("d/m/Y",$fecha)." | ".$row_com['abreviacion']; ?></a>
        </td>
        </tr>
      <?php } while ($row_com = mysql_fetch_assoc($com)); ?>
    </table>

      <?php
      mysql_free_result($com);
      ?>
  <? }?>

<? }?>

<? if(!isset($_GET['detailBlock'])){?>
<? }else{?>

  <? if(isset($_GET['SOLICITUD'])){?>

    <table class="table">
      <tr>
      <th>Listado Solicitudes</th>
      </tr>
      <?php do { ?>
          <?php $fecha = $row_com['fecha_elaboracion']; ?>
        <tr>
        <td>
        <a  class="btn btn-<? if($_GET['idsolicitud']==$row_com['idsolicitud_registro']){echo "success";}else{echo "primary";}?>" style="width:100%" <?php if($row_com['status_interno'] != "2"){echo 'href="?SOLICITUD&detailBlock&idsolicitud';}else{echo 'href="?SOLICITUD&detail&idsolicitud';} ?>=<?php echo $row_com['idsolicitud_registro']; ?>&contact"><?php echo date("d/m/Y",$fecha)." | ".$row_com['abreviacion']; ?></a>
        </td>
        </tr>
      <?php } while ($row_com = mysql_fetch_assoc($com)); ?>
    </table>

      <?php
      mysql_free_result($com);
      ?>
  <? }?>

<? }?>



</ul>