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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO opp (idf, password, nombre, abreviacion, sitio_web, telefono, email, pais, idoc, razon_social, direccion_fiscal, rfc) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['idf'], "text"),
                       GetSQLValueString($_POST['password'], "text"),
                       GetSQLValueString($_POST['nombre'], "text"),
                       GetSQLValueString($_POST['abreviacion'], "text"),
                       GetSQLValueString($_POST['sitio_web'], "text"),
                       GetSQLValueString($_POST['telefono'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['pais'], "text"),
                       GetSQLValueString($_POST['idoc'], "int"),
                       GetSQLValueString($_POST['razon_social'], "text"),
                       GetSQLValueString($_POST['direccion_fiscal'], "text"),
                       GetSQLValueString($_POST['rfc'], "text"));

  mysql_select_db($database_dspp, $dspp);
  $Result1 = mysql_query($insertSQL, $dspp) or die(mysql_error());

  $insertGoTo = "main_menu.php?OPP&add&mensaje=OPP agregado correctamente";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_dspp, $dspp);
$query_pais = "SELECT nombre FROM paises ORDER BY nombre ASC";
$pais = mysql_query($query_pais, $dspp) or die(mysql_error());
$row_pais = mysql_fetch_assoc($pais);
$totalRows_pais = mysql_num_rows($pais);

mysql_select_db($database_dspp, $dspp);
$query_oc = "SELECT idoc, idf, abreviacion, pais FROM oc ORDER BY nombre ASC";
$oc = mysql_query($query_oc, $dspp) or die(mysql_error());
$row_oc = mysql_fetch_assoc($oc);
$totalRows_oc = mysql_num_rows($oc);

/* MUESTRA LAS SOLICITUDES CON LOS OPP SEPARADOS
SELECT opp.*, solicitud_certificacion.*, COUNT(solicitud_certificacion.idsolicitud_certificacion) AS "TOTAL_SOLICITUD" FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE opp.pais = "PerÃº" GROUP BY opp.idopp
*/

/*
SELECT opp.idopp, opp.pais, solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.idoc , solicitud_certificacion.status ,COUNT(solicitud_certificacion.idsolicitud_certificacion) AS "TOTAL_SOLICITUD" FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE opp.pais = "PerÃº"
*/


$query_anual = "SELECT fecha_elaboracion, FROM_UNIXTIME(solicitud_certificacion.fecha_elaboracion, '%Y') AS 'estadistica_anual' FROM solicitud_certificacion GROUP BY FROM_UNIXTIME(solicitud_certificacion.fecha_elaboracion, '%Y' )";
$ejecutar3 = mysql_query($query_anual,$dspp) or die(mysql_error());


?>

