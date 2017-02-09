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
$row_pais = mysql_query("SELECT * FROM paises", $dspp) or die(mysql_error());
?>

<form action="" method="POST">
  <h4>Crear, Nueva Tarea</h4>
  <div class="row">
    <div class="col-lg-12">
      <button class="btn btn-default">Guardar</button>
      <button class="btn btn-default">Guardar y Crear nuevo</button>
      <button class="btn btn-default">Cancelar</button>
      <hr>
    </div>
    <div class="col-lg-12">
        <div class="form-group">
          <label for="tipo_tarea" class="col-sm-2 control-label">Tipo de Tarea</label>
          <div class="col-sm-10">
            <select name="tipo_tarea" id="tipo_tarea">
              <option value="">Tarea</option>
              <option value="">Enviar Correo</option>
              <option value="">Reunion</option>
              <option value="">Llamada</option>
              <option value="">Evento</option>
            </select>
          </div>
        </div>
    </div>
    <div class="col-lg-6 form-horizontal">
        <div class="form-group">
          <label for="inputEmail3" class="col-sm-2 control-label">Fecha Inicio</label>
          <div class="col-sm-10">
            <input type="date" class="form-control" id="inputEmail3" placeholder="nombre">
          </div>
        </div>
        <div class="form-group">
          <label for="inputEmail3" class="col-sm-2 control-label">Fecha Fin</label>
          <div class="col-sm-10">
            <input type="date" class="form-control" id="inputEmail3" placeholder="nombre">
          </div>
        </div>
        <div class="form-group">
          <label for="apellido" class="col-sm-2 control-label">Hora</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="apellido" placeholder="apellido">
          </div>
        </div>
        <div class="form-group">
          <label for="apellido" class="col-sm-2 control-label">Asunto</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="apellido" placeholder="apellido">
          </div>
        </div>
        <div class="form-group">
          <label for="apellido" class="col-sm-2 control-label">Detalle</label>
          <div class="col-sm-10">
            <textarea name="detalle" id="detalle" class="form-control" rows="2" placeholder="Detalle sobre la Tarea"></textarea>
          </div>
        </div>
    </div>
    <div class="col-lg-6 form-horizontal">
        <div class="form-group">
          <label for="apellido" class="col-sm-2 control-label">Involucrados</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="apellido" placeholder="apellido">
          </div>
        </div>
        <div class="form-group">
          <label for="apellido" class="col-sm-2 control-label">Responsable</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="apellido" placeholder="apellido">
          </div>
        </div>
    </div>
    <div class="col-lg-12 form-horizontal">
        <div class="text-center">
          <button type="submit" class="btn btn-default">Guarda</button>
          <button type="submit" class="btn btn-default">Guardar y Crear Nuevo</button>
          <button type="submit" class="btn btn-default">Cancelar</button>          
        </div>
     
    </div>

  </div>
</form>