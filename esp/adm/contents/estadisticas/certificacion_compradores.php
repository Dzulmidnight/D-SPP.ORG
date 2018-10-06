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
  $row_pais = mysql_query("SELECT opp.pais FROM opp GROUP BY opp.pais ORDER BY opp.pais ASC", $dspp) or die(mysql_error());
   ?>

    <h4>Concentrado de Certificación</h4>
    <table class="table table-bordered table-hover table-condensed">
      <thead>
        <tr>
          <th colspan="14">
            Exportar:
            <a href="#" ><img src="../../img/pdf.png" alt=""></a>
            <a href="#" ><img src="../../img/excel.png" alt=""></a>
          </th>
        </tr>
        <tr class="success">
          <th>#</th>
          <!--<th style="font-size:11px;" class="text-center">País</th>
          <th style="font-size:11px;" class="text-center">Solicitud Inicial(<small>Son OPP que han ingresado por primera vez y solo han cargado la solicitud</small>)</th>
          <th style="font-size:11px;" class="text-center">Solicitud(<small>Son OPP nuevas que han ingresado su solicitud y se les ha enviado una cotizacion</small>)</th>
          <th style="font-size:11px;" class="text-center">En Proceso(<small>OPPs que han aceptado la cotización y ha iniciado su proceso de certificacion</small>)</th>
          <th style="font-size:11px;" class="text-center">Evaluación Positiva(<small>OPPs que han finalizado el proceso de certificación con una evaluación positiva</small>)</th>
          <th style="font-size:11px;" class="text-center">Subtotal Proceso</th>
          <th style="font-size:11px;" class="text-center">Certificada(<small>Se incluyen todas las OPPs que se les ha entragado certificado, ya sean nuevas o renovación</small>)</th>
          <th style="font-size:11px;" class="text-center">En Renovación(<small>estarian entrando las OPPs con certificado expirado pero que se encuentran en proceso de renovacion</small>)</th>
          <!---- faltaria agregar las inactivas ---->
          <!--<th style="font-size:11px;" class="text-center">Canceladas(pero no se debian poner)</th>-->
          <!--<th style="font-size:11px;" class="text-center">Expirado(OPPs, que ha expirado las fechas de sus certificados)</th>
          <th style="font-size:11px;" class="text-center">Inactivas</th>
          <th style="font-size:11px;" class="text-center">Suspendida(<small>OPPs que han sido formalmente suspendidas</small>)</th>
          <th style="font-size:11px;" class="text-center">Subtotal Certificación</th>
          <th style="font-size:11px;" class="text-center">Total</th>-->
          <th style="font-size:11px;" class="text-center">País</th>
          <th style="font-size:11px;" class="text-center">Solicitud Inicial</th>
          <th style="font-size:11px;" class="text-center">Solicitud</th>
          <th style="font-size:11px;" class="text-center">En Proceso</th>
          <th style="font-size:11px;" class="text-center">Evaluación Positiva</th>
          <th style="font-size:11px;" class="text-center">Subtotal Proceso</th>
          <th style="font-size:11px;" class="text-center">Certificada</th>
          <th style="font-size:11px;" class="text-center">En Renovación</th>
          <!---- faltaria agregar las inactivas ---->
          <!--<th style="font-size:11px;" class="text-center">Canceladas(pero no se debian poner)</th>-->
          <th style="font-size:11px;" class="text-center">Expirado</th>
          <th style="font-size:11px;" class="text-center">Inactivas</th>
          <th style="font-size:11px;" class="text-center">Suspendida</th>
          <th style="font-size:11px;" class="text-center">Subtotal Certificación</th>
          <th style="font-size:11px;" class="text-center">Total</th>

          <!--<th class="text-center" style="background-color:#e74c3c;color:#ecf0f1" colspan="3">Total</th>-->
        </tr>


      </thead>
      <tbody>
        <?php
        $contador = 1;
        $total_solicitud_inicial = 0;
        $total_solicitud = 0;
        $total_en_proceso = 0;
        $total_ev_positiva = 0;
        $total_sub_total_proceso = 0;
        $total_certificada = 0;
        $total_en_renovacion = 0;
        $total_inactiva = 0;
        $total_suspendida = 0;
        $total_expirado = 0;
        $total_sub_certificacion = 0;
        $total = 0;
        while($pais = mysql_fetch_assoc($row_pais)){
          $num_sub_total_proceso = 0;
          $num_sub_total_certificacion = 0;
          $num_total = 0;
          //query SOLCITIUD INICIAL
          $row_solicitud_inicial = mysql_query("SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, opp.abreviacion FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE solicitud_certificacion.tipo_solicitud = 'NUEVA' AND solicitud_certificacion.cotizacion_opp IS NULL AND opp.pais = '$pais[pais]'", $dspp);
          $num_solicitud_inicial = mysql_num_rows($row_solicitud_inicial);
          $total_solicitud_inicial += $num_solicitud_inicial;

          //query SOLICITUD
          $row_solicitud = mysql_query("SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, opp.abreviacion FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE solicitud_certificacion.tipo_solicitud = 'NUEVA' AND solicitud_certificacion.cotizacion_opp IS NOT NULL AND solicitud_certificacion.estatus_dspp = 4 AND opp.pais = '$pais[pais]'", $dspp);
          $num_solicitud = mysql_num_rows($row_solicitud);
          $total_solicitud += $num_solicitud;


          //query EN PROCESO,que han aceptado la cotizacion y estan en proceso de certificacion, por lo tanto no pueden tener dictamen positivo, negativo
          $row_en_proceso = mysql_query("SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, opp.abreviacion FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE solicitud_certificacion.tipo_solicitud = 'NUEVA' AND solicitud_certificacion.fecha_aceptacion IS NOT NULL AND (solicitud_certificacion.estatus_dspp = 5 || solicitud_certificacion.estatus_dspp = 6 || solicitud_certificacion.estatus_dspp = 7 || solicitud_certificacion.estatus_dspp = 8 || solicitud_certificacion.estatus_dspp = 9) AND (solicitud_certificacion.estatus_interno != 8 || solicitud_certificacion.estatus_interno IS NULL) AND opp.pais = '$pais[pais]'", $dspp);
          $num_en_proceso = mysql_num_rows($row_en_proceso); 
          $total_en_proceso += $num_en_proceso; 

          //query EVALUACION POSITIVA
          //$row_ev_positiva = mysql_query("SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE solicitud_certificacion.tipo_solicitud = 'NUEVA' AND (solicitud_certificacion.estatus_interno = 8 AND solicitud_certificacion.estatus_dspp != 12) AND opp.pais = '$pais[pais]'", $dspp);
          $row_ev_positiva = mysql_query("SELECT solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, opp.abreviacion FROM solicitud_certificacion INNER JOIN opp ON solicitud_certificacion.idopp = opp.idopp WHERE solicitud_certificacion.tipo_solicitud = 'NUEVA' AND solicitud_certificacion.estatus_interno = 8 AND solicitud_certificacion.estatus_dspp != 12 AND (opp.estatus_opp = 0 || opp.estatus_opp IS NULL) AND opp.pais = '$pais[pais]'", $dspp);

          $num_ev_positiva = mysql_num_rows($row_ev_positiva);
          $total_ev_positiva += $num_ev_positiva;  

          //query SUB TOTAL EN PROCESO
          $num_sub_total_proceso = $num_solicitud_inicial + $num_solicitud + $num_en_proceso + $num_ev_positiva;
          $total_sub_total_proceso += $num_sub_total_proceso;

          //query CERTIFICADAS
          $row_certificadas = mysql_query("SELECT opp.idopp, certificado.idopp, opp.abreviacion FROM opp INNER JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.pais = '$pais[pais]' AND (opp.estatus_dspp != 16 AND opp.estatus_interno != 10 AND opp.estatus_interno != 11 OR opp.estatus_interno = 15) GROUP BY certificado.idopp", $dspp);
          $num_certificadas = mysql_num_rows($row_certificadas);
          $total_certificada += $num_certificadas;

          //query EN RENOVACION, se cuentan las OPP con estatus_dspp = certificado expirado y que no tengan estatus_interno "CANCELADO"
          $row_en_renovacion = mysql_query("SELECT opp.idopp, certificado.idopp, opp.abreviacion FROM opp  INNER JOIN certificado ON opp.idopp = certificado.idopp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE opp.pais = '$pais[pais]' AND (opp.estatus_dspp = 16 AND opp.estatus_interno != 10 AND opp.estatus_interno != 11) AND (opp.estatus_interno = 1 OR opp.estatus_interno = 2 OR opp.estatus_interno = 3 OR opp.estatus_interno = 4 OR opp.estatus_interno = 5 OR opp.estatus_interno = 6 OR opp.estatus_interno = 7 OR opp.estatus_interno = 8 OR opp.estatus_interno = 9) GROUP BY certificado.idopp", $dspp);
          $num_en_renovacion = mysql_num_rows($row_en_renovacion);
          $total_en_renovacion += $num_en_renovacion;

          //query INACTIVA, en inactivas estamo contando las opp con estatus cancelado(10)
          //$row_inactiva = mysql_query("SELECT opp.idopp, certificado.idopp FROM opp INNER JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.pais = '$pais[pais]' AND opp.estatus_interno = 10", $dspp);
          //$num_inactiva = mysql_num_rows($row_inactiva);
          //$total_inactiva += $num_inactiva;


          //query SUSPENDIDA, en inactivas estamo contando las opp con estatus suspendido(11), falta ver con alejandra si se dejan esta, ya que falta checar lo de "suspencion formal"
          $row_suspendida = mysql_query("SELECT opp.idopp, certificado.idopp, opp.abreviacion FROM opp INNER JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.pais = '$pais[pais]' AND opp.estatus_interno = 11", $dspp);
          $num_suspendida = mysql_num_rows($row_suspendida);
          $total_suspendida += $num_suspendida;

          //query EXPIRADO, se cuentan las OPP con estatus_dspp = certificado expirado y que no tengan estatus_interno "CANCELADO"
          $row_expirado = mysql_query("SELECT opp.idopp, certificado.idopp FROM opp  INNER JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.pais = '$pais[pais]' AND (opp.estatus_dspp = 16 AND opp.estatus_interno != 10 AND estatus_interno != 11 AND opp.estatus_interno != 12) AND (opp.estatus_interno != 'CANCELADO' OR opp.estatus_opp != 'ARCHIVADO')  GROUP BY certificado.idopp", $dspp);
          $num_expirado = mysql_num_rows($row_expirado);

          //$total_expirado = $num_expirado - $num_en_renovacion;
          $total_expirado += $num_expirado;

          $row_expirado2 = mysql_query("SELECT opp.idopp, certificado.idopp FROM opp  INNER JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.pais = '$pais[pais]' AND (opp.estatus_dspp = 16 AND opp.estatus_interno != 10 AND estatus_interno != 11 AND opp.estatus_interno != 12) AND (opp.estatus_interno != 'CANCELADO' OR opp.estatus_opp != 'ARCHIVADO')  GROUP BY certificado.idopp", $dspp);
          $num_expirado2 = mysql_num_rows($row_expirado2);


          $row_inactiva = mysql_query("SELECT opp.idopp, opp.abreviacion FROM opp WHERE opp.pais = '$pais[pais]' AND opp.estatus_interno = 12", $dspp) or die(mysql_error());
          $num_inactiva = mysql_num_rows($row_inactiva);

          $total_inactiva += $num_inactiva;
          // num subtotal certificacion
          $num_sub_total_certificacion = $num_certificadas + $num_suspendida + $num_expirado + $num_inactiva;
          $total_sub_certificacion += $num_sub_total_certificacion;

          //num TOTAL
          $num_total = $num_sub_total_proceso + $num_sub_total_certificacion;
          $total += $num_total;
        ?>
        <tr>
          <td><?php echo $contador; ?></td>
          <td><?php echo $pais['pais']; ?></td>
          <!--INICIA SOLICITUD INICIAL: debemos seleccionar las nuevas OPPs-->
          <td class="text-center">
            <?php
            while($registro = mysql_fetch_assoc($row_solicitud_inicial)){
              $abreviacion = $registro['abreviacion'];
            }

            if($num_solicitud_inicial > 0){
              echo "<button type='button' class='btn btn-default' data-toggle='popover' title='Organizaciones' data-content='$abreviacion'><span class='glyphicon glyphicon-search' aria-hidden='true'></span> ".$num_solicitud_inicial."</button>";
            }else{
              echo $num_solicitud_inicial;
            } 
            ?>
          </td>

          <!--INICIA SOLICITUD-->
          <td class="text-center">
            <?php
              $abreviacion = '';
              while($registro = mysql_fetch_assoc($row_solicitud)){
                $abreviacion .= '<p>'.$registro['abreviacion'].'</p>';
              }
              if($num_solicitud > 0){
                echo "<button type='button' class='btn btn-default' data-toggle='popover' title='Organizaciones' data-html='true' data-content='$abreviacion'><span class='glyphicon glyphicon-search' aria-hidden='true'></span> ".$num_solicitud."</button>";
              }else{
                echo $num_solicitud;
              } 
            ?>
          </td>

          <!--INICIA EN PROCESO-->
          <td class="text-center">
            <?php
            $abreviacion = '';
            while($registro = mysql_fetch_assoc($row_en_proceso)){
              $abreviacion .= '<p>'.$registro['abreviacion'].'</p>';
            }
            if($num_en_proceso > 0){
              echo "<button type='button' class='btn btn-default' data-toggle='popover' title='Organizaciones' data-html='true' data-content='$abreviacion'><span class='glyphicon glyphicon-search' aria-hidden='true'></span> ".$num_en_proceso."</button>";
            }else{
              echo $num_en_proceso;
            }
            ?>
          </td>

          <!--INICIA EVALUACION POSITIVA-->
          <td class="text-center">
            <?php
              $abreviacion = '';
              while($registro = mysql_fetch_assoc($row_ev_positiva)){
                $abreviacion .= '<p>'.$registro['abreviacion'].'</p>';
              }
              if($num_ev_positiva > 0){
                echo "<button type='button' class='btn btn-default' data-toggle='popover' title='Organizaciones' data-html='true' data-content='$abreviacion'><span class='glyphicon glyphicon-search' aria-hidden='true'></span> ".$num_ev_positiva."</button>";
              }else{
                echo $num_ev_positiva;
              } 
            ?>
          </td>

          <!--INICIA SUBTOTAL EN PROCESO-->
          <td class="success text-center">
            <?php 
            while($registro = mysql_fetch_assoc($row_en_proceso)){
              echo '<span style="color:red">'.$registro['idopp'].'</span><br>';
            }
            echo $num_sub_total_proceso; 
            ?>
          </td>

          <!--INICIA CERTIFICAD-->
          <td class="text-center">
            <?php
              $abreviacion = '';
              while($registro = mysql_fetch_assoc($row_certificadas)){
                $abreviacion .= '<p>'.$registro['abreviacion'].'</p>';
              }
              if($num_certificadas > 0){
                echo "<button type='button' class='btn btn-default' data-toggle='popover' title='Organizaciones' data-html='true' data-content='$abreviacion'><span class='glyphicon glyphicon-search' aria-hidden='true'></span> ".$num_certificadas."</button>";
              }else{
                echo $num_certificadas;
              } 
            ?>
          </td>

          <td class="text-center">
            <?php
              $abreviacion = '';
              while($registro = mysql_fetch_assoc($row_en_renovacion)){
                $abreviacion .= '<p>'.$registro['abreviacion'].'</p>';
              }
              if($num_en_renovacion > 0){
                echo "<button type='button' class='btn btn-default' data-toggle='popover' title='Organizaciones' data-html='true' data-content='$abreviacion'><span class='glyphicon glyphicon-search' aria-hidden='true'></span> ".$num_en_renovacion."</button>";
              }else{
                echo $num_en_renovacion;
              } 
            ?>

          </td>

          <!--INICIA CANCELADAS-->
          <!--<td class="text-center"><?php echo $num_inactiva; ?></td>-->

          <!--INICIA EXPIRADO-->
          <td class="text-center">
            <?php
            echo $num_expirado - $num_en_renovacion; 
            ?>
          </td>
          
          <!-- INICIA INACTIVAS -->
          <td class="text-center">
            <?php
              $abreviacion = '';
              while($registro = mysql_fetch_assoc($row_inactiva)){
                $abreviacion .= '<p>'.$registro['abreviacion'].'</p>';
              }
              if($num_inactiva > 0){
                echo "<button type='button' class='btn btn-default' data-toggle='popover' title='Organizaciones' data-html='true' data-content='$abreviacion'><span class='glyphicon glyphicon-search' aria-hidden='true'></span> ".$num_inactiva."</button>";
              }else{
                echo $num_inactiva;
              } 
            ?>

          </td>
          <!--INICIA SUSPENDIDA-->
          <td class="text-center">
            <?php
              $abreviacion = '';
              while($registro = mysql_fetch_assoc($row_suspendida)){
                $abreviacion .= '<p>'.$registro['abreviacion'].'</p>';
              }
              if($num_suspendida > 0){
                echo "<button type='button' class='btn btn-default' data-toggle='popover' title='Organizaciones' data-html='true' data-content='$abreviacion'><span class='glyphicon glyphicon-search' aria-hidden='true'></span> ".$num_suspendida."</button>";
              }else{
                echo $num_suspendida;
              } 
            ?>

          </td>

          <!--INICIA SUBTOTAL CERTIFICACION-->
          <td class="success text-center">
            <?php
              echo $num_sub_total_certificacion; 
            ?>
          </td>

          <!--INICIA TOTAL-->
          <td class="text-center" style="background-color:#e74c3c;color:#ecf0f1"><?php echo $num_total; ?></td>
        </tr>
        <?php
        $contador++;
        }
         ?>
         <tr>
           <td style="background-color:#27ae60;color:#ecf0f1" colspan="2" class="text-center"><b>Total</b></td>
           <td style="background-color:#27ae60;color:#ecf0f1" class="text-center"><?php echo $total_solicitud_inicial; ?></td>
           <td style="background-color:#27ae60;color:#ecf0f1" class="text-center"><?php echo $total_solicitud; ?></td>
           <td style="background-color:#27ae60;color:#ecf0f1" class="text-center"><?php echo $total_en_proceso; ?></td>
           <td style="background-color:#27ae60;color:#ecf0f1" class="text-center"><?php echo $total_ev_positiva; ?></td>
           <td class="success text-center"><?php echo $total_sub_total_proceso; ?></td>
           <td style="background-color:#27ae60;color:#ecf0f1" class="text-center"><?php echo $total_certificada; ?></td>
           <td style="background-color:#27ae60;color:#ecf0f1" class="text-center"><?php echo $total_en_renovacion; ?></td>
           <!--<td class="text-center"><?php echo $total_inactiva; ?></td>-->
           <td style="background-color:#27ae60;color:#ecf0f1" class="text-center"><?php echo $total_expirado - $total_en_renovacion; ?></td>
           <!-- INACTIVAS -->
           <td style="background-color:#27ae60;color:#ecf0f1" class="text-center"><?php echo $total_inactiva; ?></td>
           <td style="background-color:#27ae60;color:#ecf0f1" class="text-center"><?php echo $total_suspendida; ?></td>
           <td class="success text-center"><?php echo $total_sub_certificacion ?></td>
           <td style="background-color:#e74c3c;color:#ecf0f1" class="text-center"><?php echo $total; ?></td>
         </tr>
      </tbody>
    </table>
  </div>  

            <form name="formulario1" method="POST" action="../../reportes/concentrado_procesos.php">
              <input type="hidden" name="reporte_pdf" value="1">
              <input type="hidden" name="query" value="<?php echo $query_opp; ?>">
            </form> 
            <form name="formulario2" method="POST" action="../../reportes/concentrado_procesos.php">
              <input type="hidden" name="reporte_excel" value="2">
              <input type="hidden" name="query_excel" value="<?php echo $query_opp; ?>">
            </form>

