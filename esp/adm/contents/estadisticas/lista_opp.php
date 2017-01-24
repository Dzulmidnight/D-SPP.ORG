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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
mysql_select_db($database_dspp, $dspp);

$row_opp = mysql_query("SELECT * FROM opp", $dspp) or die(mysql_error());


?>
<h4>Lista de Organizaciones de Pequeños Productores</h4>
<table class="table table-bordered table-hover" style="font-size:12px;">
    <thead>
      <form action="" method="post" id="orden" enctype="application/x-www-form-urlencoded">
            <th class="text-center">Nº</th>

            <th class="text-center" >
              #SPP<br>
              <button class="btn btn-xs btn-default" name="numero" value="idf" type="submit"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></button>
              <button class="btn btn-xs btn-default" name="numeroDesc" value="idf" type="submit"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button>
            </th>
            <th class="text-center" style="width:150px;">
              Nombre OPP<br>
              <button class="btn btn-xs btn-default" name="nombre" value="abreviacion" type="submit"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></button>
              <button class="btn btn-xs btn-default" name="nombreDesc" value="abreviacion" type="submit"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button>
            </th>
            <th class="text-center" style="width:150px;">
              Abreviación<br>
              <button class="btn btn-xs btn-default" name="abreviacion" value="abreviacion" type="submit"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></button>
              <button class="btn btn-xs btn-default" name="abreviacionDesc" value="abreviacion" type="submit"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button>
            </th>
            <th class="text-center">
              OC<br>
              <button class="btn btn-xs btn-default" name="pais" value="pais" type="submit"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></button>
              <button class="btn btn-xs btn-default" name="paisDesc" value="pais" type="submit"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button>
            </th>

            <th class="text-center">
              Pais<br>
              <button class="btn btn-xs btn-default" name="pais" value="pais" type="submit"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></button>
              <button class="btn btn-xs btn-default" name="paisDesc" value="pais" type="submit"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button>
            </th>
            <th style="width:130px;" class="text-center">
              Estatus Interno<br>
              <button class="btn btn-xs btn-default" name="estatus_interno" value="idstatus" type="submit"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></button>
              <button class="btn btn-xs btn-default" name="estatus_internoDesc" value="idstatus" type="submit"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button>
            </th>
            <th style="width:130px;" class="text-center">
              Estatus General<br>
              <button class="btn btn-xs btn-default" name="estatus_publico" value="idstatus_publico" type="submit"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></button>
              <button class="btn btn-xs btn-default" name="estatus_publicoDesc" value="idstatus_publico" type="submit"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button>
            </th>
            <th style="width:130px;" class="text-center">
              Vigencia Certificado<br>
              <button class="btn btn-xs btn-default" name="estatus_publico" value="idstatus_publico" type="submit"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></button>
              <button class="btn btn-xs btn-default" name="estatus_publicoDesc" value="idstatus_publico" type="submit"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button>
            </th>

            <th  class="text-center">
              PRODUCTOS
            </th>
            <th style="width:130px;" class="text-center">
              # PRODUCTORES<br>
              <button class="btn btn-xs btn-default" name="productores" value="resp1" type="submit"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></button>
              <button class="btn btn-xs btn-default" name="productoresDesc" value="resp1" type="submit"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></button>
            </th>
            <input type="hidden" name="orden" value="orden">
      </form>
    </thead>
    <?php 
      /*$contSolicitud = 0;
      $contProceso = 0;
      $contPositiva = 0;
      $contCertificada = 0;
      $contTotal = 0;*/
     ?>

    <tbody>
    <?php
    $contador = 1;
    while($opp = mysql_fetch_assoc($row_opp)){
    ?>
      <tr>
        <td><?php echo $contador; ?></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
    <?php
    $contador++;
    }
     ?>
    </tbody>
</table>