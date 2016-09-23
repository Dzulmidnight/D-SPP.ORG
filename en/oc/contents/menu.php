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
  $query_opp = "SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.idoc , solicitud_certificacion.fecha_elaboracion, solicitud_certificacion.status,  opp.idopp, opp.abreviacion FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE solicitud_certificacion.idsolicitud_certificacion = $_GET[idsolicitud] && solicitud_certificacion.idoc = $_SESSION[idoc] ORDER BY solicitud_certificacion.fecha_elaboracion DESC";
}else{
  $query_opp = "SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.idoc , solicitud_certificacion.fecha_elaboracion, solicitud_certificacion.status,  opp.idopp, opp.abreviacion FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE solicitud_certificacion.idoc = $_SESSION[idoc] ORDER BY fecha_elaboracion DESC";
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
  $query_opp = "SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.idoc , solicitud_certificacion.fecha_elaboracion, solicitud_certificacion.status,  opp.idopp, opp.abreviacion FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE solicitud_certificacion.idoc = $_SESSION[idoc] ORDER BY fecha_elaboracion DESC";
}else{
  $query_opp = "SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.idoc , solicitud_certificacion.fecha_elaboracion, solicitud_certificacion.status,  opp.idopp, opp.abreviacion FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE  solicitud_certificacion.idoc = $_SESSION[idoc] ORDER BY fecha_elaboracion DESC";
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





mysql_select_db($database_dspp, $dspp);
$query_opp2 = "SELECT idopp, idf, abreviacion FROM opp WHERE idoc = $_SESSION[idoc] ORDER BY idf ASC";
$query_limit_opp2 = sprintf("%s LIMIT %d, %d", $query_opp2, $startRow_opp, $maxRows_opp);
$opp2 = mysql_query($query_limit_opp2, $dspp) or die(mysql_error());
$row_opp2 = mysql_fetch_assoc($opp2);

if (isset($_GET['totalRows_opp2'])) {
  $totalRows_opp2 = $_GET['totalRows_opp2'];
} else {
  $all_opp = mysql_query($query_opp2);
  $totalRows_opp2 = mysql_num_rows($all_opp);
}


if(isset($_GET['idsolicitud'])){
  $query_com = "SELECT solicitud_registro.idsolicitud_registro, solicitud_registro.idcom, solicitud_registro.idoc , solicitud_registro.fecha_elaboracion, solicitud_registro.status_interno,  com.idcom, com.abreviacion FROM solicitud_registro INNER JOIN com ON solicitud_registro.idcom = com.idcom WHERE solicitud_registro.idoc = $_SESSION[idoc] ORDER BY fecha_elaboracion DESC";
}else{
  $query_com = "SELECT solicitud_registro.idsolicitud_registro, solicitud_registro.idcom, solicitud_registro.idoc , solicitud_registro.fecha_elaboracion, solicitud_registro.status_interno,  com.idcom, com.abreviacion FROM solicitud_registro INNER JOIN com ON solicitud_registro.idcom = com.idcom WHERE  solicitud_registro.idoc = $_SESSION[idoc] ORDER BY fecha_elaboracion DESC";
}
$com = mysql_query($query_com,$dspp) or die(mysql_error());
$row_com = mysql_fetch_assoc($com);



mysql_select_db($database_dspp, $dspp);
$query_com2 = "SELECT idcom, idf, abreviacion FROM com WHERE idoc = $_SESSION[idoc] ORDER BY idf ASC";
$com2 = mysql_query($query_com2,$dspp) or die(mysql_error());
$row_com2 = mysql_fetch_assoc($com2);





?>
<ul class="nav nav-sidebar">
  <li <? if(isset($_GET['SOLICITUD'])){?> class="active" <? }?>>
    <a href="?SOLICITUD&select">Applications</a>
  </li>
  <li <? if(isset($_GET['OPP'])){ ?> class="active" <?}?>>
    <a href="?OPP&select">OPP information</a>
  </li>
  <li <? if(isset($_GET['COM'])){ ?> class="active" <?}?>>
    <a href="?COM&select">Information Companies</a>
  </li>
  <li <? if(isset($_GET['OC'])){ ?> class="active" <?}?>>
    <a href="?OC&detail">OC Information</a>
  </li>  
  <li <? if(isset($_GET['.'])){?> class="active" <? }?>>
    <a href="#">---</a>
  </li>

  <li><a href="<?php echo $logoutAction ?>">Sign off</a></li>

<!-------------------------------------------- INICIA LISTA LATERAL DE LAS SOLICITUDES OPP-------------------------------------------------------->

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
        <a  class="btn btn-<? if($_GET['idsolicitud']==$row_opp['idsolicitud_certificacion']){echo "success";}else{echo "primary";}?>" style="width:100%" <?php if($row_opp['status'] == "APROBADO" || $row_opp['status'] == "REVISION"){echo 'href="?SOLICITUD&detailBlock&idsolicitud';}else{echo 'href="?SOLICITUD&detailBlock&idsolicitud';} ?>=<?php echo $row_opp['idsolicitud_certificacion']; ?>"&contact"><?php echo date("d/m/Y",$fecha)." | ".$row_opp['abreviacion']; ?></a>
        </td>
        </tr>
      <?php } 
      while ($row_opp = mysql_fetch_assoc($opp)); ?>
    </table>

      <?php
      mysql_free_result($opp);
      ?>
  <? }?>
