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

$maxRows_oc = 30;
$pageNum_oc = 0;
if (isset($_GET['pageNum_oc'])) {
  $pageNum_oc = $_GET['pageNum_oc'];
}
$startRow_oc = $pageNum_oc * $maxRows_oc;

mysql_select_db($database_dspp, $dspp);
$query_oc = "SELECT idoc, idf, nombre FROM oc ORDER BY nombre ASC";
$query_limit_oc = sprintf("%s LIMIT %d, %d", $query_oc, $startRow_oc, $maxRows_oc);
$oc = mysql_query($query_limit_oc, $dspp) or die(mysql_error());
$row_oc = mysql_fetch_assoc($oc);

if (isset($_GET['totalRows_oc'])) {
  $totalRows_oc = $_GET['totalRows_oc'];
} else {
  $all_oc = mysql_query($query_oc);
  $totalRows_oc = mysql_num_rows($all_oc);
}
$totalPages_oc = ceil($totalRows_oc/$maxRows_oc)-1;

$maxRows_opp = 30;
$pageNum_opp = 0;
if (isset($_GET['pageNum_opp'])) {
  $pageNum_opp = $_GET['pageNum_opp'];
}
$startRow_opp = $pageNum_opp * $maxRows_opp;

mysql_select_db($database_dspp, $dspp);
$query_opp = "SELECT idopp, idf, abreviacion FROM opp ORDER BY idf ASC";
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
$query_opp = "SELECT idopp, idf, abreviacion FROM opp ORDER BY nombre ASC";
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

/********************* INICIA COM **************************/


$maxRows_com = 30;
$pageNum_com = 0;
if (isset($_GET['pageNum_com'])) {
  $pageNum_com = $_GET['pageNum_com'];
}
$startRow_com = $pageNum_com * $maxRows_com;

mysql_select_db($database_dspp, $dspp);
$query_com = "SELECT idcom, idf, abreviacion FROM com ORDER BY idf ASC";
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
$query_com = "SELECT idcom, idf, abreviacion FROM com ORDER BY nombre ASC";
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
/********************* TERMINA COM **************************/

?>
<ul class="nav nav-sidebar">

  <li <? if(isset($_GET['ESTADISTICAS'])){?> class="active" <? }?>><a href="?ESTADISTICAS&select">Estadisticas</a></li>
  <li <? if(isset($_GET['ANEXOS'])){?> class="active" <? }?>><a href="?ANEXOS&select">Anexos</a></li>
  <li <? if(isset($_GET['CORREO'])){?> class="active" <? }?>><a href="?CORREO&select">Correo</a></li>
  <li <? if(isset($_GET['SOLICITUD'])){?> class="active" <? }?>><a href="?SOLICITUD&select">Solicitudes</a></li>
  <li <? if(isset($_GET['OPP'])){?> class="active" <? }?>><a href="?OPP&select">Información OPP</a></li>
  <li <? if(isset($_GET['OC'])){?> class="active" <? }?>><a href="?OC&select">Información OC</a></li>
  <li <? if(isset($_GET['COM'])){?> class="active" <? }?>><a href="?COM&select">Información Empresas</a></li>

  <li <? if(isset($_GET['.'])){?> class="active" <? }?>><a href="#">---</a></li>

  <li><a href="<?php echo $logoutAction ?>">Cerrar Sesión</a></li>

