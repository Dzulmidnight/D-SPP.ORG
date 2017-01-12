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


/* MUESTRA LAS SOLICITUDES CON LOS OPP SEPARADOS
SELECT opp.*, solicitud_certificacion.*, COUNT(solicitud_certificacion.idsolicitud_certificacion) AS "TOTAL_SOLICITUD" FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE opp.pais = "PerÃº" GROUP BY opp.idopp
*/

/*
SELECT opp.idopp, opp.pais, solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.idoc , solicitud_certificacion.status ,COUNT(solicitud_certificacion.idsolicitud_certificacion) AS "TOTAL_SOLICITUD" FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE opp.pais = "PerÃº"
*/


?>
<div class="row">
  <div class="btn-group" role="group" aria-label="...">
    <button type="button" class="btn btn-default">OPP</button>
    <button type="button" class="btn btn-default">Empresas</button>
  </div>

  <table class="table table-bordered ">
    <thead>
      <tr class="success">
        <th>País</th>
        <th>Solicitud</th>
        <th>En Proceso</th>
        <th>Evaluación Positiva</th>
        <th>Certificada</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      <?php


      $row_pais = mysql_query("SELECT opp.pais FROM opp GROUP BY opp.pais ORDER BY opp.pais ASC", $dspp) or die(mysql_error());
      while($pais = mysql_fetch_assoc($row_pais)){
        //consultamos el total de solicitud por pais
        $query = "SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.tipo_solicitud, opp.idopp, opp.pais FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE opp.pais = '$pais[pais]'";
        $row_total_pais = mysql_query($query, $dspp) or die(mysql_error());
        $total = mysql_num_rows($row_total_pais);


        $num_en_proceso = 0;
        $num_positiva = 0;
        $num_certificada = 0;

        //solicitudes con evaluacion positiva
        $estatus_interno = 8;//dictamen positivo
        $row_positiva = mysql_query("SELECT proceso_certificacion.idsolicitud_certificacion, MAX(proceso_certificacion.fecha_registro) AS 'registro', solicitud_certificacion.idopp, opp.pais FROM proceso_certificacion INNER JOIN solicitud_certificacion ON proceso_certificacion.idsolicitud_certificacion = proceso_certificacion.idsolicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE estatus_interno = $estatus_interno  AND proceso_certificacion.idsolicitud_certificacion IS NOT NULL GROUP BY idsolicitud_certificacion ORDER BY registro DESC", $dspp) or die(mysql_error());
        $num_positiva = mysql_num_rows($row_positiva);

        
      ?>
        <tr>
          <td><?php echo $pais['pais']; ?></td>
          <td>
            0
          </td>

          <td>
            
          </td>
          <td></td>
          <td></td>
          <td><?php echo $total; ?></td>
        </tr>
      <?php
      }
       ?>
    </tbody>
  </table>
</div>