<?php 
while ($contar_anios = mysql_fetch_assoc($ejecutar3)) { 

  $query_opp = "SELECT * FROM opp GROUP BY opp.pais";
$opp = mysql_query($query_opp,$dspp);
$row_opp = mysql_fetch_assoc($opp);
?>
<hr>
<h4 class="text-center">ESTADISTICAS ANUALES (<?php echo $contar_anios['estadistica_anual']; ?>)</h4>
<table class="table table-bordered table-hover">
  <thead>
    <th class="text-center">País</th>
    <th style="width:130px;" class="text-center">Solicitudes</th>
    <th style="width:130px;" class="text-center">En proceso</th>
    <th style="width:130px;" class="text-center">Evalación Positiva</th>
    <th style="width:130px;" class="text-center">Certificada</th>
    <th style="width:130px;" class="text-center">Total</th>

  </thead>
  <?php 
    $contSolicitud = 0;
    $contProceso = 0;
    $contPositiva = 0;
    $contCertificada = 0;
    $contTotal = 0;
   ?>

  <tbody>
    <?php while($row_opp = mysql_fetch_assoc($opp)){ ?>
    <tr>
      <td><?php echo $row_opp['pais']; ?></td>

      <!--------------------------- INICIO  SOLICITUDES ---------------------------------------->
      <td class="text-center">
        <?php 
          //$query = "SELECT opp.idopp, opp.pais, solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.idoc , solicitud_certificacion.status ,COUNT(solicitud_certificacion.idsolicitud_certificacion) AS 'TOTAL_SOLICITUD' FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE FROM_UNIXTIME(solicitud_certificacion.fecha_elaboracion, '%Y' ) = '$contar_anios[estadistica_anual]' AND opp.pais = '$row_opp[pais]' AND (solicitud_certificacion.status = 1 OR solicitud_certificacion.status = 2 OR solicitud_certificacion.status = 17)";
        $query = "SELECT solicitud_certificacion.*, opp.pais FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE FROM_UNIXTIME(solicitud_certificacion.fecha_elaboracion,'%Y') = '$contar_anios[estadistica_anual]' AND opp.pais = '$row_opp[pais]' AND (solicitud_certificacion.status = 1 OR solicitud_certificacion.status = 2 OR solicitud_certificacion.status = 17)";
          $registro = mysql_query($query,$dspp);
         // $solicitud = mysql_fetch_assoc($registro);
          $total = mysql_num_rows($registro);
          echo $total;
         ?>
      </td>
      <!--------------------------- FIN  SOLICITUDES ---------------------------------------->

      <!--------------------------- INICIO SOLICITUDES EN PROCESO ---------------------------------------->
      <td class="text-center">
        <?php 
        
          //$query = "SELECT opp.idopp, opp.pais, solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.idoc , solicitud_certificacion.status ,COUNT(solicitud_certificacion.idsolicitud_certificacion) AS 'TOTAL_SOLICITUD' FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE FROM_UNIXTIME(solicitud_certificacion.fecha_elaboracion, '%Y' ) = '$contar_anios[estadistica_anual]' AND opp.pais = '$row_opp[pais]' AND (solicitud_certificacion.status = 18  OR solicitud_certificacion.status = 19)";
          $query = "SELECT opp.idopp, opp.pais, solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.idoc , solicitud_certificacion.status ,COUNT(solicitud_certificacion.idsolicitud_certificacion) AS 'TOTAL_SOLICITUD' FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE FROM_UNIXTIME(solicitud_certificacion.fecha_elaboracion, '%Y' ) = '$contar_anios[estadistica_anual]' AND opp.pais = '$row_opp[pais]' AND (solicitud_certificacion.status != 1 AND solicitud_certificacion.status != 2 AND solicitud_certificacion.status != 8 AND solicitud_certificacion.status != 17 AND solicitud_certificacion.status != 10)";

          $registro = mysql_query($query,$dspp);
          $solicitud = mysql_fetch_assoc($registro);

          echo $solicitud['TOTAL_SOLICITUD'];
         ?>        
      </td>
      <!--------------------------- FIN SOLICITUDES EN PROCESO ---------------------------------------->

      <!--------------------------- INICIO SOLICITUDES EVALUACION POSTIVA ---------------------------------------->
      <td class="text-center">
        <?php 
        
          $query = "SELECT opp.idopp, opp.pais, solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.idoc , solicitud_certificacion.status ,COUNT(solicitud_certificacion.idsolicitud_certificacion) AS 'TOTAL_SOLICITUD' FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE FROM_UNIXTIME(solicitud_certificacion.fecha_elaboracion, '%Y' ) = '$contar_anios[estadistica_anual]' AND opp.pais = '$row_opp[pais]' AND solicitud_certificacion.status = 8";
          $registro = mysql_query($query,$dspp);
          $solicitud = mysql_fetch_assoc($registro);

          echo $solicitud['TOTAL_SOLICITUD'];
         ?>        
      </td>
      <!--------------------------- FIN SOLICITUDES EVALUACION POSTIVA ---------------------------------------->

      <!--------------------------- INICIO SOLICITUDES CERTIFICADA ---------------------------------------->
      <td class="text-center">
        <?php 
        
          $query = "SELECT opp.idopp, opp.pais, solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.idoc , solicitud_certificacion.status ,COUNT(solicitud_certificacion.idsolicitud_certificacion) AS 'TOTAL_SOLICITUD' FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE FROM_UNIXTIME(solicitud_certificacion.fecha_elaboracion, '%Y' ) = '$contar_anios[estadistica_anual]' AND opp.pais = '$row_opp[pais]' AND solicitud_certificacion.status = 10";
          $registro = mysql_query($query,$dspp);
          $solicitud = mysql_fetch_assoc($registro);

          echo $solicitud['TOTAL_SOLICITUD'];
         ?>        
      </td>
      <!--------------------------- FIN SOLICITUDES CERTIFICADA ---------------------------------------->

      <!--------------------------- INICIO TOTAL SOLICITUDES ---------------------------------------->
      <td class="text-center">
        <?php 
        
          $query = "SELECT opp.idopp, opp.pais, solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.idoc , solicitud_certificacion.status ,COUNT(solicitud_certificacion.idsolicitud_certificacion) AS 'TOTAL_SOLICITUD' FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE FROM_UNIXTIME(solicitud_certificacion.fecha_elaboracion, '%Y' ) = '$contar_anios[estadistica_anual]' AND opp.pais = '$row_opp[pais]'";
          $registro = mysql_query($query,$dspp);
          $solicitud = mysql_fetch_assoc($registro);

          echo $solicitud['TOTAL_SOLICITUD'];
         ?>        
      </td>
      <!--------------------------- FIN TOTAL SOLICITUDES ---------------------------------------->


    </tr>
    <?php } ?>
    <tr>
      <td>TOTALES</td>
      <td class="text-center">
        <?php 
          //$query = "SELECT opp.idopp, opp.pais, solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.idoc , solicitud_certificacion.status ,COUNT(solicitud_certificacion.idsolicitud_certificacion) AS 'TOTAL_SOLICITUD2' FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE  FROM_UNIXTIME(solicitud_certificacion.fecha_elaboracion, '%Y' ) = '$contar_anios[estadistica_anual]' AND solicitud_certificacion.status = 1 OR solicitud_certificacion.status = 2 OR solicitud_certificacion.status = 17";

         // $query = "SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.idoc , solicitud_certificacion.status ,COUNT(solicitud_certificacion.idsolicitud_certificacion) AS 'TOTAL_SOLICITUD' FROM solicitud_certificacion  WHERE  FROM_UNIXTIME(solicitud_certificacion.fecha_elaboracion, '%Y' ) = '$contar_anios[estadistica_anual]' and solicitud_certificacion.status = 1 OR solicitud_certificacion.status = 2 OR solicitud_certificacion.status = 17";

          $query = "SELECT solicitud_certificacion.*  FROM solicitud_certificacion WHERE  FROM_UNIXTIME(solicitud_certificacion.fecha_elaboracion, '%Y' ) = '$contar_anios[estadistica_anual]' AND (solicitud_certificacion.status = 1 OR solicitud_certificacion.status = 2 OR solicitud_certificacion.status = 17)";

          $registro = mysql_query($query,$dspp);
          $total = mysql_num_rows($registro);
          //$solicitud = mysql_fetch_assoc($registro);

          echo $total;
         ?>

      </td>
      <td class="text-center">
        <?php 
          //$query = "SELECT solicitud_certificacion.* FROM solicitud_certificacion WHERE FROM_UNIXTIME(solicitud_certificacion.fecha_elaboracion, '%Y' ) = '$contar_anios[estadistica_anual]' AND (solicitud_certificacion.status = 18 OR solicitud_certificacion.status = 19)";        
          $query = "SELECT solicitud_certificacion.* FROM solicitud_certificacion WHERE FROM_UNIXTIME(solicitud_certificacion.fecha_elaboracion, '%Y' ) = '$contar_anios[estadistica_anual]' AND (solicitud_certificacion.status != 1 AND solicitud_certificacion.status != 2 AND solicitud_certificacion.status != 8 AND solicitud_certificacion.status != 17 AND solicitud_certificacion.status != 10)";
          $registro = mysql_query($query,$dspp);
          $total = mysql_num_rows($registro);

          echo $total;
         ?>  
      </td>
      <td class="text-center">
        <?php 
          //$query = "SELECT opp.idopp, opp.pais, solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.idoc , solicitud_certificacion.status ,COUNT(solicitud_certificacion.idsolicitud_certificacion) AS 'TOTAL_SOLICITUD' FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE FROM_UNIXTIME(solicitud_certificacion.fecha_elaboracion, '%Y' ) = '$contar_anios[estadistica_anual]' AND solicitud_certificacion.status = 8";

          $query = "SELECT solicitud_certificacion.* FROM solicitud_certificacion  WHERE FROM_UNIXTIME(solicitud_certificacion.fecha_elaboracion, '%Y' ) = '$contar_anios[estadistica_anual]' AND solicitud_certificacion.status = 8";
          $registro = mysql_query($query,$dspp);
          $total = mysql_num_rows($registro);
          //$solicitud = mysql_fetch_assoc($registro);

          echo $total;
         ?>      
      </td>
      <td class="text-center">
        <?php 
          //$query = "SELECT opp.idopp, opp.pais, solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.idoc , solicitud_certificacion.status ,COUNT(solicitud_certificacion.idsolicitud_certificacion) AS 'TOTAL_SOLICITUD' FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE FROM_UNIXTIME(solicitud_certificacion.fecha_elaboracion, '%Y' ) = '$contar_anios[estadistica_anual]' AND solicitud_certificacion.status = 10";        
          $query = "SELECT solicitud_certificacion.* FROM solicitud_certificacion WHERE FROM_UNIXTIME(solicitud_certificacion.fecha_elaboracion, '%Y' ) = '$contar_anios[estadistica_anual]' AND solicitud_certificacion.status = 10";
          $registro = mysql_query($query,$dspp);
          $total = mysql_num_rows($registro);
          //$solicitud = mysql_fetch_assoc($registro);

          echo $total;
         ?>  
      </td>
      <td class="text-center">
        <?php 

          //$query = "SELECT opp.idopp, opp.pais, solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.idoc , solicitud_certificacion.status ,COUNT(solicitud_certificacion.idsolicitud_certificacion) AS 'TOTAL_SOLICITUD' FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE FROM_UNIXTIME(solicitud_certificacion.fecha_elaboracion, '%Y' ) = '$contar_anios[estadistica_anual]'";        
          $query = "SELECT solicitud_certificacion.* FROM solicitud_certificacion WHERE FROM_UNIXTIME(solicitud_certificacion.fecha_elaboracion, '%Y' ) = '$contar_anios[estadistica_anual]'";
          $registro = mysql_query($query,$dspp);
          $total = mysql_num_rows($registro);
          //$solicitud = mysql_fetch_assoc($registro);

          echo $total;
         ?>   
      </td>
    </tr>
  </tbody>
</table>


<?
}


 ?>