<? }?>

<!-------------------------------------------- INICIA LISTA LATERAL DE LAS SOLICITUDES COM-------------------------------------------------------->

<? if(!isset($_GET['detailCOM'])){?>
<? }else{?>

  <? if(isset($_GET['SOLICITUD'])){?>

    <table class="table">
      <tr>
      <th>Applications List</th>
      </tr>
      <?php do { ?>
          <?php $fecha = $row_com['fecha_elaboracion']; ?>
        <tr>
        <td>
        <a  class="btn btn-<? if($_GET['idsolicitud']==$row_com['idsolicitud_registro']){echo "success";}else{echo "primary";}?>" style="width:100%" <?php if($row_com['status_interno'] == "APROBADO" || $row_com['status_interno'] == "REVISION"){echo 'href="?SOLICITUD&detailCOM&idsolicitud';}else{echo 'href="?SOLICITUD&detailCOM&idsolicitud';} ?>=<?php echo $row_com['idsolicitud_registro']; ?>"&contact"><?php echo date("d/m/Y",$fecha)." | ".$row_com['abreviacion']; ?></a>
        </td>
        </tr>
      <?php } 
      while ($row_com = mysql_fetch_assoc($com)); ?>
    </table>

      <?php
      mysql_free_result($com);
      ?>
  <? }?>
<? }?>


<!-------------------------------------------- INICIA LISTA LATERAL DE LOS OPPS-------------------------------------------------------->

<? if(!isset($_GET['detail'])){?>
<? }else{?>

<? if(isset($_GET['OPP'])){?><table class="table">
  <tr>
    <th>OPP list</th>
  </tr>
  <?php do { ?>
    <tr>
      <td><a  class="btn btn-<? if($_GET['idopp']==$row_opp2['idopp']){echo "success";}else{echo "primary";}?>" style="width:100%" href="?OPP&detail&idopp=<?php echo $row_opp2['idopp']; ?>&contact"><?php echo $row_opp2['idf']; ?></a></td>
    </tr>
    <?php } while ($row_opp2 = mysql_fetch_assoc($opp2)); ?>
</table><? }?>

<? }?>
</ul>



<!-------------------------------------------- INICIA LISTA LATERAL DE LOS COMs-------------------------------------------------------->

<? if(!isset($_GET['detail'])){?>
<? }else{?>

<? if(isset($_GET['COM'])){?><table class="table">
  <tr>
    <th>Companies list</th>
  </tr>
  <?php do { ?>
    <tr>
      <td><a  class="btn btn-<? if($_GET['idcom']==$row_com2['idcom']){echo "success";}else{echo "primary";}?>" style="width:100%" href="?COM&detail&idcom=<?php echo $row_com2['idcom']; ?>&contact"><?php echo $row_com2['idf']; ?></a></td>
    </tr>
    <?php } while ($row_com2 = mysql_fetch_assoc($com2)); ?>
</table><? }?>

<? }?>
</ul>