<? if(!isset($_GET['detail'])){?>
<? }else{?>

<? if(isset($_GET['OPP'])){?>
  <table class="table">
  <tr>
  <th>IDF Listado</th>
  </tr>
  <?php do { ?>
  <tr>
  <td>
  <a  class="btn btn-<? if($_GET['idopp']==$row_opp['idopp']){echo "success";}else{echo "primary";}?>" style="width:100%" href="?OPP&detail&idopp=<?php echo $row_opp['idopp']; ?>&contact"><?php echo substr($row_opp['idf'],0,20)." | ".substr($row_opp['abreviacion'],0,10); ?></a>
  </td>
  </tr>
  <?php } while ($row_opp = mysql_fetch_assoc($opp)); ?>
</table>

<table align="center">
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
<? }?>
<!---------------------------------------- LISTA LATERAL DE COM ------------------------------------------------------------------>
<? if(isset($_GET['COM'])){?>
  <table class="table">
  <tr>
  <th>IDF Listado</th>
  </tr>
  <?php do { ?>
  <tr>
  <td>
  <a  class="btn btn-<? if($_GET['idcom']==$row_com['idcom']){echo "success";}else{echo "primary";}?>" style="width:100%" href="?COM&detail&idcom=<?php echo $row_com['idcom']; ?>&contact"><?php echo substr($row_com['idf'],0,20)." | ".substr($row_com['abreviacion'],0,10); ?></a>
  </td>
  </tr>
  <?php } while ($row_com = mysql_fetch_assoc($com)); ?>
</table>

<table align="center">
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
<? }?>

<!---------------------------------------- LISTA LATERAL DE OC ------------------------------------------------------------------>
<? if(isset($_GET['OC'])){?><table class="table">
  <tr>
    <th>IDF Listado</th>
  </tr>
  <?php do { ?>
    <tr>
      <td><a  class="btn btn-<? if($_GET['idoc']==$row_oc['idoc']){echo "success";}else{echo "primary";}?>" style="width:100%" href="?OC&detail&idoc=<?php echo $row_oc['idoc']; ?>&contact"><?php echo $row_oc['idf']; ?></a></td>
    </tr>
    <?php } while ($row_oc = mysql_fetch_assoc($oc)); ?>
</table><? }?>

<? }?>


<!---------------------------------------------------->

<? if(!isset($_GET['detailBlock'])){?>
<? }else{?>
  <?php 
    $query_solicitud = "SELECT solicitud_certificacion.*,  opp.idopp, opp.abreviacion FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE solicitud_certificacion.idoc = $_GET[query] ORDER BY fecha_elaboracion DESC";
    $solicitud = mysql_query($query_solicitud, $dspp) or die(mysql_error());
    $row_solicitud = mysql_fetch_assoc($solicitud);

   ?>

    <? if(isset($_GET['OC'])){?>

      <table class="table">
        <tr>
          <th>Listado Solicitudes</th>
        </tr>
        <?php do { ?>
        <tr>
        <?php $fecha = $row_solicitud['fecha_elaboracion']; ?>

          <td>
            <a  class="btn btn-<? if($_GET['formato']==$row_solicitud['idsolicitud_certificacion']){echo "success";}else{echo "primary";}?>" style="width:100%" href="?OC&detailBlock&query=<?php echo $_GET['query'];?>&formato=<?php echo $row_solicitud['idsolicitud_certificacion']; ?>"><?php echo date("d/m/Y",$fecha)." | ".$row_solicitud['abreviacion']; ?></a>
          </td>
        </tr>
        <?php } while ($row_solicitud = mysql_fetch_assoc($solicitud)); ?>
      </table>
      <?php
      mysql_free_result($opp);
      ?>

    <? }?>
<? }?>

<!---------------------------------------------------->

<? if(!isset($_GET['detailCOM'])){?>
<? }else{?>
  <?php 
    $query_solicitud = "SELECT solicitud_registro.*,  com.idcom, com.abreviacion FROM solicitud_registro INNER JOIN com ON solicitud_registro.idcom = com.idcom WHERE solicitud_registro.idoc = $_GET[query] ORDER BY fecha_elaboracion DESC";
    $solicitud = mysql_query($query_solicitud, $dspp) or die(mysql_error());
    $row_solicitud = mysql_fetch_assoc($solicitud);

   ?>

    <? if(isset($_GET['COM'])){?>

      <table class="table">
        <tr>
          <th>Listado Solicitudes</th>
        </tr>
        <?php do { ?>
        <tr>
        <?php $fecha = $row_solicitud['fecha_elaboracion']; ?>

          <td>
            <a  class="btn btn-<? if($_GET['formato']==$row_solicitud['idsolicitud_registro']){echo "success";}else{echo "primary";}?>" style="width:100%" href="?COM&detailCOM&query=<?php echo $_GET['query'];?>&formato=<?php echo $row_solicitud['idsolicitud_registro']; ?>"><?php echo date("d/m/Y",$fecha)." | ".$row_solicitud['abreviacion']; ?></a>
          </td>
        </tr>
        <?php } while ($row_solicitud = mysql_fetch_assoc($solicitud)); ?>
      </table>
      <?php
      mysql_free_result($opp);
      ?>

    <? }?>
<? }?>

</ul>


<?php
mysql_free_result($oc);
?>
