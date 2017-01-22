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
<style>
  .td_dato{
    background-color: #2ecc71;
    color: #ecf0f1;
    text-align: center;
  }
  .td_total{
    background-color:#e74c3c;
    color:#ecf0f1;
    text-align: center;
  }
</style>
<div class="col-md-12">
  <div class="row">

  <?php
  $td_anios = '';
  $arreglo_anios = array();
  $row_anios = mysql_query("SELECT FROM_UNIXTIME(solicitud_certificacion.fecha_registro, '%Y') AS 'anio_solicitud' FROM solicitud_certificacion GROUP BY anio_solicitud ORDER BY anio_solicitud ASC", $dspp) or die(mysql_error());
  $num_td = mysql_num_rows($row_anios);

  while($anio_solicitud = mysql_fetch_assoc($row_anios)){
    $arreglo_anios[] = $anio_solicitud['anio_solicitud'];
    $td_anios .= "<td style='font-size:10px;text-align:center'>$anio_solicitud[anio_solicitud]</td>";
  }

   ?>

    <h4>Solicitudes de Certificación</h4>
    <table class="table table-bordered table-hover table-condensed">
      <thead>
        <tr class="success">
          <th class="text-center" rowspan="2">País</th>
          <th class="text-center">Solicitud Inicial(<small>Son OPP que han ingresado por primera vez y que han o no han cargado su primera solcitud</small>)</th>
          <th class="text-center">Solicitud(<small>Son OPP nuevas que han ingresado su solicitud</small>)</th>
          <th class="text-center">En Proceso(<small>OPPs que han aceptado la cotización y ha iniciado su proceso de certificacion</small>)</th>
          <th class="text-center">Evaluación Positiva(<small>OPPs que han finalizado el proceso de certificación con una evaluación positiva</small>)</th>
          <th class="text-center">Subtotal Proceso</th>
          <th class="text-center">Certificada(<small>Se incluyen todas las OPPs que se les ha entragado certificado, ya sean nuevas o renovación</small>)</th>
          <th class="text-center">Inactiva</th>
          <th class="text-center">Suspendida(<small>OPPs que han sido formalmente suspendidas</small>)</th>
          <th class="text-center">Expirado(OPPs, que ha expirado las fechas de sus certificados)</th>
          <th class="text-center">Subtotal Certificación</th>
          <th class="text-center">Total</th>
          <!--<th class="text-center" style="background-color:#e74c3c;color:#ecf0f1" colspan="3">Total</th>-->
        </tr>
        <tr>
          <?php 
            echo $td_anios;
            echo $td_anios;
            echo $td_anios;
            echo $td_anios;
          ?>
          <td style="background-color:#e74c3c;color:#ecf0f1;font-size:10px;text-align:center">NUEVAS</td>
          <td style="background-color:#e74c3c;color:#ecf0f1;font-size:10px;text-align:center">RENOVACIÓN</td>
          <td style="background-color:#e74c3c;color:#ecf0f1;font-size:10px;text-align:center">TOTAL</td>
        </tr>

      </thead>
      <tbody>
        <?php
          $total_en_revision = 0;
          $total_en_proceso = 0;
          $total_ev_positiva = 0;
          $total_certificadas = 0;
          $total_final = 0;
          $total_final_nuevas = 0;
          $total_final_renovacion = 0;
          $row_pais = mysql_query("SELECT * FROM vw_paises", $dspp) or die(mysql_error());
          while($pais = mysql_fetch_assoc($row_pais)){
            $num_en_revision = 0;
            $num_en_proceso = 0;
            $num_ev_positiva = 0;
            $num_certificadas = 0;

        ?>
          <tr>
            <td><?php echo $pais['pais']; ?></td>
            <!--numero de solicitudes en revision-->
            <?php
            /////// SOLICITUDES EN REVISION
            for ($i=0; $i < count($arreglo_anios); $i++) { 
              $row_en_revision = mysql_query("SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.estatus_interno, solicitud_certificacion.estatus_dspp, opp.pais FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE opp.pais = '$pais[pais]' AND solicitud_certificacion.estatus_dspp = 1 AND FROM_UNIXTIME(solicitud_certificacion.fecha_registro, '%Y') = $arreglo_anios[$i]", $dspp) or die(mysql_error());
              $num_en_revision = mysql_num_rows($row_en_revision);
              if($num_en_revision > 0){
                echo "<td class='td_dato'>$num_en_revision</td>";
              }else{
                echo "<td>-</td>";
              }

            }
            ////// SOLICITUDES EN PROCESO
            for ($i=0; $i < count($arreglo_anios); $i++) { 
              $row_en_proceso = mysql_query("SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.estatus_interno, solicitud_certificacion.estatus_dspp, opp.idopp, opp.pais FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE opp.pais = '$pais[pais]' AND (solicitud_certificacion.estatus_interno != 8 OR solicitud_certificacion.estatus_interno IS NULL) AND solicitud_certificacion.estatus_dspp != 1 AND solicitud_certificacion.estatus_dspp != 12 AND FROM_UNIXTIME(solicitud_certificacion.fecha_registro, '%Y') = $arreglo_anios[$i]", $dspp) or die(mysql_error());
              $num_en_proceso = mysql_num_rows($row_en_proceso);
              if($num_en_proceso > 0){
                echo "<td class='td_dato'>$num_en_proceso</td>";
              }else{
                echo "<td>-</td>";
              }

            }
            /////// SOLICITUDES CON EVALUACION POSITIVA
            for ($i=0; $i < count($arreglo_anios); $i++) { 
              $row_ev_positiva = mysql_query("SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.estatus_interno, solicitud_certificacion.estatus_dspp, opp.pais FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE opp.pais = '$pais[pais]' AND (solicitud_certificacion.estatus_interno = 8 AND solicitud_certificacion.estatus_dspp != 12) AND FROM_UNIXTIME(solicitud_certificacion.fecha_registro, '%Y') = $arreglo_anios[$i]", $dspp) or die(mysql_error());
              $num_ev_positiva = mysql_num_rows($row_ev_positiva);
              if($num_ev_positiva > 0){
                echo "<td class='td_dato'>$num_ev_positiva</td>";
              }else{
                echo "<td>-</td>";
              }

            }
            ////// SOLICITUDES CON CERTIFICADO EMITIDO
            for ($i=0; $i < count($arreglo_anios); $i++) { 
              $row_certificadas = mysql_query("SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.estatus_interno, solicitud_certificacion.estatus_dspp, opp.pais FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE opp.pais = '$pais[pais]'  AND solicitud_certificacion.estatus_dspp = 12 AND FROM_UNIXTIME(solicitud_certificacion.fecha_registro, '%Y') = $arreglo_anios[$i]", $dspp) or die(mysql_error());
              $num_certificadas = mysql_num_rows($row_certificadas);
              if($num_certificadas > 0){
                echo "<td class='td_dato'>$num_certificadas</td>";
              }else{
                echo "<td>-</td>";
              }

            }
            $query = "SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.tipo_solicitud, FROM_UNIXTIME(solicitud_certificacion.fecha_registro, '%Y') AS 'anio_solicitud', opp.idopp, opp.pais FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE opp.pais = '$pais[pais]' AND solicitud_certificacion.tipo_solicitud = 'NUEVA' ORDER BY anio_solicitud DESC";
            $row_total_pais = mysql_query($query, $dspp) or die(mysql_error());

            $total_nueva = mysql_num_rows($row_total_pais);

            $query = "SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.tipo_solicitud, FROM_UNIXTIME(solicitud_certificacion.fecha_registro, '%Y') AS 'anio_solicitud', opp.idopp, opp.pais FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE opp.pais = '$pais[pais]' AND solicitud_certificacion.tipo_solicitud = 'RENOVACION' ORDER BY anio_solicitud DESC";
            $row_total_pais = mysql_query($query, $dspp) or die(mysql_error());

            $total_renovacion = mysql_num_rows($row_total_pais);
            $total = $total_nueva + $total_renovacion;

            $total_final_nuevas += $total_nueva;
            $total_final_renovacion += $total_renovacion;
             ?>

            <!--numero total de solicitudes-->
            <td class="td_total">
              <?php
              if($total_nueva > 0){
                echo $total_nueva; 
              }else{
                echo '-';
              } 
              ?>
            </td>
            <td class="td_total">
              <?php 
              if($total_renovacion > 0){
                echo $total_renovacion;
              }else{
                echo '-';
              }
              ?>
            </td>
            <td>
              <?php 
              if($total > 0){
                echo $total;
              }else{
                echo '-';
              }
              ?>
            </td>
          </tr>
        <?php

        }
         ?>
         <tr style="color:red">
           <td>TOTAL</td>
           <?php 
           /// TOTAL SOLICITUDES EN REVISION
            for ($i=0; $i < count($arreglo_anios); $i++) { 
                $row_en_revision = mysql_query("SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.estatus_interno, solicitud_certificacion.estatus_dspp FROM solicitud_certificacion WHERE solicitud_certificacion.estatus_dspp = 1 AND FROM_UNIXTIME(solicitud_certificacion.fecha_registro, '%Y') = $arreglo_anios[$i]", $dspp) or die(mysql_error());
                $total_en_revision = mysql_num_rows($row_en_revision);

                $total_final += $total_en_revision;
                echo "<td>$total_en_revision</td>";
            }
           /// TOTAL SOLICITUDES EN PROCESO
            for ($i=0; $i < count($arreglo_anios); $i++) { 
              $row_en_proceso = mysql_query("SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.estatus_interno, solicitud_certificacion.estatus_dspp FROM solicitud_certificacion WHERE (solicitud_certificacion.estatus_interno != 8 OR solicitud_certificacion.estatus_interno IS NULL) AND solicitud_certificacion.estatus_dspp != 1 AND solicitud_certificacion.estatus_dspp != 12 AND FROM_UNIXTIME(solicitud_certificacion.fecha_registro, '%Y') = $arreglo_anios[$i]", $dspp) or die(mysql_error());
              $total_en_proceso = mysql_num_rows($row_en_proceso);
              $total_final += $total_en_proceso;
                
                echo "<td>$total_en_proceso</td>";
            }

            ///// TOTAL SOLICITUDES EVALUACION POSITIVA
            for ($i=0; $i < count($arreglo_anios); $i++) { 
              $row_ev_positiva = mysql_query("SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.estatus_interno, solicitud_certificacion.estatus_dspp FROM solicitud_certificacion  WHERE (solicitud_certificacion.estatus_interno = 8 AND solicitud_certificacion.estatus_dspp != 12) AND FROM_UNIXTIME(solicitud_certificacion.fecha_registro, '%Y') = $arreglo_anios[$i]", $dspp) or die(mysql_error());
              $total_ev_positiva = mysql_num_rows($row_ev_positiva);
              $total_final += $total_ev_positiva;
                
                echo "<td>$total_ev_positiva</td>";
            }
            ///// TOTAL SOLICITUDES CERTIFICADAS
            for ($i=0; $i < count($arreglo_anios); $i++) { 
              $row_certificadas = mysql_query("SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.estatus_interno, solicitud_certificacion.estatus_dspp FROM solicitud_certificacion WHERE solicitud_certificacion.estatus_dspp = 12 AND FROM_UNIXTIME(solicitud_certificacion.fecha_registro, '%Y') = $arreglo_anios[$i]", $dspp) or die(mysql_error());
              $total_certificadas = mysql_num_rows($row_certificadas);
              $total_final += $total_certificadas;
                
                echo "<td>$total_certificadas</td>";
            }

            ?>
           <td class="text-center"><?php echo $total_final_nuevas; ?></td>
           <td class="text-center"><?php echo $total_final_renovacion; ?></td>
           <td class="text-center"><?php echo $total_final; ?></td>
         </tr>
      </tbody>
    </table>
  </div>  
