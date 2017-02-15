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
if(isset($_POST['actualizar_contacto'])){
  $idcontacto = $_POST['actualizar_contacto'];
  $updateSQL = sprintf("UPDATE contactos_crm SET nombre = %s, apellido = %s, idioma = %s, pais = %s, telefono1 = %s, telefono2 = %s, email1 = %s, email2 = %s, skype = %s, compania = %s, cargo = %s, informacion_extra = %s WHERE idcontacto = %s",
    GetSQLValueString($_POST['nombre'], "text"),
    GetSQLValueString($_POST['apellido'], "text"),
    GetSQLValueString($_POST['idioma'], "text"),
    GetSQLValueString($_POST['pais'], "text"),
    GetSQLValueString($_POST['telefono1'], "text"),
    GetSQLValueString($_POST['telefono2'], "text"),
    GetSQLValueString($_POST['email1'], "text"),
    GetSQLValueString($_POST['email2'], "text"),
    GetSQLValueString($_POST['skype'], "text"),
    GetSQLValueString($_POST['compania'], "text"),
    GetSQLValueString($_POST['cargo'], "text"),
    GetSQLValueString($_POST['informacion_extra'], "text"),
    GetSQLValueString($idcontacto, "int"));
  $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());
}
$idcontacto = $_GET['detalle_cliente'];
$row_cliente = mysql_query("SELECT * FROM contactos_crm WHERE idcontacto = $idcontacto", $dspp) or die(mysql_error());
$cliente = mysql_fetch_assoc($row_cliente);

$row_pais = mysql_query("SELECT * FROM paises", $dspp) or die(mysql_error());
$row_tareas = mysql_query("SELECT * FROM tareas WHERE idcontacto = $cliente[idcontacto]", $dspp) or die(mysql_error());
$num_tareas = mysql_num_rows($row_tareas);


?>

<form action="" method="POST">
  <h4>Detalles de <span style="color:#2980b9"><?php echo $cliente['nombre']." ".$cliente['apellido']; ?></span></h4>
  <div class="row">
    <div class="col-lg-12">
      <hr>
    </div>
    <div class="col-lg-2 text-center"> <!-- INICIA IMAGEN USUARIO -->
      <img src="../../img/usuario.png" alt="">
      <a style="color:white" class="form-control btn btn-success" href=""><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> Enviar mensaje</a>
      <a style="margin-top:10px;color:white" class="form-control btn btn-danger" href=""><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Ver tareas</a>
    </div> <!-- TERMINA IMAGEN USUARIO -->

    <div class="col-lg-10" >
      <div class="btn-group" role="group" aria-label="...">
        <button type="button" id="btn-detalle" onclick="ver_detalle()" class="active btn btn-sm btn-default">Detalles</button>
        <button type="button" id="btn-tareas" onclick="ver_tareas()" class="btn btn-sm btn-default"><?php echo $num_tareas; ?> Tareas</button>
        <button type="button" id="btn-mensajes" onclick="ver_mensajes()" class="btn btn-sm btn-default">Mensajes</button>
        <button type="button" id="btn-notas" onclick="ver_notas()" class="btn btn-sm btn-default">Notas</button>
      </div>
      <div id="detalle_usuario">
        <table class="table" style="font-size:11px;">
          <tr>
            <td>Nombre</td>
            <td><input type="text" style="width:100%" name="nombre" value="<?php echo $cliente['nombre']; ?>"></td>
          </tr>
          <tr>
            <td>Apellido(a)</td>
            <td><input type="text" style="width:100%" name="apellido" value="<?php echo $cliente['apellido']; ?>"></td>
          </tr>
          <tr>
            <td>Idioma</td>
            <td><input type="text" style="width:100%" name="idioma" value="<?php echo $cliente['idioma']; ?>"></td>
          </tr>
          <tr>
            <td>País</td>
            <td>
              <select name="pais" id="pais">
                <option value="">---</option>
                <?php 
                while($pais = mysql_fetch_assoc($row_pais)){
                  if($cliente['pais'] == utf8_encode($pais['nombre'])){
                    echo '<option value='.utf8_encode($pais['nombre']).' selected>'.utf8_encode($pais['nombre']).'</option>';
                  }else{
                    echo '<option value='.utf8_encode($pais['nombre']).'>'.utf8_encode($pais['nombre']).'</option>';
                  }
                }
                 ?>
              </select>
            </td>
          </tr>
          <tr>
            <td>Telefono principal</td>
            <td><input type="text" style="width:100%" name="telefono1" value="<?php echo $cliente['telefono1']; ?>"></td>
          </tr>
          <tr>
            <td>Telefono secundario</td>
            <td><input type="text" style="width:100%" name="telefono2" value="<?php echo $cliente['telefono2']; ?>"></td>
          </tr>
          <tr>
            <td>Email 1</td>
            <td><input type="text" style="width:100%" name="email1" value="<?php echo $cliente['email1']; ?>"></td>
          </tr>
          <tr>
            <td>Email 2</td>
            <td><input type="text" style="width:100%" name="email2" value="<?php echo $cliente['email2']; ?>"></td>
          </tr>
          <tr>
            <td>Skype</td>
            <td><input type="text" style="width:100%" name="skype" value="<?php echo $cliente['skype']; ?>"></td>
          </tr>
          <tr>
            <td>Compañia</td>
            <td><input type="text" style="width:100%" name="compania" value="<?php echo $cliente['compania']; ?>"></td>
          </tr>
          <tr>
            <td>Cargo</td>
            <td><input type="text" style="width:100%" name="cargo" value="<?php echo $cliente['cargo']; ?>"></td>
          </tr>
          <tr>
            <td>Nivel interes</td>
            <td><?php echo $cliente['nivel_interes']; ?></td>
          </tr>
          <tr>
            <td>Información extra</td>
            <td><textarea style="font-size:11px;" name="informacion_extra" id="" rows="2" class="form-control"><?php echo $cliente['informacion_extra']; ?></textarea></td>
          </tr>
          <tr>
            <td><button type="submit" name="actualizar_contacto" value="<?php echo $cliente['idcontacto'] ?>" class="btn btn-sm btn-success"><span class="glyphicon glyphicon-refresh" aria-hidden="true"></span> Actualizar Información</button></td>
          </tr>

        </table>        
      </div>

    </div>

  </div>
