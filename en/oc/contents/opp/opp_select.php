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
	$query_opp = "SELECT *, opp.nombre AS 'nombreOPP', status.idstatus, status.nombre AS 'nombreStatus' FROM opp LEFT JOIN status ON opp.estado = status.idstatus where idoc='".$_SESSION['idoc']."' ORDER BY opp.nombre ASC";
}else{
	$query_opp = "SELECT *, opp.nombre AS 'nombreOPP', status.idstatus, status.nombre AS 'nombreStatus' FROM opp LEFT JOIN status ON opp.estado = status.idstatus where idoc='".$_SESSION['idoc']."' ORDER BY opp.nombre ASC";
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
<script language="JavaScript"> 
function preguntar(){ 
    if(!confirm('¿Estas seguro de eliminar el registro?')){ 
       return false; } 
} 
</script>
<div class="panel panel-default">
  <div class="panel-heading">Lista OPP(s)</div>
  <div class="panel-body">
    <table class="table table-bordered table-hover" style="font-size:12px;">
      <thead>
        <tr>
          <th class="text-center">IDF</th>
          <th class="text-center">Estatus Certificado</th>
          <th class="text-center">Nombre</th>
          <!--<th class="text-center">Abreviación</th>-->
          <th class="text-center">Información</th>
          <!--<th class="text-center">Email</th>
          <th class="text-center">Teléfono Oficinas</th>
          <th class="text-center">País</th>-->
          <!--<th class="text-center">OC</th>-->
          <!--<th class="text-center">Razón social</th>-->
          <th class="text-center">Dirección Oficinas</th>
          <!--<th class="text-center">Dirección fiscal</th>-->
          <!--<th class="text-center">RFC</th>-->
          <th class="text-center">Eliminar</th>
        </tr>
      </thead>
      <tbody>
        <?php $cont=0; while ($row_opp = mysql_fetch_assoc($opp)) {$cont++; ?>
          <tr class="text-justify">
            <td>
              <a class="btn btn-sm btn-primary" href="?OPP&amp;detail&amp;idopp=<?php echo $row_opp['idopp']; ?>&contact">Consultar<br><?php echo $row_opp['idf']; ?></a>
            </td>
            <td style="width:150px;">
              <?php 
                if(isset($row_opp['nombreStatus'])){
                  if($row_opp['estado'] == 10){
                    echo "<p class='informacion text-center alert alert-success' style='padding:7px;'>$row_opp[nombreStatus]</p>";
                  }
                  if($row_opp['estado'] == 11){
                    echo "<p class='informacion text-center alert alert-warning' style='padding:7px;'>$row_opp[nombreStatus]</p>";
                  }
                  if($row_opp['estado'] == 16){
                    echo "<p class='informacion text-center alert alert-danger' style='padding:7px;'>$row_opp[nombreStatus]</p>";
                  }
                }else{
                  echo "<p class='text-center'>No Disponible</p>";
                }
               ?>
            </td>
            <td>
              <?php echo $row_opp['nombreOPP'].", <u>".$row_opp['abreviacion']."</u>"; ?>
            </td>
            <!--<td>
              <?php echo $row_opp['abreviacion']; ?>
            </td>-->
            <td>
              <?php echo "<u>".$row_opp['sitio_web']."</u><br><u>".$row_opp['email']."</u><br><u>".$row_opp['telefono']."</u><br>".$row_opp['pais']; ?>
            </td>
            <!--<td><?php echo $row_opp['telefono']; ?></td>
            <td><?php echo $row_opp['email']; ?></td>
            <td><?php echo $row_opp['pais']; ?></td>-->


            <!--<td><?php echo $row_opp['razon_social']; ?></td>-->
            <td><?php echo $row_opp['direccion']; ?></td>
            <!--<td><?php echo $row_opp['direccion_fiscal']; ?></td>
            <td><?php echo $row_opp['rfc']; ?></td>-->
            <td>
              <a href="?OPP&detail&idopp=<?php echo $row_opp['idopp']; ?>&contact" class="btn btn-xs btn-info col-xs-6" data-toggle="tooltip" data-placement="top" title="Editar">
                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
              </a>

              <form action="" method="post" name="formularioEliminar" ONSUBMIT="return preguntar();">
                <button class="btn btn-xs btn-danger col-xs-6" type="subtmit" value="Eliminar" data-toggle="tooltip" data-placement="top" title="Eliminar">
                  <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                </button>        
                <input type="hidden" value="OPP eliminado correctamente" name="mensaje" />
                <input type="hidden" value="1" name="opp_delete" />
                <input type="hidden" value="<?php echo $row_opp['idopp']; ?>" name="idopp" />
              </form>

              <!--<form action="" method="post">
              <button class="btn btn-danger" type="submit" value="Eliminar">
                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Eliminar
              </button>
              
              <input type="hidden" value="OPP eliminado correctamente" name="mensaje" />
              <input type="hidden" value="1" name="opp_delete" />
              <input type="hidden" value="<?php echo $row_opp['idopp']; ?>" name="idopp" />
              </form>-->
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
