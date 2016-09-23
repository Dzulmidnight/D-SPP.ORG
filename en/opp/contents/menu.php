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


$maxRows_opp = 30;
$pageNum_opp = 0;
if (isset($_GET['pageNum_opp'])) {
  $pageNum_opp = $_GET['pageNum_opp'];
}
$startRow_opp = $pageNum_opp * $maxRows_opp;

mysql_select_db($database_dspp, $dspp);
if(isset($_GET['idsolicitud'])){
  $query_opp = "SELECT idsolicitud_certificacion, fecha_elaboracion, status FROM solicitud_certificacion WHERE idsolicitud_certificacion = $_GET[idsolicitud] ORDER BY fecha_elaboracion DESC";
}else{
  $query_opp = "SELECT idsolicitud_certificacion, fecha_elaboracion, status FROM solicitud_certificacion ORDER BY fecha_elaboracion DESC";
}

$query_limit_opp = sprintf("%s LIMIT %d, %d", $query_opp, $startRow_opp, $maxRows_opp);
$opp = mysql_query($query_limit_opp, $dspp) or die(mysql_error());
$row_opp = mysql_fetch_assoc($opp);

if (isset($_GET['totalRows_opp'])) {
  $totalRows_opp = $_GET['totalRows_opp'];
} else {
  $all_opp = mysql_query($query_opp);
  $totalRows_opp = mysql_num_rows($all_opp);
}
$totalPages_opp = $totalRows_opp/$maxRows_opp = 15;
$pageNum_opp = 0;
if (isset($_GET['pageNum_opp'])) {
  $pageNum_opp = $_GET['pageNum_opp'];
}
$startRow_opp = $pageNum_opp * $maxRows_opp;



mysql_select_db($database_dspp, $dspp);
if(isset($_GET['idsolicitud'])){
  $query_opp = "SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.fecha_elaboracion, solicitud_certificacion.status, oc.idoc, oc.abreviacion FROM solicitud_certificacion INNER JOIN oc ON solicitud_certificacion.idoc = oc.idoc WHERE solicitud_certificacion.idopp = $_SESSION[idopp] ORDER BY fecha_elaboracion DESC";
}else{
  $query_opp = "SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.fecha_elaboracion, solicitud_certificacion.status, oc.idoc, oc.abreviacion FROM solicitud_certificacion INNER JOIN oc ON solicitud_certificacion.idoc = oc.idoc WHERE solicitud_certificacion.idopp = $_SESSION[idopp] ORDER BY fecha_elaboracion DESC";
}
$query_limit_opp = sprintf("%s LIMIT %d, %d", $query_opp, $startRow_opp, $maxRows_opp);
$opp = mysql_query($query_limit_opp, $dspp) or die(mysql_error());
$row_opp = mysql_fetch_assoc($opp);




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


<ul class="nav nav-sidebar">
  <li <? if(isset($_GET['SOLICITUD'])){?> class="active" <? }?>>
    <a href="?SOLICITUD&select">Applications</a>
  </li>
  <li <? if(isset($_GET['OPP'])){?> class="active" <?}?>>
    <a href="?OPP&detail">OPP information</a>
  </li>
  <li <? if(isset($_GET['.'])){?> class="active" <? }?>>
    <a href="#">---</a>
  </li>
  <li><a href="<?php echo $logoutAction ?>">Sign off</a></li>

<? if(!isset($_GET['detail'])){?>
<? }else{?>

  <? if(isset($_GET['SOLICITUD'])){?>

    <table class="table">
      <tr>
      <th>Applications List</th>
      </tr>
      <?php do { ?>
          <?php $fecha = $row_opp['fecha_elaboracion']; ?>
        <tr>
        <td>
        <a  class="btn btn-<? if($_GET['idsolicitud']==$row_opp['idsolicitud_certificacion']){echo "success";}else{echo "primary";}?>" style="width:100%" <?php if($row_opp['status'] != "2"){echo 'href="?SOLICITUD&detailBlock&idsolicitud';}else{echo 'href="?SOLICITUD&detail&idsolicitud';} ?>=<?php echo $row_opp['idsolicitud_certificacion']; ?>&contact"><?php echo date("d/m/Y",$fecha)." | ".$row_opp['abreviacion']; ?></a>
        </td>
        </tr>
      <?php } while ($row_opp = mysql_fetch_assoc($opp)); ?>
    </table>

      <?php
      mysql_free_result($opp);
      ?>
  <? }?>

<? }?>

<? if(!isset($_GET['detailBlock'])){?>
<? }else{?>

  <? if(isset($_GET['SOLICITUD'])){?>

    <table class="table">
      <tr>
      <th>Applications List</th>
      </tr>
      <?php do { ?>
          <?php $fecha = $row_opp['fecha_elaboracion']; ?>
        <tr>
        <td>
        <a  class="btn btn-<? if($_GET['idsolicitud']==$row_opp['idsolicitud_certificacion']){echo "success";}else{echo "primary";}?>" style="width:100%" <?php if($row_opp['status'] != "2"){echo 'href="?SOLICITUD&detailBlock&idsolicitud';}else{echo 'href="?SOLICITUD&detail&idsolicitud';} ?>=<?php echo $row_opp['idsolicitud_certificacion']; ?>&contact"><?php echo date("d/m/Y",$fecha)." | ".$row_opp['abreviacion']; ?></a>
        </td>
        </tr>
      <?php } while ($row_opp = mysql_fetch_assoc($opp)); ?>
    </table>

      <?php
      mysql_free_result($opp);
      ?>
  <? }?>

<? }?>



</ul>