</form>

<script>
  function ver_detalle(){
    document.getElementById('detalle_usuario').style.display = 'block';
    document.getElementById('btn-detalle').classList.add('active');
    document.getElementById('btn-tareas').classList.remove('active');
    document.getElementById('btn-mensajes').classList.remove('active');
    document.getElementById('btn-notas').classList.remove('active');
  }
  function ver_tareas(){
    document.getElementById('detalle_usuario').style.display = 'none';
    document.getElementById('btn-tareas').classList.add('active');
    document.getElementById('btn-detalle').classList.remove('active');
    document.getElementById('btn-mensajes').classList.remove('active');
    document.getElementById('btn-notas').classList.remove('active');
  }
  function ver_mensajes(){
    document.getElementById('detalle_usuario').style.display = 'none';
    document.getElementById('btn-mensajes').classList.add('active');
    document.getElementById('btn-detalle').classList.remove('active');
    document.getElementById('btn-tareas').classList.remove('active');
    document.getElementById('btn-notas').classList.remove('active');
  }
  function ver_notas(){
    document.getElementById('detalle_usuario').style.display = 'none';
    document.getElementById('btn-notas').classList.add('active');
    document.getElementById('btn-detalle').classList.remove('active');
    document.getElementById('btn-tareas').classList.remove('active');
    document.getElementById('btn-mensajes').classList.remove('active');
  }

  function administrador(){
    document.getElementById('btn_administrador').style.background = '#f74d65';
    document.getElementById('btn_organizacion').removeAttribute("style");

    document.getElementById('frm_administrador').style.display = 'block';
    document.getElementById('frm_organizacion').style.display = 'none';
  }
  function organizacion()
  {
    document.getElementById('btn_administrador').removeAttribute("style");
    document.getElementById('btn_organizacion').style.background = '#f74d65';

    document.getElementById('frm_organizacion').style.display = 'block';
    document.getElementById('frm_administrador').style.display = 'none';
  }


</script>