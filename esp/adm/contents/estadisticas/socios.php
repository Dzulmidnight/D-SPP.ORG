<?php 
require_once('../Connections/dspp.php');
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


/* MUESTRA LAS SOLICITUDES CON LOS OPPs SEPARADOS
SELECT opp.*, solicitud_certificacion.*, COUNT(solicitud_certificacion.idsolicitud_certificacion) AS "TOTAL_SOLICITUD" FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE opp.pais = "PerÃº" GROUP BY opp.idopp
*/

/*
SELECT opp.idopp, opp.pais, solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.idoc , solicitud_certificacion.status ,COUNT(solicitud_certificacion.idsolicitud_certificacion) AS "TOTAL_SOLICITUD" FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE opp.pais = "PerÃº"
opp.pais = 'PerÃº'

SELECT opp.idopp, opp.pais, opp.estatus_opp, opp.estatus_dspp, num_socios.idnum_socios, num_socios.idopp, num_socios.numero FROM num_socios INNER JOIN opp ON num_socios.idopp = opp.idopp WHERE opp.pais = 'PerÃº' AND (opp.estatus_opp != 'CANCELADO' OR opp.estatus_opp != 'ARCHIVADO' OR opp.estatus_opp IS NULL) GROUP BY num_socios.idopp*/
?>
<div class="col-md-12">
  <div class="row">
    <h4>Socios</h4>
    <p class="alert alert-info" style="padding:5px;">Nota: No se incluyen OPPs archivadas ni canceladas, asi como OPPs que no han ingresado su numero de socios.</p>
    <table class="table table-bordered table-hover ">
      <thead>
        <tr class="success">
          <th>País</th>
          <th>Num OPPs</th>
          <th>En Proceso</th>
          <th>Dictamen Positiva</th>
          <th>Certificado Emitido</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $num_opp = 0;
          $row_pais = mysql_query("SELECT opp.pais FROM opp GROUP BY opp.pais", $dspp) or die(mysql_error());
          while($pais = mysql_fetch_assoc($row_pais)){
            $row_socios = mysql_query("SELECT opp.idopp, opp.pais, opp.estatus_opp, opp.estatus_interno, opp.estatus_dspp, num_socios.idnum_socios, num_socios.idopp, num_socios.numero FROM num_socios INNER JOIN opp ON num_socios.idopp = opp.idopp WHERE (opp.pais = '$pais[pais]') AND ((opp.estatus_opp != 'CANCELADO') AND (opp.estatus_opp != 'ARCHIVADO') AND (opp.estatus_interno != 10) OR (opp.estatus_opp IS NULL)) GROUP BY num_socios.idopp", $dspp) or die(mysql_error());
            $num_opp = mysql_num_rows($row_socios);
        ?>
          <tr>
            <td><?php echo $pais['pais']; ?></td>
            <td><?php echo $num_opp; ?></td>
            <td>
              <?php 
              $row_productos = mysql_query("SELECT producto_general, producto FROM productos WHERE idopp IS NOT NULL GROUP BY producto", $dspp) or die(mysql_error());
              while($productos = mysql_fetch_assoc($row_productos)){
                echo $productos['producto_general'].' -- '.$productos['producto'].'<br/>';
              }
               ?>              
            </td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
        <?php

        }
         ?>
      </tbody>
    </table>
  </div>  
</div>
