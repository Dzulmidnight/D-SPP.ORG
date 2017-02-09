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
  <h4>Crear, Nueva Oportunidad</h4>
  <div class="row">
    <div class="col-lg-12">
      Información sobre el posible cliente 
      <button class="btn btn-default">Guardar</button>
      <button class="btn btn-default">Guardar y Crear nuevo</button>
      <button class="btn btn-default">Cancelar</button>
      <hr>
    </div>
    <div class="col-lg-6 form-horizontal">
      <div class="form-group">
          <label for="inputEmail3" class="col-sm-2 control-label">Nombre</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="inputEmail3" placeholder="nombre">
          </div>
        </div>
        <div class="form-group">
          <label for="apellido" class="col-sm-2 control-label">Apellido</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="apellido" placeholder="apellido">
          </div>
        </div>
        <div class="form-group">
          <label for="idioma" class="col-sm-2 control-label">Idioma</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="idioma" placeholder="idioma">
          </div>
        </div>
        <div class="form-group">
          <label for="pais" class="col-sm-2 control-label">Pais</label>
          <div class="col-sm-10">
            <select name="pais" id="pais">
              <option value="">---</option>
              <?php 
              while($pais = mysql_fetch_assoc($row_pais)){
                echo '<option value='.utf8_encode($pais['nombre']).'>'.utf8_encode($pais['nombre']).'</option>';
              }
               ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label for="direccion" class="col-sm-2 control-label">Dirección</label>
          <div class="col-sm-10">
            <textarea name="direccion" id="direccion" class="form-control" placeholder="Dirección del Cliente" rows="2"></textarea>
          </div>
        </div>
        <div class="form-group">
          <label for="telefono1" class="col-sm-2 control-label">Telefono Principal</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="telefono1" placeholder="telefono1">
          </div>
        </div>
        <div class="form-group">
          <label for="telefono2" class="col-sm-2 control-label">Telefono Secundario</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="telefono2" placeholder="telefono2">
          </div>
        </div>
    </div>
    <div class="col-lg-6 form-horizontal">
        <div class="form-group">
          <label for="email1" class="col-sm-2 control-label">Correo Electronico</label>
          <div class="col-sm-10">
            <input type="email" class="form-control" id="email1" placeholder="email1">
          </div>
        </div>
        <div class="form-group">
          <label for="email2" class="col-sm-2 control-label">Correo Electronico Secundario</label>
          <div class="col-sm-10">
            <input type="email" class="form-control" id="email2" placeholder="email2">
          </div>
        </div>

        <div class="form-group">
          <label for="skype" class="col-sm-2 control-label">Skype</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="skype" placeholder="skype">
          </div>
        </div>

        <div class="form-group">
          <label for="empresa" class="col-sm-2 control-label">Empresa</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="empresa" placeholder="empresa">
          </div>
        </div>
        <div class="form-group">
          <label for="cargo" class="col-sm-2 control-label">Cargo</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="cargo" placeholder="cargo">
          </div>
        </div>
        <div class="form-group">
          <label for="nivel_interes" class="col-sm-2 control-label">Nivel de Interes</label>
          <div class="col-sm-10">
            <select name="nivel_interes" id="nivel_interes">
              <option value="">Bajo</option>
              <option value="">Normal</option>
              <option value="">Alto</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label for="informacion_extra" class="col-sm-2 control-label">informacion_extra</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="informacion_extra" placeholder="informacion_extra">
          </div>
        </div>
        <div class="form-group">
          <label for="status" class="col-sm-2 control-label">status</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="status" placeholder="status">
          </div>
        </div>
    </div>
    <div class="col-lg-12 form-horizontal">
        <div class="form-group">
          <label for="informacion_extra" class="col-sm-2 control-label">Información Extra</label>
          <div class="col-sm-10">
            <textarea name="informacion_extra" id="informacion_extra" class="form-control" rows="2" placeholder="Información extra sobre el cliente"></textarea>
          </div>
        </div>

        <div class="text-center">
          <button type="submit" name="guardar_cliente" value="1" class="btn btn-default">Guardar</button>
          <button type="submit" class="btn btn-default">Guardar y Crear Nuevo</button>
          <button type="submit" class="btn btn-default">Cancelar</button>          
        </div>
     
    </div>

  </div>
</form>