</div>

<!------------------------------------------------------------------  SECCIÓN SOLICITUDES REGISTRO -------------------------------------------------------------------------------------------->
<!------------------------------------------------------------------                               -------------------------------------------------------------------------------------------->
<div class="col-md-12">
  <div class="row">

  <?php
  $row_pais = mysql_query("SELECT empresa.pais FROM empresa GROUP BY empresa.pais ORDER BY empresa.pais ASC", $dspp) or die(mysql_error());
   ?>

    <h4>Concentrado de Registros</h4>
    <table class="table table-bordered table-hover table-condensed">
      <thead>
        <tr class="warning">
          <!--04_05_2017<th style="font-size:11px;" class="text-center">País</th>
          <th style="font-size:11px;" class="text-center">Solicitud Inicial(<small>Son empresas que han ingresado por primera vez y solo han cargado la solicitud</small>)</th>
          <th style="font-size:11px;" class="text-center">Solicitud(<small>Son empresas nuevas que han ingresado su solicitud y se les ha enviado una cotizacion</small>)</th>
          <th style="font-size:11px;" class="text-center">En Proceso(<small>empresa que han aceptado la cotización y ha iniciado su proceso de certificacion</small>)</th>
          <th style="font-size:11px;" class="text-center">Evaluación Positiva(<small>empresas que han finalizado el proceso de certificación con una evaluación positiva</small>)</th>
          <th style="font-size:11px;" class="text-center">Subtotal Proceso</th>
          <th style="font-size:11px;" class="text-center">Certificada(<small>Se incluyen todas las empresas que se les ha entragado certificado, ya sean nuevas o renovación</small>)</th>
          <th style="font-size:11px;" class="text-center">Inactiva</th>
          <th style="font-size:11px;" class="text-center">Suspendida(<small>empresas que han sido formalmente suspendidas</small>)</th>
          <th style="font-size:11px;" class="text-center">Expirado(empresas, que ha expirado las fechas de sus certificados)</th>
          <th style="font-size:11px;" class="text-center">Subtotal Certificación</th>
          <th style="font-size:11px;" class="text-center">Total</th>
          <!--<th class="text-center" style="background-color:#e74c3c;color:#ecf0f1" colspan="3">Total</th> 04_05_2017-->
          <th>#</th>
          <th style="font-size:11px;" class="text-center">País</th>
          <th style="font-size:11px;" class="text-center">Solicitud Inicial</th>
          <th style="font-size:11px;" class="text-center">Solicitud</th>
          <th style="font-size:11px;" class="text-center">En Proceso</th>
          <th style="font-size:11px;" class="text-center">Evaluación Positiva</th>
          <th style="font-size:11px;" class="text-center">Subtotal Proceso</th>
          <th style="font-size:11px;" class="text-center">Certificada</th>
          <!--<th style="font-size:11px;" class="text-center">En Renovación</th>-->
          <!---- faltaria agregar las inactivas ---->
          <!--<th style="font-size:11px;" class="text-center">Canceladas(pero no se debian poner)</th>-->
          <th style="font-size:11px;" class="text-center">Expirado</th>
          <th style="font-size:11px;" class="text-center">Inactivas</th>
          <th style="font-size:11px;" class="text-center">Suspendida</th>
          <th style="font-size:11px;" class="text-center">Subtotal Certificación</th>
          <th style="font-size:11px;" class="text-center">Total</th>
        </tr>


      </thead>
      <tbody>
        <?php
        $contador = 1;
        $total_solicitud_inicial = 0;
        $total_solicitud = 0;
        $total_en_proceso = 0;
        $total_ev_positiva = 0;
        $total_sub_total_proceso = 0;
        $total_certificada = 0;
        $total_en_renovacion = 0;
        $total_inactiva = 0;
        $total_suspendida = 0;
        $total_expirado = 0;
        $total_sub_certificacion = 0;
        $total = 0;
        while($pais = mysql_fetch_assoc($row_pais)){
          $num_sub_total_proceso = 0;
          $num_sub_total_certificacion = 0;
          $num_total = 0;
          //query SOLCITIUD INICIAL
          $row_solicitud_inicial = mysql_query("SELECT solicitud_registro.idsolicitud_registro, solicitud_registro.idempresa, empresa.abreviacion FROM solicitud_registro INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa WHERE solicitud_registro.fecha_registro < '1525188494' AND solicitud_registro.tipo_solicitud = 'NUEVA' AND solicitud_registro.cotizacion_empresa IS NULL AND empresa.pais = '$pais[pais]' GROUP BY solicitud_registro.idempresa", $dspp);
          $num_solicitud_inicial = mysql_num_rows($row_solicitud_inicial);
          $total_solicitud_inicial += $num_solicitud_inicial;

          //query SOLICITUD
          $row_solicitud = mysql_query("SELECT solicitud_registro.idsolicitud_registro, solicitud_registro.idempresa, empresa.abreviacion FROM solicitud_registro INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa WHERE solicitud_registro.fecha_registro < '1525188494' AND solicitud_registro.tipo_solicitud = 'NUEVA' AND solicitud_registro.cotizacion_empresa IS NOT NULL AND solicitud_registro.estatus_dspp = 4 AND empresa.pais = '$pais[pais]'", $dspp);
          $num_solicitud = mysql_num_rows($row_solicitud);
          $total_solicitud += $num_solicitud;


          //query EN PROCESO,que han aceptado la cotizacion y estan en proceso de certificacion, por lo tanto no pueden tener dictamen positivo, negativo
          $row_en_proceso = mysql_query("SELECT solicitud_registro.idsolicitud_registro, solicitud_registro.idempresa, empresa.abreviacion FROM solicitud_registro INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa WHERE solicitud_registro.fecha_registro < '1525188494' AND solicitud_registro.tipo_solicitud = 'NUEVA' AND solicitud_registro.fecha_aceptacion IS NOT NULL AND (solicitud_registro.estatus_dspp = 5 || solicitud_registro.estatus_dspp = 6 || solicitud_registro.estatus_dspp = 7 || solicitud_registro.estatus_dspp = 8 || solicitud_registro.estatus_dspp = 9) AND (solicitud_registro.estatus_interno != 8 || solicitud_registro.estatus_interno IS NULL) AND empresa.pais = '$pais[pais]' GROUP BY solicitud_registro.idempresa", $dspp);
          $num_en_proceso = mysql_num_rows($row_en_proceso); 
          $total_en_proceso += $num_en_proceso; 

          //query EVALUACION POSITIVA
          //$row_ev_positiva = mysql_query("SELECT solicitud_registro.idsolicitud_registro, solicitud_registro.idempresa FROM solicitud_registro INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa WHERE solicitud_registro.tipo_solicitud = 'NUEVA' AND (solicitud_registro.estatus_interno = 8 AND solicitud_registro.estatus_dspp != 12) AND empresa.pais = '$pais[pais]'", $dspp);
          $row_ev_positiva = mysql_query("SELECT solicitud_registro.idsolicitud_registro, solicitud_registro.idempresa, empresa.abreviacion FROM solicitud_registro INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa WHERE solicitud_registro.fecha_registro < '1525188494' AND solicitud_registro.tipo_solicitud = 'NUEVA' AND solicitud_registro.estatus_interno = 8 AND solicitud_registro.estatus_dspp != 12 AND (empresa.estatus_empresa = 0 || empresa.estatus_empresa IS NULL) AND empresa.pais = '$pais[pais]'", $dspp);

          $num_ev_positiva = mysql_num_rows($row_ev_positiva);
          $total_ev_positiva += $num_ev_positiva;  

          //query SUB TOTAL EN PROCESO
          $num_sub_total_proceso = $num_solicitud_inicial + $num_solicitud + $num_en_proceso + $num_ev_positiva;
          $total_sub_total_proceso += $num_sub_total_proceso;

          //query CERTIFICADAS
          $row_certificadas = mysql_query("SELECT empresa.idempresa, certificado.idempresa, empresa.abreviacion FROM empresa INNER JOIN certificado ON empresa.idempresa = certificado.idempresa WHERE empresa.fecha_registro < '1525188494' AND empresa.pais = '$pais[pais]' AND (empresa.estatus_dspp != 16 AND empresa.estatus_interno != 10 AND empresa.estatus_interno != 11 OR empresa.estatus_interno = 15) GROUP BY certificado.idempresa", $dspp);
          $num_certificadas = mysql_num_rows($row_certificadas);
          $total_certificada += $num_certificadas;

          //query EN RENOVACION, se cuentan las OPP con estatus_dspp = certificado expirado y que no tengan estatus_interno "CANCELADO"
          $row_en_renovacion = mysql_query("SELECT empresa.idempresa, certificado.idempresa, empresa.abreviacion FROM empresa  INNER JOIN certificado ON empresa.idempresa = certificado.idempresa INNER JOIN solicitud_registro ON empresa.idempresa = solicitud_registro.idempresa WHERE empresa.fecha_registro < 1525188494 AND empresa.pais = '$pais[pais]' AND (empresa.estatus_dspp = 16 AND empresa.estatus_interno != 10 AND empresa.estatus_interno != 11) AND (empresa.estatus_interno = 1 OR empresa.estatus_interno = 2 OR empresa.estatus_interno = 3 OR empresa.estatus_interno = 4 OR empresa.estatus_interno = 5 OR empresa.estatus_interno = 6 OR empresa.estatus_interno = 7 OR empresa.estatus_interno = 8 OR empresa.estatus_interno = 9) GROUP BY certificado.idempresa", $dspp);
          $num_en_renovacion = mysql_num_rows($row_en_renovacion);
          $total_en_renovacion += $num_en_renovacion;

          //query INACTIVA, en inactivas estamo contando las opp con estatus cancelado(10)
          //$row_inactiva = mysql_query("SELECT opp.idopp, certificado.idopp FROM opp INNER JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.pais = '$pais[pais]' AND opp.estatus_interno = 10", $dspp);
          //$num_inactiva = mysql_num_rows($row_inactiva);
          //$total_inactiva += $num_inactiva;


          //query SUSPENDIDA, en inactivas estamo contando las opp con estatus suspendido(11), falta ver con alejandra si se dejan esta, ya que falta checar lo de "suspencion formal"
          $row_suspendida = mysql_query("SELECT empresa.idempresa, certificado.idempresa, empresa.abreviacion FROM empresa INNER JOIN certificado ON empresa.idempresa = certificado.idempresa WHERE empresa.fecha_registro < 1525188494 AND empresa.pais = '$pais[pais]' AND empresa.estatus_interno = 11", $dspp);
          $num_suspendida = mysql_num_rows($row_suspendida);
          $total_suspendida += $num_suspendida;

          //query EXPIRADO, se cuentan las OPP con estatus_dspp = certificado expirado y que no tengan estatus_interno "CANCELADO"
          $row_expirado = mysql_query("SELECT empresa.idempresa, empresa.abreviacion, certificado.idempresa FROM empresa INNER JOIN certificado ON empresa.idempresa = certificado.idempresa WHERE empresa.fecha_registro < 1525188494 AND empresa.pais = '$pais[pais]' AND (empresa.estatus_dspp = 16 AND empresa.estatus_interno != 10 AND estatus_interno != 11 AND empresa.estatus_interno != 12) AND (empresa.estatus_interno != 'CANCELADO' OR empresa.estatus_empresa != 'ARCHIVADO')  GROUP BY certificado.idempresa", $dspp);
          $num_expirado = mysql_num_rows($row_expirado);

          //$total_expirado = $num_expirado - $num_en_renovacion;
          $total_expirado += $num_expirado;

          $row_expirado2 = mysql_query("SELECT empresa.idempresa, certificado.idempresa FROM empresa  INNER JOIN certificado ON empresa.idempresa = certificado.idempresa WHERE empresa.fecha_registro < 1525188494 AND empresa.pais = '$pais[pais]' AND (empresa.estatus_dspp = 16 AND empresa.estatus_interno != 10 AND estatus_interno != 11 AND empresa.estatus_interno != 12) AND (empresa.estatus_interno != 'CANCELADO' OR empresa.estatus_empresa != 'ARCHIVADO')  GROUP BY certificado.idopp", $dspp);
          $num_expirado2 = mysql_num_rows($row_expirado2);


          $row_inactiva = mysql_query("SELECT empresa.idempresa, empresa.abreviacion FROM empresa WHERE empresa.fecha_registro < 1525188494 AND empresa.pais = '$pais[pais]' AND empresa.estatus_interno = 12", $dspp) or die(mysql_error());
          $num_inactiva = mysql_num_rows($row_inactiva);

          $total_inactiva += $num_inactiva;
          // num subtotal certificacion
          $num_sub_total_certificacion = $num_certificadas + $num_suspendida + $num_expirado + $num_inactiva;
          $total_sub_certificacion += $num_sub_total_certificacion;

          //num TOTAL
          $num_total = $num_sub_total_proceso + $num_sub_total_certificacion;
          $total += $num_total;
        ?>
        <tr>
          <td><?php echo $contador; ?></td>
          <td><?php echo $pais['pais']; ?></td>
          <!--INICIA SOLICITUD INICIAL: debemos seleccionar las nuevas OPPs-->
          <td class="text-center">
            <?php
              $abreviacion = '';
              while($registro = mysql_fetch_assoc($row_solicitud_inicial)){
                $abreviacion .= '<p>'.$registro['abreviacion'].'</p>';
              }
              if($num_solicitud_inicial > 0){
                echo "<button type='button' class='btn btn-default' data-toggle='popover' title='Empresas' data-html='true' data-content='$abreviacion'><span class='glyphicon glyphicon-search' aria-hidden='true'></span> ".$num_solicitud_inicial."</button>";
              }else{
                echo $num_solicitud_inicial;
              } 
            ?>

          </td>

          <!--INICIA SOLICITUD-->
          <td class="text-center">
            <?php
              $abreviacion = '';
              while($registro = mysql_fetch_assoc($row_solicitud)){
                $abreviacion .= '<p>'.$registro['abreviacion'].'</p>';
              }
              if($num_solicitud > 0){
                echo "<button type='button' class='btn btn-default' data-toggle='popover' title='Empresas' data-html='true' data-content='$abreviacion'><span class='glyphicon glyphicon-search' aria-hidden='true'></span> ".$num_solicitud."</button>";
              }else{
                echo $num_solicitud;
              } 
            ?>

          </td>

          <!--INICIA EN PROCESO-->
          <td class="text-center">
            <?php
              $abreviacion = '';
              while($registro = mysql_fetch_assoc($row_en_proceso)){
                $abreviacion .= '<p>'.$registro['abreviacion'].'</p>';
              }
              if($num_en_proceso > 0){
                echo "<button type='button' class='btn btn-default' data-toggle='popover' title='Empresas' data-html='true' data-content='$abreviacion'><span class='glyphicon glyphicon-search' aria-hidden='true'></span> ".$num_en_proceso."</button>";
              }else{
                echo $num_en_proceso;
              } 
            ?>

          </td>

          <!--INICIA EVALUACION POSITIVA-->
          <td class="text-center">
            <?php
              $abreviacion = '';
              while($registro = mysql_fetch_assoc($row_ev_positiva)){
                $abreviacion .= '<p>'.$registro['abreviacion'].'</p>';
              }
              if($num_ev_positiva > 0){
                echo "<button type='button' class='btn btn-default' data-toggle='popover' title='Empresas' data-html='true' data-content='$abreviacion'><span class='glyphicon glyphicon-search' aria-hidden='true'></span> ".$num_ev_positiva."</button>";
              }else{
                echo $num_ev_positiva;
              } 
            ?>

          </td>

          <!--INICIA SUBTOTAL EN PROCESO-->
          <td class="success text-center"><?php echo $num_sub_total_proceso; ?></td>

          <!--INICIA CERTIFICAD-->
          <td class="text-center">
            <?php
              $abreviacion = '';
              while($registro = mysql_fetch_assoc($row_certificadas)){
                $abreviacion .= '<p>'.$registro['abreviacion'].'</p>';
              }
              if($num_certificadas > 0){
                echo "<button type='button' class='btn btn-default' data-toggle='popover' title='Empresas' data-html='true' data-content='$abreviacion'><span class='glyphicon glyphicon-search' aria-hidden='true'></span> ".$num_certificadas."</button>";
              }else{
                echo $num_certificadas;
              } 
            ?>
          </td>

          <!--<td class="text-center"><?php echo $num_en_renovacion; ?></td>-->

          <!--INICIA CANCELADAS-->
          <!--<td class="text-center"><?php echo $num_inactiva; ?></td>-->

          <!--INICIA EXPIRADO-->
          <td class="text-center">
            <?php
              $abreviacion = '';
              while($registro = mysql_fetch_assoc($row_expirado)){
                $abreviacion .= '<p>'.$registro['abreviacion'].'</p>';
              }
              if($num_expirado > 0){
                echo "<button type='button' class='btn btn-default' data-toggle='popover' title='Empresas' data-html='true' data-content='$abreviacion'><span class='glyphicon glyphicon-search' aria-hidden='true'></span> ".$num_expirado."</button>";
              }else{
                echo $num_expirado;
              } 
            ?>
          </td>
          
          <!-- INICIA INACTIVAS -->
          <td class="text-center">
            <?php
              $abreviacion = '';
              while($registro = mysql_fetch_assoc($row_inactiva)){
                $abreviacion .= '<p>'.$registro['abreviacion'].'</p>';
              }
              if($num_inactiva > 0){
                echo "<button type='button' class='btn btn-default' data-toggle='popover' title='Empresas' data-html='true' data-content='$abreviacion'><span class='glyphicon glyphicon-search' aria-hidden='true'></span> ".$num_inactiva."</button>";
              }else{
                echo $num_inactiva;
              } 
            ?>
          </td>
          <!--INICIA SUSPENDIDA-->
          <td class="text-center">
            <?php
              $abreviacion = '';
              while($registro = mysql_fetch_assoc($row_suspendida)){
                $abreviacion .= '<p>'.$registro['abreviacion'].'</p>';
              }
              if($num_suspendida > 0){
                echo "<button type='button' class='btn btn-default' data-toggle='popover' title='Empresas' data-html='true' data-content='$abreviacion'><span class='glyphicon glyphicon-search' aria-hidden='true'></span> ".$num_suspendida."</button>";
              }else{
                echo $num_suspendida;
              } 
            ?>
          </td>

          <!--INICIA SUBTOTAL CERTIFICACION-->
          <td class="success text-center"><?php echo $num_sub_total_certificacion; ?></td>

          <!--INICIA TOTAL-->
          <td class="text-center" style="background-color:#e74c3c;color:#ecf0f1"><?php echo $num_total; ?></td>
        </tr>
        <?php
        $contador++;
        }
         ?>
         <tr>
            <!-- TOTAL -->
           <td style="background-color:#27ae60;color:#ecf0f1" colspan="2" class="text-center"><b>Total</b></td>
           <!-- NUMERO DE SOLICITUD INICIAL -->
           <td style="background-color:#27ae60;color:#ecf0f1" class="text-center"><?php echo $total_solicitud_inicial; ?></td>
           <!-- NUMERO DE SOLICITUD -->
           <td style="background-color:#27ae60;color:#ecf0f1" class="text-center"><?php echo $total_solicitud; ?></td>
           <!-- NUMERO DE EN PROCESO -->
           <td style="background-color:#27ae60;color:#ecf0f1" class="text-center"><?php echo $total_en_proceso; ?></td>
           <!-- NUMERO DE EVALUACIÓN POSITIVA -->
           <td style="background-color:#27ae60;color:#ecf0f1" class="text-center"><?php echo $total_ev_positiva; ?></td>
           <!-- NUMERO DE SUB-TOTAL PROCESO -->
           <td class="success text-center"><?php echo $total_sub_total_proceso; ?></td>
           <!-- NUMERO DE CERTIFICADA -->
           <td style="background-color:#27ae60;color:#ecf0f1" class="text-center"><?php echo $total_certificada; ?></td>
           <!-- NUMERO EN RENOVACIÓN -->
           <!--<td style="background-color:#27ae60;color:#ecf0f1" class="text-center"><?php echo $total_en_renovacion; ?></td>-->
           <!-- NUMERO DE EXPIRADO -->
           <td style="background-color:#27ae60;color:#ecf0f1" class="text-center"><?php echo ($total_expirado); ?></td>
           <!-- INACTIVAS -->
           <td style="background-color:#27ae60;color:#ecf0f1" class="text-center"><?php echo $total_inactiva; ?></td>
            <!-- NUMERO DE EXPIRADO -->
           <td style="background-color:#27ae60;color:#ecf0f1" class="text-center"><?php echo $total_suspendida; ?></td>

           <td class="success text-center"><?php echo $total_sub_certificacion ?></td>
           <td style="background-color:#e74c3c;color:#ecf0f1" class="text-center"><?php echo $total; ?></td>
         </tr>
      </tbody>
    </table>
  </div>  
</div>