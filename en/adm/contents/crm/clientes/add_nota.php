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
if(isset($_POST['agregar_nota'])){
  $titulo = $_POST['titulo'];
  $descripcion = $_POST['descripcion'];
  $idcontacto = $_POST['idcontacto'];


	//creamos la nueva tarea
  $insertSQL = sprintf("INSERT INTO notas(titulo, descripcion, fecha_registro) VALUES (%s, %s, %s)",
    GetSQLValueString($titulo, "text"),
    GetSQLValueString($descripcion, "text"),
    GetSQLValueString($fecha_actual, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

  //creamos el registro de la tarea registrada
  $idnota = mysql_insert_id($dspp);

  $insertSQL = sprintf("INSERT INTO notas_adm(idnota, idadm) VALUES (%s, %s)",
  	GetSQLValueString($idnota, "int"),
  	GetSQLValueString($idadministrador, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

  echo "<script>alert('se ha creado una nueva nota');</script>";
  /*if($_POST['agregar_nota'] == 1){
    echo "<script>window.location='?CRM&po_clientes'</script>";
  }*/

}

$row_pais = mysql_query("SELECT * FROM paises", $dspp) or die(mysql_error());
?>

<form action="" method="POST">
  <h4>Crear, Nueva Nota</h4>
  <div class="row">
    <div class="col-lg-12">
      Información sobre el posible cliente 
      <button type="submit" name="agregar_nota" value="1" class="btn btn-default">Guardar</button>
      <button type="submit" name="agregar_nota" value="2" class="btn btn-default">Guardar y Crear nuevo</button>
      <a href="?CRM&po_clientes" class="btn btn-default">Cancelar</a>
      <hr>
    </div>
    <div class="col-lg-12">

        <div class="form-group">
          <label for="titulo">Titulo</label>
          <input type="text" class="form-control" name="titulo" id="titulo" placeholder="Titulo de la Nota">
        </div>
        <div class="form-group">
          <label for="descripcion">Descripción de la nota</label>
          <textarea name="descripcion" id="descripcion" class="form-control" rows="2" placeholder="Descripción de la nota"></textarea>
        </div>


        <div class="form-group">
          <label for="idcontacto">Agregar nota al cliente</label>
            <select id="idcontacto" class="form-control chosen-select" data-placeholder="Posibles Clientes" name="idcontacto">
              <option value="">- - -</option>
              <?php
              $row_posibles_clientes1 = mysql_query("SELECT idcontacto, nombre FROM contactos_crm WHERE status = 1", $dspp) or die(mysql_error());

              while($posible_cliente1 = mysql_fetch_assoc($row_posibles_clientes1)){
                echo "<option value='$posible_cliente1[idcontacto]'>$posible_cliente1[nombre]</option>";
              }
               ?>
            </select>
        </div>

        <!--<div class="form-group">
          <label for="status" class="col-sm-2 control-label">status</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="status" id="status" value="" readonly>
          </div>
        </div>-->
    </div>
    <div class="col-lg-12">
        <div class="text-center">
        	<hr>
          <button type="submit" name="agregar_nota" value="1" class="btn btn-default">Guardar</button>
          <button type="submit" name="agregar_nota" value="2" class="btn btn-default">Guardar y Crear Nuevo</button>
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