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

$idadministrador = $_SESSION['idadministrador'];
$fecha_actual = time();
/* MUESTRA LAS SOLICITUDES CON LOS OPPs SEPARADOS
SELECT opp.*, solicitud_certificacion.*, COUNT(solicitud_certificacion.idsolicitud_certificacion) AS "TOTAL_SOLICITUD" FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE opp.pais = "PerÃº" GROUP BY opp.idopp
*/

/*
SELECT opp.idopp, opp.pais, solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.idoc , solicitud_certificacion.status ,COUNT(solicitud_certificacion.idsolicitud_certificacion) AS "TOTAL_SOLICITUD" FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE opp.pais = "PerÃº"
opp.pais = 'PerÃº'

SELECT opp.idopp, opp.pais, opp.estatus_opp, opp.estatus_dspp, num_socios.idnum_socios, num_socios.idopp, num_socios.numero FROM num_socios INNER JOIN opp ON num_socios.idopp = opp.idopp WHERE opp.pais = 'PerÃº' AND (opp.estatus_opp != 'CANCELADO' OR opp.estatus_opp != 'ARCHIVADO' OR opp.estatus_opp IS NULL) GROUP BY num_socios.idopp*/
if(isset($_POST['guardar_cliente'])){
  
  $nombre = $_POST['nombre'];
  $apellido = $_POST['apellido'];
  $idioma = $_POST['idioma'];
  $pais = $_POST['pais'];
  $direccion = $_POST['direccion'];
  $telefono1 = $_POST['telefono1'];
  $telefono2 = $_POST['telefono2'];
  $email1 = $_POST['email1'];
  $email2 = $_POST['email2'];
  $skype = $_POST['skype'];
  $compania = $_POST['compania'];
  $cargo = $_POST['cargo'];
  $nivel_interes = $_POST['nivel_interes'];
  $informacion_extra = $_POST['informacion_extra'];
  $status = 1; //posible cliente

  $insertSQL = sprintf("INSERT INTO contactos_crm (nombre, apellido, pais, idioma, telefono1, telefono2, email1, email2, skype, compania, cargo, nivel_interes, informacion_extra, creado_por, status, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($nombre, "text"),
    GetSQLValueString($apellido, "text"),
    GetSQLValueString($pais, "text"),
    GetSQLValueString($idioma, "text"),
    GetSQLValueString($telefono1, "text"),
    GetSQLValueString($telefono2, "text"),
    GetSQLValueString($email1, "text"),
    GetSQLValueString($email2, "text"),
    GetSQLValueString($skype, "text"),
    GetSQLValueString($compania, "text"),
    GetSQLValueString($cargo, "text"),
    GetSQLValueString($nivel_interes, "int"),
    GetSQLValueString($informacion_extra, "text"),
    GetSQLValueString($idadministrador, "int"),
    GetSQLValueString($status, "int"),
    GetSQLValueString($fecha_actual, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());


  if($_POST['guardar_cliente'] == 1){
    echo "<script>window.location='?CRM&po_clientes'</script>";
  }

}

$row_pais = mysql_query("SELECT * FROM paises", $dspp) or die(mysql_error());
?>

<form action="" method="POST">
  <h4>Crear, Posible Cliente</h4>
  <div class="row">
    <div class="col-lg-12">
      Información sobre el posible cliente 
      <button type="submit" name="guardar_cliente" value="1" class="btn btn-default">Guardar</button>
      <button type="submit" name="guar_cliente" value="2" class="btn btn-default">Guardar y Crear nuevo</button>
      <a href="?CRM&po_clientes" class="btn btn-default">Cancelar</a>
      <hr>
    </div>
    <div class="col-lg-6 form-horizontal">
      <div class="form-group">
          <label for="nombre" class="col-sm-2 control-label">Nombre</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="nombre" id="nombre" placeholder="nombre">
          </div>
        </div>
        <div class="form-group">
          <label for="apellido" class="col-sm-2 control-label">Apellido(s)</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="apellido" id="apellido" placeholder="Apellido(s)">
          </div>
        </div>
        <div class="form-group">
          <label for="idioma" class="col-sm-2 control-label">Idioma</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="idioma" id="idioma" placeholder="idioma">
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
            <input type="text" class="form-control" name="telefono1" id="telefono1" placeholder="telefono1">
          </div>
        </div>
        <div class="form-group">
          <label for="telefono2" class="col-sm-2 control-label">Telefono Secundario</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="telefono2" id="telefono2" placeholder="telefono2">
          </div>
        </div>
    </div>
    <div class="col-lg-6 form-horizontal">
        <div class="form-group">
          <label for="email1" class="col-sm-2 control-label">Correo Electronico</label>
          <div class="col-sm-10">
            <input type="email" class="form-control" name="email1" id="email1" placeholder="email1">
          </div>
        </div>
        <div class="form-group">
          <label for="email2" class="col-sm-2 control-label">Correo Electronico Secundario</label>
          <div class="col-sm-10">
            <input type="email" class="form-control" name="email2" id="email2" placeholder="email2">
          </div>
        </div>

        <div class="form-group">
          <label for="skype" class="col-sm-2 control-label">Skype</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="skype" id="skype" placeholder="skype">
          </div>
        </div>

        <div class="form-group">
          <label for="compania" class="col-sm-2 control-label">Empresa</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="compania" id="compania" placeholder="Empresa">
          </div>
        </div>
        <div class="form-group">
          <label for="cargo" class="col-sm-2 control-label">Cargo</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="cargo" id="cargo" placeholder="cargo">
          </div>
        </div>
        <div class="form-group">
          <label for="nivel_interes" class="col-sm-2 control-label">Nivel de Interes</label>
          <div class="col-sm-10">
            <select name="nivel_interes" id="nivel_interes">
              <option value="1">Bajo</option>
              <option value="2">Normal</option>
              <option value="3">Alto</option>
            </select>
          </div>
        </div>
        <!--<div class="form-group">
          <label for="status" class="col-sm-2 control-label">status</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="status" id="status" value="" readonly>
          </div>
        </div>-->
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
          <button type="submit" name="guardar_cliente" value="2" class="btn btn-default">Guardar y Crear Nuevo</button>
          <a href="?CRM&po_clientes" class="btn btn-default">Cancelar</a>        
        </div>
     
    </div>

  </div>
</form>