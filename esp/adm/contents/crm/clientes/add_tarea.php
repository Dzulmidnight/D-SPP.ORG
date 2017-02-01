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
if(isset($_POST['agregar_tarea'])){
	$fecha_inicio = $_POST['fecha_inicio'];
	$fecha_fin = $_POST['fecha_fin'];
	$tipo_tarea = $_POST['tipo_tarea'];
	$status_tarea = 'INICIADA';
	$titulo = $_POST['titulo'];
	$detalle = $_POST['detalle'];
	$hora = $_POST['hora'];
	$responsable = $_POST['responsable'];
	$fecua_registro = $fecha_actual;
	$idcontacto = $_POST['idcontacto'];


	//creamos la nueva tarea
  $insertSQL = sprintf("INSERT INTO tareas(fecha_inicio, fecha_fin, tipo_tarea, status_tarea, titulo, detalle, hora, responsable, fecha_registro, idcontacto) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($fecha_inicio, "int"),
    GetSQLValueString($fecha_fin, "int"),
    GetSQLValueString($tipo_tarea, "int"),
    GetSQLValueString($status_tarea, "text"),
    GetSQLValueString($titulo, "text"),
    GetSQLValueString($detalle, "text"),
    GetSQLValueString($hora, "text"),
    GetSQLValueString($responsable, "int"),
    GetSQLValueString($fecha_actual, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

  //creamos el registro de la tarea registrada
  $idtarea = mysql_insert_id($dspp);

  $insertSQL = sprintf("INSERT INTO tareas_adm(idtarea, idadm) VALUES (%s, %s)",
  	GetSQLValueString($idtarea, "int"),
  	GetSQLValueString($idadministrador, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

  //registramos los involucrados en la tarea
  $insertSQL = sprintf("INSERT INTO involucrados_tarea(idadm, idcontacto) VALUES (%s, %s)",
  	GetSQLValueString($),
  	GetSQLValueString());
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

  /*if($_POST['agregar_tarea'] == 1){
    echo "<script>window.location='?CRM&po_clientes'</script>";
  }*/

}

$row_pais = mysql_query("SELECT * FROM paises", $dspp) or die(mysql_error());
?>

<form action="" method="POST">
  <h4>Crear, Nueva Tarea</h4>
  <div class="row">
    <div class="col-lg-12">
      Información sobre el posible cliente 
      <button type="submit" name="agregar_tarea" value="1" class="btn btn-default">Guardar</button>
      <button type="submit" name="agregar_tarea" value="2" class="btn btn-default">Guardar y Crear nuevo</button>
      <a href="?CRM&po_clientes" class="btn btn-default">Cancelar</a>
      <hr>
    </div>
    <div class="col-lg-6 form-horizontal">
      	<div class="form-group">
          <label for="tipo_tarea" class="col-sm-2 control-label">Tipo de Tarea</label>
          <div class="col-sm-10">
            <select name="tipo_tarea" id="tipo_tarea" onchange="funcionSelect()">
            	<?php
            	$row_tarea = mysql_query("SELECT idtipo_tarea, tipo FROM tipo_tarea", $dspp) or die(mysql_error());
            	while($tipo_tarea = mysql_fetch_assoc($row_tarea)){
            		if($tipo_tarea['idtipo_tarea'] == 7){
            			echo "<option value='$tipo_tarea[idtipo_tarea]' selected>$tipo_tarea[tipo]</option>";
            		}else{
            			echo "<option value='$tipo_tarea[idtipo_tarea]'>$tipo_tarea[tipo]</option>";
            		}
            	}
            	 ?>
            </select>
          </div>
        </div>
      	<div class="form-group">
          <label for="nombre" class="col-sm-2 control-label">Titulo</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Titulo de la tarea">
          </div>
        </div>
        <div class="form-group" id="">
          <label for="detalle" class="col-sm-2 control-label">Descripción de la tarea</label>
          <div class="col-sm-10">
            <textarea name="detalle" id="detalle" class="form-control" rows="2" placeholder="Descripción de la tarea"></textarea>
          </div>
        </div>

        <div class="form-group">
          <label for="responsable" class="col-sm-2 control-label">Responsable de la tarea</label>
          <div class="col-sm-10">
            <select name="responsable" id="responsable">
              <option value="">---</option>
              <?php 
              $row_adm = mysql_query("SELECT idadm, nombre FROM adm", $dspp) or die(mysql_error());
              while($adm = mysql_fetch_assoc($row_adm)){
              	if($adm['idadm'] == $idadministrador){
              		echo "<option value='$adm[idadm]' selected>".utf8_encode($adm['nombre'])."</option>";
              	}else{
              		echo "<option value='$adm[idadm]'>".utf8_encode($adm['nombre'])."</option>";
              	}
              }
               ?>
            </select>
          </div>
        </div>

    </div>
    <div class="col-lg-6 form-horizontal">
        <div class="form-group">
          <label for="fecha_inicio" class="col-sm-2 control-label">Fecha Inicio</label>
          <div class="col-sm-10">
            <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" placeholder="dd/mm/aaaa">
          </div>
        </div>
        <div class="form-group">
          <label for="fecha_fin" class="col-sm-2 control-label">Fecha Fin</label>
          <div class="col-sm-10">
            <input type="date" class="form-control" name="fecha_fin" id="fecha_fin" placeholder="dd/mm/aaaa">
          </div>
        </div>

        <div class="form-group">
          <label for="hora" class="col-sm-2 control-label">Hora</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="hora" id="hora" placeholder="Hora">
          </div>
        </div>


        <div class="form-group" id="selectMultiple">
          <label for="idcontacto" class="col-sm-2 control-label">Posible Cliente Involucrado</label>
          <div class="col-sm-10">
            <select id="" class="form-control chosen-select" data-placeholder="Posibles Clientes" name="idcontacto"  multiple>
              <option value="1">- - -</option>
              <option value="NINGUNO">Ninguno</option>
              <?php
              $row_posibles_clientes1 = mysql_query("SELECT idcontacto, nombre FROM contactos_crm WHERE status = 1", $dspp) or die(mysql_error());

              while($posible_cliente1 = mysql_fetch_assoc($row_posibles_clientes1)){
              	echo "<option value='$posible_cliente1[idcontacto]'>$posible_cliente1[nombre]</option>";
              }
               ?>
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
        <div class="text-center">
        	<hr>
          <button type="submit" name="agregar_tarea" value="1" class="btn btn-default">Guardar</button>
          <button type="submit" name="agregar_tarea" value="2" class="btn btn-default">Guardar y Crear Nuevo</button>
          <a href="?CRM&po_clientes" class="btn btn-default">Cancelar</a>        
        </div>
     
    </div>

  </div>
</form>

<script>
	/*function funcionSelect() {
		var valor = document.getElementById('tipo_tarea').value;
		switch (valor) {
			case '1': //enviar correo
				document.getElementById('descripcion_tarea').style.display = 'block';
				break;
			case '2': //reunion 1 a 1
				document.getElementById('selectNormal').style.display = 'block';
				document.getElementById('selectMultiple').style.display = 'none';
				break;
			case '3':// reunion 1 a varios
				document.getElementById('selectNormal').style.display = 'none';
				document.getElementById('selectMultiple').style.display = 'block';
				break;
			case '4':// llamada 1 a 1
				document.getElementById('divPrincipal').style.display = 'block';
				break;
			case '5':// llamada 1 a varios
				document.getElementById('divPrincipal').style.display = 'block';
				break;
			case '6':// evento
				document.getElementById('divPrincipal').style.display = 'block';
				break;
			case '7':// tarea
				
				break;
			default:
				
				break;
		}
	}
</script>