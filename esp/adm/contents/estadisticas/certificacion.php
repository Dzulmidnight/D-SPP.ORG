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
*/


?>
<div class="col-md-6">
  <div class="row">
    <div class="btn-group" role="group" aria-label="...">
      <button type="button" class="btn btn-default">OPP</button>
      <button type="button" class="btn btn-default">Empresas</button>
    </div>
    <h4>Solicitudes de Certificación</h4>
    <table class="table table-bordered table-hover ">
      <thead>
        <tr class="success">
          <th>País</th>
          <th>Solicitud</th>
          <th>En Proceso</th>
          <th>Dictamen Positiva</th>
          <th>Certificado Emitido</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $total_en_revision = 0;
          $total_en_proceso = 0;
          $total_ev_positiva = 0;
          $total_certificadas = 0;
          $row_pais = mysql_query("SELECT opp.pais FROM opp GROUP BY opp.pais ORDER BY opp.pais ASC", $dspp) or die(mysql_error());
          while($pais = mysql_fetch_assoc($row_pais)){
            $num_en_revision = 0;
            $num_en_proceso = 0;
            $num_ev_positiva = 0;
            $num_certificadas = 0;


            $query = "SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.tipo_solicitud, opp.idopp, opp.pais FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE opp.pais = '$pais[pais]'";
            $row_total_pais = mysql_query($query, $dspp) or die(mysql_error());

            $total = mysql_num_rows($row_total_pais);

            $row_en_revision = mysql_query("SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.estatus_interno, solicitud_certificacion.estatus_dspp, opp.pais FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE opp.pais = '$pais[pais]' AND solicitud_certificacion.estatus_dspp = 1", $dspp) or die(mysql_error());
            $num_en_revision = mysql_num_rows($row_en_revision);

            $row_en_proceso = mysql_query("SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.estatus_interno, solicitud_certificacion.estatus_dspp, opp.idopp, opp.pais FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE opp.pais = '$pais[pais]' AND (solicitud_certificacion.estatus_interno != 8 OR solicitud_certificacion.estatus_interno IS NULL) AND solicitud_certificacion.estatus_dspp != 1 AND solicitud_certificacion.estatus_dspp != 12", $dspp) or die(mysql_error());
            $num_en_proceso = mysql_num_rows($row_en_proceso);

            $row_ev_positiva = mysql_query("SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.estatus_interno, solicitud_certificacion.estatus_dspp, opp.pais FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE opp.pais = '$pais[pais]' AND (solicitud_certificacion.estatus_interno = 8 AND solicitud_certificacion.estatus_dspp != 12)", $dspp) or die(mysql_error());
            $num_ev_positiva = mysql_num_rows($row_ev_positiva);

            $row_certificadas = mysql_query("SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.estatus_interno, solicitud_certificacion.estatus_dspp, opp.pais FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE opp.pais = '$pais[pais]'  AND solicitud_certificacion.estatus_dspp = 12", $dspp) or die(mysql_error());
            $num_certificadas = mysql_num_rows($row_certificadas);

            $total_en_revision = $total_en_revision + $num_en_revision;
            $total_en_proceso = $total_en_proceso + $num_en_proceso;
            $total_ev_positiva = $total_ev_positiva + $num_ev_positiva;
            $total_certificadas = $total_certificadas + $num_certificadas;
            $total_final = $total_en_revision + $total_en_proceso + $total_ev_positiva + $total_certificadas;
        ?>
          <tr>
            <td><?php echo $pais['pais']; ?></td>
            <!--numero de solicitudes en revision-->
            <td><?php echo $num_en_revision; ?></td>
            <!--numero de solicitudes en proceso-->
            <td><?php echo $num_en_proceso; ?></td>
            <!--numero de solicitudes con evaluacion positiva-->
            <td><?php echo $num_ev_positiva; ?></td>
            <!--numero de solicitudes con certificado emitido-->
            <td class="success"><?php echo $num_certificadas; ?></td>
            <!--numero total de solicitudes-->
            <td><?php echo $total; ?></td>
          </tr>
        <?php

        }
         ?>
         <tr style="color:red">
           <td>TOTAL</td>
           <td><?php echo $total_en_revision; ?></td>
           <td><?php echo $total_en_proceso; ?></td>
           <td><?php echo $total_ev_positiva; ?></td>
           <td><?php echo $total_certificadas; ?></td>
           <td><?php echo $total_final; ?></td>
         </tr>
      </tbody>
    </table>
  </div>  
</div>

