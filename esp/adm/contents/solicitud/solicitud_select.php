<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php');

//error_reporting(E_ALL ^ E_DEPRECATED);
mysql_select_db($database_dspp, $dspp);

if (!isset($_SESSION)) {
  session_start();
  
  $redireccion = "../index.php?ADM";

  if(!$_SESSION["autentificado"]){
    header("Location:".$redireccion);
  }
}
if (!function_exists("GetSQLValueString")) {
  function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
  {
    if (PHP_VERSION < 6) {
      $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
    }

    $theValue = function_exissts("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

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

$row_solicitud = mysql_query("SELECT solicitud_certificacion.idsolicitud_certificacion , solicitud_certificacion.fecha_registro, opp.nombre AS 'nombre_opp', opp.abreviacion AS 'abreviacion_opp', proceso_certificacion.idproceso_certificacion, proceso_certificacion.estatus_interno, proceso_certificacion.estatus_dspp, solicitud_certificacion.cotizacion_opp, periodo_objecion.* FROM solicitud_certificacion LEFT JOIN opp ON solicitud_certificacion.idopp = opp.idopp LEFT JOIN proceso_certificacion ON solicitud_certificacion.idsolicitud_certificacion = proceso_certificacion.idsolicitud_certificacion LEFT JOIN periodo_objecion ON solicitud_certificacion.idsolicitud_certificacion = periodo_objecion.idsolicitud_certificacion ORDER BY proceso_certificacion.idproceso_certificacion DESC LIMIT 1", $dspp) or die(mysql_error());

?>
<div class="row">
  <div class="col-md-12">
    <table class="table table-bordered" style="font-size:12px">
      <thead>
        <tr>
          <th class="text-center">ID</th>
          <th class="text-center">Fecha Solicitud</th>
          <th class="text-center">Organización</th>
          <th class="text-center">Estatus Solicitud</th>
          <th class="text-center">Cotización</th>
          <th class="text-center">Resolución de Objeción</th>
          <th class="text-center">Proceso Certificación</th>
          <th class="text-center">Certificado</th>
          <th class="text-center">Observaciones Solicitud</th>
          <th class="text-center">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        while($solicitud = mysql_fetch_assoc($row_solicitud)){
        ?>
          <tr>
            <td><?php echo $solicitud['id']; ?></td>
          </tr>
        <?php
        }
         ?>
      </tbody>
    </table>
  </div>
</div>