</div>

<div class="col-md-12">
  <div class="row">
      <?php
      $td_anios = '';
      $arreglo_anios = array();
      $row_anios = mysql_query("SELECT FROM_UNIXTIME(solicitud_registro.fecha_registro, '%Y') AS 'anio_solicitud' FROM solicitud_registro GROUP BY anio_solicitud ORDER BY anio_solicitud ASC", $dspp) or die(mysql_error());
      $num_td = mysql_num_rows($row_anios);

      while($anio_solicitud = mysql_fetch_assoc($row_anios)){
        $arreglo_anios[] = $anio_solicitud['anio_solicitud'];
        $td_anios .= "<td style='font-size:10px;text-align:center'>$anio_solicitud[anio_solicitud]</td>";
      }
      ?>

    <h4>Solicitudes de Registro</h4>
    <table class="table table-bordered table-hover table-condensed">
      <thead>
        <tr class="warning">
          <th class="text-center" rowspan="2">País</th>
          <th class="text-center" colspan="<?php echo $num_td; ?>">Solicitud</th>
          <th class="text-center" colspan="<?php echo $num_td; ?>">En Proceso</th>
          <th class="text-center" colspan="<?php echo $num_td; ?>">Dictamen Positivo</th>
          <th class="text-center" colspan="<?php echo $num_td; ?>">Certificado Emitido</th>
          <th class="text-center" style="background-color:#e74c3c;color:#ecf0f1" colspan="3">Total</th>
        </tr>
        <tr>
          <?php 
            echo $td_anios;
            echo $td_anios;
            echo $td_anios;
            echo $td_anios;
          ?>
          <td style="background-color:#e74c3c;color:#ecf0f1;font-size:10px;text-align:center">NUEVAS</td>
          <td style="background-color:#e74c3c;color:#ecf0f1;font-size:10px;text-align:center">RENOVACIÓN</td>
          <td style="background-color:#e74c3c;color:#ecf0f1;font-size:10px;text-align:center">TOTAL</td>
        </tr>

      </thead>
      <tbody>
        <?php
          $total_en_revision = 0;
          $total_en_proceso = 0;
          $total_ev_positiva = 0;
          $total_certificadas = 0;
          $total_final = 0;
          $total_final_nuevas = 0;
          $total_final_renovacion = 0;
          $row_pais = mysql_query("SELECT empresa.pais FROM empresa GROUP BY empresa.pais ORDER BY empresa.pais ASC", $dspp) or die(mysql_error());
          while($pais = mysql_fetch_assoc($row_pais)){
            $num_en_revision = 0;
            $num_en_proceso = 0;
            $num_ev_positiva = 0;
            $num_certificadas = 0;

        ?>
          <tr>
            <td><?php echo $pais['pais']; ?></td>
            <!--numero de solicitudes en revision-->
            <?php
            /////// SOLICITUDES EN REVISION
            for ($i=0; $i < count($arreglo_anios); $i++) { 
              $row_en_revision = mysql_query("SELECT solicitud_registro.idsolicitud_registro, solicitud_registro.idempresa, solicitud_registro.estatus_interno, solicitud_registro.estatus_dspp, empresa.pais FROM solicitud_registro INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa WHERE empresa.pais = '$pais[pais]' AND (solicitud_registro.estatus_dspp = 1 OR solicitud_registro.estatus_dspp IS NULL) AND FROM_UNIXTIME(solicitud_registro.fecha_registro, '%Y') = $arreglo_anios[$i]", $dspp) or die(mysql_error());
              $num_en_revision = mysql_num_rows($row_en_revision);
              if($num_en_revision > 0){
                echo "<td class='td_dato'>$num_en_revision</td>";
              }else{
                echo "<td>-</td>";
              }

            }
            ////// SOLICITUDES EN PROCESO
            for ($i=0; $i < count($arreglo_anios); $i++) { 
              $row_en_proceso = mysql_query("SELECT solicitud_registro.idsolicitud_registro, solicitud_registro.idempresa, solicitud_registro.estatus_interno, solicitud_registro.estatus_dspp, empresa.idempresa, empresa.pais FROM solicitud_registro INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa WHERE empresa.pais = '$pais[pais]' AND (solicitud_registro.estatus_interno != 8 OR solicitud_registro.estatus_interno IS NULL) AND solicitud_registro.estatus_dspp != 1 AND solicitud_registro.estatus_dspp != 12 AND FROM_UNIXTIME(solicitud_registro.fecha_registro, '%Y') = $arreglo_anios[$i]", $dspp) or die(mysql_error());
              $num_en_proceso = mysql_num_rows($row_en_proceso);
              if($num_en_proceso > 0){
                echo "<td class='td_dato'>$num_en_proceso</td>";
              }else{
                echo "<td>-</td>";
              }

            }
            /////// SOLICITUDES CON EVALUACION POSITIVA
            for ($i=0; $i < count($arreglo_anios); $i++) { 
              $row_ev_positiva = mysql_query("SELECT solicitud_registro.idsolicitud_registro, solicitud_registro.idempresa, solicitud_registro.estatus_interno, solicitud_registro.estatus_dspp, empresa.pais FROM solicitud_registro INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa WHERE empresa.pais = '$pais[pais]' AND (solicitud_registro.estatus_interno = 8 AND solicitud_registro.estatus_dspp != 12) AND FROM_UNIXTIME(solicitud_registro.fecha_registro, '%Y') = $arreglo_anios[$i]", $dspp) or die(mysql_error());
              $num_ev_positiva = mysql_num_rows($row_ev_positiva);
              if($num_ev_positiva > 0){
                echo "<td class='td_dato'>$num_ev_positiva</td>";
              }else{
                echo "<td>-</td>";
              }

            }
            ////// SOLICITUDES CON CERTIFICADO EMITIDO
            for ($i=0; $i < count($arreglo_anios); $i++) { 
              $row_certificadas = mysql_query("SELECT solicitud_registro.idsolicitud_registro, solicitud_registro.idempresa, solicitud_registro.estatus_interno, solicitud_registro.estatus_dspp, empresa.pais FROM solicitud_registro INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa WHERE empresa.pais = '$pais[pais]'  AND solicitud_registro.estatus_dspp = 12 AND FROM_UNIXTIME(solicitud_registro.fecha_registro, '%Y') = $arreglo_anios[$i]", $dspp) or die(mysql_error());
              $num_certificadas = mysql_num_rows($row_certificadas);
              if($num_certificadas > 0){
                echo "<td class='td_dato'>$num_certificadas</td>";
              }else{
                echo "<td>-</td>";
              }

            }
            $query = "SELECT solicitud_registro.idsolicitud_registro, solicitud_registro.idempresa, solicitud_registro.tipo_solicitud, FROM_UNIXTIME(solicitud_registro.fecha_registro, '%Y') AS 'anio_solicitud', empresa.idempresa, empresa.pais FROM solicitud_registro INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa WHERE empresa.pais = '$pais[pais]' AND solicitud_registro.tipo_solicitud = 'NUEVA' ORDER BY anio_solicitud DESC";
            $row_total_pais = mysql_query($query, $dspp) or die(mysql_error());
            $total_nueva = mysql_num_rows($row_total_pais);

            $query = "SELECT solicitud_registro.idsolicitud_registro, solicitud_registro.idempresa, solicitud_registro.tipo_solicitud, FROM_UNIXTIME(solicitud_registro.fecha_registro, '%Y') AS 'anio_solicitud', empresa.idempresa, empresa.pais FROM solicitud_registro INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa WHERE empresa.pais = '$pais[pais]' AND solicitud_registro.tipo_solicitud = 'RENOVACION' ORDER BY anio_solicitud DESC";
            $row_total_pais = mysql_query($query, $dspp) or die(mysql_error());
            
            $total_renovacion = mysql_num_rows($row_total_pais);


            $total = $total_nueva + $total_renovacion;

            $total_final_nuevas += $total_nueva;
            $total_final_renovacion += $total_renovacion;

             ?>

            <!--numero total de solicitudes-->
            <td class="td_total">
              <?php
              if($total_nueva > 0){
                echo $total_nueva; 
              }else{
                echo '-';
              } 
              ?>
            </td>
            <td class="td_total">
              <?php 
              if($total_renovacion > 0){
                echo $total_renovacion;
              }else{
                echo '-';
              }
              ?>
            </td>
            <td>
              <?php
              if($total > 0){
                echo $total;
              }else{
                echo '-';
              }
              ?>
            </td>
          </tr>
        <?php

        }
         ?>
         <tr style="color:red">
           <td>TOTAL</td>
           <?php 
           /// TOTAL SOLICITUDES EN REVISION
            for ($i=0; $i < count($arreglo_anios); $i++) { 
                $row_en_revision = mysql_query("SELECT solicitud_registro.idsolicitud_registro, solicitud_registro.estatus_interno, solicitud_registro.estatus_dspp FROM solicitud_registro WHERE (solicitud_registro.estatus_dspp = 1 OR solicitud_registro.estatus_dspp IS NULL) AND FROM_UNIXTIME(solicitud_registro.fecha_registro, '%Y') = $arreglo_anios[$i]", $dspp) or die(mysql_error());
                $total_en_revision = mysql_num_rows($row_en_revision);

                $total_final += $total_en_revision;
                echo "<td>$total_en_revision</td>";
            }
           /// TOTAL SOLICITUDES EN PROCESO
            for ($i=0; $i < count($arreglo_anios); $i++) { 
              $row_en_proceso = mysql_query("SELECT solicitud_registro.idsolicitud_registro, solicitud_registro.estatus_interno, solicitud_registro.estatus_dspp FROM solicitud_registro WHERE (solicitud_registro.estatus_interno != 8 OR solicitud_registro.estatus_interno IS NULL) AND solicitud_registro.estatus_dspp != 1 AND solicitud_registro.estatus_dspp != 12 AND FROM_UNIXTIME(solicitud_registro.fecha_registro, '%Y') = $arreglo_anios[$i]", $dspp) or die(mysql_error());
              $total_en_proceso = mysql_num_rows($row_en_proceso);
              $total_final += $total_en_proceso;
                
                echo "<td>$total_en_proceso</td>";
            }

            ///// TOTAL SOLICITUDES EVALUACION POSITIVA
            for ($i=0; $i < count($arreglo_anios); $i++) { 
              $row_ev_positiva = mysql_query("SELECT solicitud_registro.idsolicitud_registro, solicitud_registro.estatus_interno, solicitud_registro.estatus_dspp FROM solicitud_registro  WHERE (solicitud_registro.estatus_interno = 8 AND solicitud_registro.estatus_dspp != 12) AND FROM_UNIXTIME(solicitud_registro.fecha_registro, '%Y') = $arreglo_anios[$i]", $dspp) or die(mysql_error());
              $total_ev_positiva = mysql_num_rows($row_ev_positiva);
              $total_final += $total_ev_positiva;
                
                echo "<td>$total_ev_positiva</td>";
            }
            ///// TOTAL SOLICITUDES CERTIFICADAS
            for ($i=0; $i < count($arreglo_anios); $i++) { 
              $row_certificadas = mysql_query("SELECT solicitud_registro.idsolicitud_registro, solicitud_registro.estatus_interno, solicitud_registro.estatus_dspp FROM solicitud_registro WHERE solicitud_registro.estatus_dspp = 12 AND FROM_UNIXTIME(solicitud_registro.fecha_registro, '%Y') = $arreglo_anios[$i]", $dspp) or die(mysql_error());
              $total_certificadas = mysql_num_rows($row_certificadas);
              $total_final += $total_certificadas;
                
                echo "<td>$total_certificadas</td>";
            }

            ?>
           <td class="text-center"><?php echo $total_final_nuevas; ?></td>
           <td class="text-center"><?php echo $total_final_renovacion; ?></td>
           <td class="text-center"><?php echo $total_final; ?></td>
         </tr>
      </tbody>
    </table>
  </div>  
</div>