<div class="col-md-6">
  <div class="row">
    <div class="btn-group" role="group" aria-label="...">
      <button type="button" class="btn btn-default">OPP</button>
      <button type="button" class="btn btn-default">Empresas</button>
    </div>
    <h4>Solicitudes de Registro</h4>
    <table class="table table-bordered table-hover ">
      <thead>
        <tr class="warning">
          <th>País</th>
          <th>Solicitud</th>
          <th>En Proceso</th>
          <th>Dictamen Positiva</th>
          <th>Certificado Emitido</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $total_en_revision = 0;
          $total_en_proceso = 0;
          $total_ev_positiva = 0;
          $total_certificadas = 0;
          $row_pais = mysql_query("SELECT empresa.pais FROM empresa GROUP BY empresa.pais ORDER BY empresa.pais ASC", $dspp) or die(mysql_error());
          while($pais = mysql_fetch_assoc($row_pais)){
            $num_en_revision = 0;
            $num_en_proceso = 0;
            $num_ev_positiva = 0;
            $num_certificadas = 0;


            $query = "SELECT solicitud_registro.idsolicitud_registro, solicitud_registro.idempresa, solicitud_registro.tipo_solicitud, empresa.idempresa, empresa.pais FROM solicitud_registro INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa WHERE empresa.pais = '$pais[pais]'";
            $row_total_pais = mysql_query($query, $dspp) or die(mysql_error());

            $total = mysql_num_rows($row_total_pais);

            $row_en_revision = mysql_query("SELECT solicitud_registro.idsolicitud_registro, solicitud_registro.idempresa, solicitud_registro.estatus_interno, solicitud_registro.estatus_dspp, empresa.pais FROM solicitud_registro INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa WHERE empresa.pais = '$pais[pais]' AND solicitud_registro.estatus_dspp = 1", $dspp) or die(mysql_error());
            $num_en_revision = mysql_num_rows($row_en_revision);

            $row_en_proceso = mysql_query("SELECT solicitud_registro.idsolicitud_registro, solicitud_registro.idempresa, solicitud_registro.estatus_interno, solicitud_registro.estatus_dspp, empresa.idempresa, empresa.pais FROM solicitud_registro INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa WHERE empresa.pais = '$pais[pais]' AND (solicitud_registro.estatus_interno != 8 OR solicitud_registro.estatus_interno IS NULL) AND solicitud_registro.estatus_dspp != 1 AND solicitud_registro.estatus_dspp != 12", $dspp) or die(mysql_error());
            $num_en_proceso = mysql_num_rows($row_en_proceso);

            $row_ev_positiva = mysql_query("SELECT solicitud_registro.idsolicitud_registro, solicitud_registro.idempresa, solicitud_registro.estatus_interno, solicitud_registro.estatus_dspp, empresa.pais FROM solicitud_registro INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa WHERE empresa.pais = '$pais[pais]' AND (solicitud_registro.estatus_interno = 8 AND solicitud_registro.estatus_dspp != 12)", $dspp) or die(mysql_error());
            $num_ev_positiva = mysql_num_rows($row_ev_positiva);

            $row_certificadas = mysql_query("SELECT solicitud_registro.idsolicitud_registro, solicitud_registro.idempresa, solicitud_registro.estatus_interno, solicitud_registro.estatus_dspp, empresa.pais FROM solicitud_registro INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa WHERE empresa.pais = '$pais[pais]'  AND solicitud_registro.estatus_dspp = 12", $dspp) or die(mysql_error());
            $num_certificadas = mysql_num_rows($row_certificadas);

            $total_en_revision = $total_en_revision + $num_en_revision;
            $total_en_proceso = $total_en_proceso + $num_en_proceso;
            $total_ev_positiva = $total_ev_positiva + $num_ev_positiva;
            $total_certificadas = $total_certificadas + $num_certificadas;
            $total_final = $total_en_revision + $total_en_proceso + $total_ev_positiva + $total_certificadas;
        ?>
          <tr>
            <td><?php echo $pais['pais']; ?></td>
            <!--numero de solicitudes en revision-->
            <td><?php echo $num_en_revision; ?></td>
            <!--numero de solicitudes en proceso-->
            <td><?php echo $num_en_proceso; ?></td>
            <!--numero de solicitudes con evaluacion positiva-->
            <td><?php echo $num_ev_positiva; ?></td>
            <!--numero de solicitudes con certificado emitido-->
            <td class="success"><?php echo $num_certificadas; ?></td>
            <!--numero total de solicitudes-->
            <td><?php echo $total; ?></td>
          </tr>
        <?php

        }
         ?>
         <tr style="color:red">
           <td>TOTAL</td>
           <td><?php echo $total_en_revision; ?></td>
           <td><?php echo $total_en_proceso; ?></td>
           <td><?php echo $total_ev_positiva; ?></td>
           <td><?php echo $total_certificadas; ?></td>
           <td><?php echo $total_final; ?></td>
         </tr>
      </tbody>
    </table>
  </div>  
</div>