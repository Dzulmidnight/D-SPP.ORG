<?php 
  require_once('../Connections/dspp.php');
  require_once('../Connections/mail.php');

mysql_select_db($database_dspp, $dspp);

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

/*
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
  $insertSQL = sprintf("INSERT INTO contacto (idopp, contacto, cargo, tipo, telefono1, telefono2, email1, emaril2) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['idopp'], "int"),
                       GetSQLValueString($_POST['contacto'], "text"),
                       GetSQLValueString($_POST['cargo'], "text"),
                       GetSQLValueString($_POST['tipo'], "text"),
                       GetSQLValueString($_POST['telefono1'], "text"),
                       GetSQLValueString($_POST['telefono2'], "text"),
                       GetSQLValueString($_POST['email1'], "text"),
                       GetSQLValueString($_POST['emaril2'], "text"));

  mysql_select_db($database_dspp, $dspp);
  $Result1 = mysql_query($insertSQL, $dspp) or die(mysql_error());
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form3")) {
  $updateSQL = sprintf("UPDATE contacto SET idopp=%s, contacto=%s, cargo=%s, tipo=%s, telefono1=%s, telefono2=%s, email1=%s, emaril2=%s WHERE idcontacto=%s",
                       GetSQLValueString($_POST['idopp'], "int"),
                       GetSQLValueString($_POST['contacto'], "text"),
                       GetSQLValueString($_POST['cargo'], "text"),
                       GetSQLValueString($_POST['tipo'], "text"),
                       GetSQLValueString($_POST['telefono1'], "text"),
                       GetSQLValueString($_POST['telefono2'], "text"),
                       GetSQLValueString($_POST['email1'], "text"),
                       GetSQLValueString($_POST['emaril2'], "text"),
                       GetSQLValueString($_POST['idcontacto'], "int"));

  mysql_select_db($database_dspp, $dspp);
  $Result1 = mysql_query($updateSQL, $dspp) or die(mysql_error());
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form5")) {
  $updateSQL = sprintf("UPDATE cta_bn SET idopp=%s, banco=%s, sucursal=%s, cuenta=%s, clabe=%s, propietario=%s WHERE idcta_bn=%s",
                       GetSQLValueString($_POST['idopp'], "int"),
                       GetSQLValueString($_POST['banco'], "text"),
                       GetSQLValueString($_POST['sucursal'], "text"),
                       GetSQLValueString($_POST['cuenta'], "text"),
                       GetSQLValueString($_POST['clabe'], "text"),
                       GetSQLValueString($_POST['propietario'], "text"),
                       GetSQLValueString($_POST['idcta_bn'], "int"));

  mysql_select_db($database_dspp, $dspp);
  $Result1 = mysql_query($updateSQL, $dspp) or die(mysql_error());
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form7")) {
  $updateSQL = sprintf("UPDATE ultima_accion SET ultima_accion=%s, persona=%s, fecha=%s, observacion=%s WHERE idultima_accion=%s",
                       GetSQLValueString($_POST['ultima_accion'], "text"),
                       GetSQLValueString($_POST['persona'], "text"),
                       GetSQLValueString($_POST['fecha'], "text"),
                       GetSQLValueString($_POST['observacion'], "text"),
                       GetSQLValueString($_POST['idultima_accion'], "int"));

  mysql_select_db($database_dspp, $dspp);
  $Result1 = mysql_query($updateSQL, $dspp) or die(mysql_error());
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form6")) {
  $insertSQL = sprintf("INSERT INTO ultima_accion (idopp, ultima_accion, persona, fecha, observacion) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['idopp'], "int"),
                       GetSQLValueString($_POST['ultima_accion'], "text"),
                       GetSQLValueString($_POST['persona'], "text"),
                       GetSQLValueString($_POST['fecha'], "text"),
                       GetSQLValueString($_POST['observacion'], "text"));

  mysql_select_db($database_dspp, $dspp);
  $Result1 = mysql_query($insertSQL, $dspp) or die(mysql_error());
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form4")) {
  $insertSQL = sprintf("INSERT INTO cta_bn (idopp, banco, sucursal, cuenta, clabe, propietario) VALUES (%s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['idopp'], "int"),
                       GetSQLValueString($_POST['banco'], "text"),
                       GetSQLValueString($_POST['sucursal'], "text"),
                       GetSQLValueString($_POST['cuenta'], "text"),
                       GetSQLValueString($_POST['clabe'], "text"),
                       GetSQLValueString($_POST['propietario'], "text"));

  mysql_select_db($database_dspp, $dspp);
  $Result1 = mysql_query($insertSQL, $dspp) or die(mysql_error());
}
*/

if(isset($_POST['contacto_delete'])){
	$query=sprintf("delete from contacto where idcontacto = %s",GetSQLValueString($_POST['idcontacto'], "text"));
	$ejecutar=mysql_query($query,$dspp) or die(mysql_error());
}

if(isset($_POST['cta_bn_delete'])){
	$query=sprintf("delete from cta_bn where idcta_bn = %s",GetSQLValueString($_POST['idcta_bn'], "text"));
	$ejecutar=mysql_query($query,$dspp) or die(mysql_error());
}

if(isset($_POST['action_delete'])){
	$query=sprintf("delete from ultima_accion where idultima_accion = %s",GetSQLValueString($_POST['idultima_accion'], "text"));
	$ejecutar=mysql_query($query,$dspp) or die(mysql_error());
}


$colname_contacto_detail = "-1";
if (isset($_GET['idcontacto'])) {
  $colname_contacto_detail = $_GET['idcontacto'];
}
mysql_select_db($database_dspp, $dspp);
$query_contacto_detail = sprintf("SELECT * FROM contacto WHERE idcontacto = %s", GetSQLValueString($colname_contacto_detail, "int"));
$contacto_detail = mysql_query($query_contacto_detail, $dspp) or die(mysql_error());
$row_contacto_detail = mysql_fetch_assoc($contacto_detail);
$totalRows_contacto_detail = mysql_num_rows($contacto_detail);

$colname_cta_bn_detail = "-1";
if (isset($_GET['idcta_bn'])) {
  $colname_cta_bn_detail = $_GET['idcta_bn'];
}
mysql_select_db($database_dspp, $dspp);
$query_cta_bn_detail = sprintf("SELECT * FROM cta_bn WHERE idcta_bn = %s", GetSQLValueString($colname_cta_bn_detail, "int"));
$cta_bn_detail = mysql_query($query_cta_bn_detail, $dspp) or die(mysql_error());
$row_cta_bn_detail = mysql_fetch_assoc($cta_bn_detail);
$totalRows_cta_bn_detail = mysql_num_rows($cta_bn_detail);

$maxRows_accion_detalle = 20;
$pageNum_accion_detalle = 0;
if (isset($_GET['pageNum_accion_detalle'])) {
  $pageNum_accion_detalle = $_GET['pageNum_accion_detalle'];
}
$startRow_accion_detalle = $pageNum_accion_detalle * $maxRows_accion_detalle;

$colname_accion_detalle = "-1";
if (isset($_GET['idsolicitud'])) {
  $colname_accion_detalle = $_GET['idsolicitud'];
}


###################################################################################################

mysql_select_db($database_dspp, $dspp);
$query_accion_detalle = sprintf("SELECT solicitud_certificacion.*, oc.idoc, oc.nombre AS 'nombreOC' FROM solicitud_certificacion INNER JOIN oc ON solicitud_certificacion.idoc = oc.idoc WHERE idsolicitud_certificacion = %s", GetSQLValueString($colname_accion_detalle, "int"));
$query_limit_accion_detalle = sprintf("%s LIMIT %d, %d", $query_accion_detalle, $startRow_accion_detalle, $maxRows_accion_detalle);
$accion_detalle = mysql_query($query_limit_accion_detalle, $dspp) or die(mysql_error());

$row_solicitud = mysql_fetch_assoc($accion_detalle);




         

###################################################################################################





if (isset($_GET['totalRows_accion_detalle'])) {
  $totalRows_accion_detalle = $_GET['totalRows_accion_detalle'];
} else {
  $all_accion_detalle = mysql_query($query_accion_detalle);
  $totalRows_accion_detalle = mysql_num_rows($all_accion_detalle);
}
$totalPages_accion_detalle = ceil($totalRows_accion_detalle/$maxRows_accion_detalle)-1;

$colname_accion_detail = "-1";
if (isset($_GET['idultima_accion'])) {
  $colname_accion_detail = $_GET['idultima_accion'];
}
mysql_select_db($database_dspp, $dspp);
$query_accion_detail = sprintf("SELECT * FROM ultima_accion WHERE idultima_accion = %s", GetSQLValueString($colname_accion_detail, "int"));
$accion_detail = mysql_query($query_accion_detail, $dspp) or die(mysql_error());
$row_accion_detail = mysql_fetch_assoc($accion_detail);
$totalRows_accion_detail = mysql_num_rows($accion_detail);

mysql_select_db($database_dspp, $dspp);
$query_accion_lateral = "SELECT idultima_accion, idopp, ultima_accion FROM ultima_accion ORDER BY fecha DESC";
$accion_lateral = mysql_query($query_accion_lateral, $dspp) or die(mysql_error());
$row_accion_lateral = mysql_fetch_assoc($accion_lateral);
$totalRows_accion_lateral = mysql_num_rows($accion_lateral);

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE opp SET idf=%s, password=%s, nombre=%s, abreviacion=%s, sitio_web=%s, email=%s, pais=%s, idoc=%s, razon_social=%s, direccion_fiscal=%s, rfc=%s WHERE idopp=%s",
                       GetSQLValueString($_POST['idf'], "text"),
                       GetSQLValueString($_POST['password'], "text"),
                       GetSQLValueString($_POST['nombre'], "text"),
                       GetSQLValueString($_POST['abreviacion'], "text"),
                       GetSQLValueString($_POST['sitio_web'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['pais'], "text"),
                       GetSQLValueString($_POST['idoc'], "int"),
                       GetSQLValueString($_POST['razon_social'], "text"),
                       GetSQLValueString($_POST['direccion_fiscal'], "text"),
                       GetSQLValueString($_POST['rfc'], "text"),
                       GetSQLValueString($_POST['idopp'], "int"));

  $Result1 = mysql_query($updateSQL, $dspp) or die(mysql_error());
}

$colname_opp = "-1";
 
$colname_opp = $_SESSION['idopp'];

$query_opp = sprintf("SELECT * FROM opp WHERE idopp = %s", GetSQLValueString($colname_opp, "int"));
$opp = mysql_query($query_opp, $dspp) or die(mysql_error());
$row_opp = mysql_fetch_assoc($opp);
$totalRows_opp = mysql_num_rows($opp);

$colname_cta_bn = "-1";
if (isset($_GET['idopp'])) {
  $colname_cta_bn = $_GET['idopp'];
}
$query_cta_bn = sprintf("SELECT * FROM cta_bn WHERE idopp = %s", GetSQLValueString($colname_cta_bn, "int"));
$cta_bn = mysql_query($query_cta_bn, $dspp) or die(mysql_error());
//$row_cta_bn = mysql_fetch_assoc($cta_bn);
$totalRows_cta_bn = mysql_num_rows($cta_bn);

$colname_contacto = "-1";
if (isset($_GET['idopp'])) {
  $colname_contacto = $_GET['idopp'];
}
$query_contacto = sprintf("SELECT * FROM contacto WHERE idopp = %s ORDER BY tipo ASC, contacto asc", GetSQLValueString($colname_contacto, "int"));
$contacto = mysql_query($query_contacto, $dspp) or die(mysql_error());
//$row_contacto = mysql_fetch_assoc($contacto);
$totalRows_contacto = mysql_num_rows($contacto);

$query_oc = "SELECT * FROM oc ORDER BY nombre ASC";
$oc = mysql_query($query_oc, $dspp) or die(mysql_error());
//$row_oc = mysql_fetch_assoc($oc);
$totalRows_oc = mysql_num_rows($oc);

$query_pais = "SELECT * FROM paises ORDER BY nombre ASC";
$pais = mysql_query($query_pais, $dspp) or die(mysql_error());
//$row_pais = mysql_fetch_assoc($pais);
$totalRows_pais = mysql_num_rows($pais);


?>
<div class="row-xs-12">
  
  <div class="col-xs-12">
  <!------------------------------ MENSAJE ACTUALIZAR ---------------------------------------------->
  <? if(isset($_POST['update'])){?>
  <p>
  <div class="alert alert-success" role="alert"><? echo $_POST['update'];?></div>
  </p>
  <? }?>
  <!---------------------------------- MENSAJE ACTUALIZAR ------------------------------------------>
    

  <!------------------------------ MENSAJE DE DENEGACION ---------------------------------------------->
  <? if(!empty($row_solicitud['observaciones'])){?>
    <p>
      <div class="alert alert-danger" role="alert">
        <h4>Observaciones realizadas por: <?echo $row_solicitud['nombreOC']?></h4>
        <br>
        <? echo nl2br($row_solicitud['observaciones']);?>
      </div>
    </p>
  <? }?>
  <!---------------------------------- MENSAJE DE DENEGACION ------------------------------------------>
    
<form class="" method="post" name="form1" action="<?php echo $editFormAction; ?>" enctype="multipart/form-data">

	<table class="table table-bordered table-striped col-xs-8">
		<tr>
			<th colspan="4" class="text-center"><h3>Solicitud de Certificación para Organizaciones de Pequeños Productores</h3></th>
		</tr>	
    <?php
      $procedimiento = $row_solicitud['procedimiento'];
    ?>    
    <tr>
      <td colspan="4">
        
                <div class="col-xs-12 text-center">
                  <div class="row">
                <h4>Procedimiento de Certificación <br><small>(realizado por OC)</small></h4>
                  </div>
                </div>
                <div class="col-xs-3 text-center">
                  <div class="row">
                    <div class="col-xs-12">
                      <p style="font-size:10px;"><b>DOCUMENTAL "ACORTADO"</b></p> 
                    </div>       
                    <div class="col-xs-12">
                      <input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='DOCUMENTAL "ACORTADO"' <?php if($procedimiento == 'DOCUMENTAL "ACORTADO"'){echo "checked";}else{echo "readonly";} ?> >
      
                    </div>                        
                  </div>
                </div>
                <div class="col-xs-3 text-center">
                  <div class="row">
                    <div class="col-xs-12">
                      <p style="font-size:10px;"><b>DOCUMENTAL "NORMAL"</b></p> 
                    </div>
                    <div class="col-xs-12">
                      <input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='DOCUMENTAL "NORMAL"' <?php if($procedimiento == 'DOCUMENTAL "NORMAL"'){echo "checked";}else{echo "readonly";} ?> >
      
                    </div>                
                  </div>
                </div>
                <div class="col-xs-3 text-center">
                  <div class="row">
                    <div class="col-xs-12">
                      <p style="font-size:10px;"><b>COMPLETO "IN SITU"</b></p>  
                    </div>
                    <div class="col-xs-12">
                      <input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='COMPLETO "IN SITU"' <?php if($procedimiento == 'COMPLETO "IN SITU"'){echo "checked";}else{echo "readonly";} ?> >
      
                    </div>                
                  </div>
                </div>
                <div class="col-xs-3 text-center">
                  <div class="row">
                    <div class="col-xs-12">
                      <p style="font-size:10px;"><b>COMPLETO "A DISTANCIA"</b></p>  
                    </div>
                    <div class="col-xs-12">
                      <input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='COMPLETO "A DISTANCIA"' <?php if($procedimiento == 'COMPLETO "A DISTANCIA"'){echo "checked";}else{echo "readonly";} ?> >
      
                    </div>                
                  </div>
                </div>    
      </td>
    </tr>


		<tr class="success">
			<th colspan="4" class="text-center">DATOS GENERALES</th>
		</tr>
		<tr>
			<td colspan="2">
				NOMBRE COMPLETO DE LA ORGANIZACIÓN DE PEQUEÑOS PRODCUTORES
			</td>
			<td colspan="2">
				<input type="text" autofocus="autofocus" class="form-control" id="exampleInputEmail1" size="70" placeholder="Nombre Organización" value="<?php echo $row_opp['nombre']?>" readonly>
			</td>
		</tr>
		<tr>
			<td colspan="2">RFC</td>
			<td colspan="2">
				<?php 
					if(isset($row_opp['rfc'])){
						echo "<input type='text' class='form-control' id='exampleInputEmail1' placeholder='RFC' value='$row_opp[rfc]' readonly>";

					}else{
						echo "<input type='text' class='form-control' id='exampleInputEmail1' placeholder='NO DISPONIBLE' readonly>";

					}
				 ?>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				DIRECCIÓN COMPLETA DE SUS OFICINAS CENTRALES(CALLE, BARRIO, LUGAR, REGIÓN)<br>
				<?php 
					if(isset($row_opp['direccion_fiscal'])){
						echo "<input type='text' class='form-control' name='direccion_opp' id='exampleInputEmail1' value='$row_opp[direccion_fiscal]' readonly>";
					}else{
						echo "<input type='text' class='form-control' name='direccion_opp' id='exampleInputEmail1' placeholder='No Disponible' readonly>";
					}
				 ?>

			</td>
			<td colspan="1">
				PAÍS<br>
				<?php if(isset($row_opp['pais'])){
						echo "<input type='text' class='form-control' name='direccion' id='exampleInputEmail1' placeholder='Dirección de Oficinas' value='$row_opp[pais]' readonly>";}
					else{ ?>
					No Disponible				
		      <?php } ?>
			</td>
		</tr>	
		<tr>
			<td colspan="2">CORREO ELECTRONICO</td>
			<td colspan="2">
				<?php 
					if(isset($row_opp['email'])){
						echo "<input type='email' class='form-control' name='email_opp' id='exampleInputEmail1' value='$row_opp[email]' readonly>";
					}else{
						echo "<input type='email' class='form-control' name='email_opp' id='exampleInputEmail1' placeholder='No Disponible' readonly>";
					}
				 ?>

			</td>
		</tr>
		<tr>
			<td colspan="3">
				SITIO WEB<br>
				<?php 
					if(isset($row_opp['sitio_web'])){
						echo "<input type='text' class='form-control' name='web_opp' id='exampleInputEmail1' value='$row_opp[sitio_web]' readonly>";
					}else{
						echo "<input type='text' class='form-control' name='web_opp' id='exampleInputEmail1' placeholder='No Disponible' readonly>";
					}
				 ?>
				
			</td>
			<td colspan="1">
				TELEFONO<br>
				<?php 
					if(isset($row_opp['telefono1'])){
						echo "<input type='text' class='form-control' name='telefono' id='exampleInputEmail1' value='$row_opp[telefono1]' readonly>";
					}else{
						echo "<input type='text' class='form-control' name='telefono' id='exampleInputEmail1' placeholder='No Disponible' readonly>";
					}
				 ?>
				
			</td>
		</tr>		
		<tr>
			<td class="text-center" colspan="4">
				DATOS FISCALES(PARA FACTURACIÓN COMO DOMICILIO, RFC, RUC, CIUDAD, PAÍS, ETC)<br>
			</td>
		</tr>
		<tr>
			<?php 
				if(isset($row_solicitud['direccion_fiscal'])){
					echo "<td class='col-xs-3'>DOMICILIO: <input type='text' class='form-control' name='f_domicilio' id='exampleInputEmail1' value='$row_solicitud[direccion_fiscal]' readonly></td>";
				}else{
					echo "<td class='col-xs-3'>DOMICILIO: <input type='text' class='form-control' name='f_domicilio' id='exampleInputEmail1' placeholder='No Disponible' readonly></td>";
				}
				if(isset($row_solicitud['rfc'])){
					echo "<td class='col-xs-3'>RFC: <input type='text' class='form-control' name='f_rfc' id='exampleInputEmail1' value='$row_solicitud[rfc]' readonly></td>";
				}else{
					echo "<td class='col-xs-3'>RFC: <input type='text' class='form-control' name='f_rfc' id='exampleInputEmail1' placeholder='No Disponible' readonly></td>";
				}
			 ?>		
			<td class="col-xs-3">RUC: <input type="text" class="form-control" name="ruc" id="exampleInputEmail1" placeholder="RUC" value="<?php echo $row_solicitud['ruc']?>" readonly></td>
			
			<td class="col-xs-3">CIUDAD: <input type="text" class="form-control" name="ciudad" id="exampleInputEmail1" placeholder="Ciudad" value="<?php echo $row_solicitud['ciudad']?>" readonly></td>
		</tr>
		<tr class="text-center warning">
			<td colspan="4">PERSONA(S) DE CONTACTO</td>
		</tr>
		<tr>
			<td colspan="2">
				NOMBRE DE CONTACTO SOLICITUD<br>
				<input type="text" class="form-control" name="p1_nombre" id="exampleInputEmail1" placeholder="Contacto Solicitud 1" value="<?php echo $row_solicitud['p1_nombre']?>" readonly><br>
				<input type="text" class="form-control" name="p2_nombre" id="exampleInputEmail1" placeholder="Contacto Solicitud 2" value="<?php echo $row_solicitud['p2_nombre']?>" readonly><br>
				CORREO ELECTRÓNICO DE CONTACTO
				<input type="email" class="form-control" name="p1_email" id="exampleInputEmail1" placeholder="Correo Electrónico 1" value="<?php echo $row_solicitud['p1_email']?>" readonly><br>
				<input type="email" class="form-control" name="p2_email" id="exampleInputEmail1" placeholder="Correo Electrónico 2" value="<?php echo $row_solicitud['p2_email']?>" readonly><br>
			</td>
			<td colspan="2">
				CARGO<br>
				<input type="text" class="form-control" name="p1_cargo" id="exampleInputEmail1" placeholder="Cargo 1" value="<?php echo $row_solicitud['p1_cargo']?>" readonly><br>
				<input type="text" class="form-control" name="p2_cargo" id="exampleInputEmail1" placeholder="Cargo 2" value="<?php echo $row_solicitud['p2_cargo']?>" readonly><br>
				TELÉFONO<br>
				<input type="text" class="form-control" name="p1_telefono" id="exampleInputtext1" placeholder="Telefono 1" value="<?php echo $row_solicitud['p1_telefono']?>" readonly><br>
				<input type="text" class="form-control" name="p2_telefono" id="exampleInputEmail1" placeholder="Telefono 2" value="<?php echo $row_solicitud['p2_telefono']?>" readonly><br>
			</td>
		</tr>
		<tr class="text-center warning">
			<td colspan="4">PERSONA DEL ÁREA ADMINISTRATIVA</td>
		</tr>

		<tr>
			<td colspan="2">
				PERSONA DEL ÁREA ADMINISTRATIVA<br>
				<input type="text" class="form-control" name="adm_nom1" id="exampleInputEmail1" placeholder="Persona del Área Administrativa 1" value="<?php echo $row_solicitud['adm_nom1']?>" readonly><br>
				<input type="text" class="form-control" name="adm_nom2" id="exampleInputEmail1" placeholder="Persona del Área Administrativa 2" value="<?php echo $row_solicitud['adm_nom2']?>" readonly><br>
				CORREO ELECTRÓNICO DEL ÁREA ADMINISTRATIVA
				<input type="email" class="form-control" name="adm_email1" id="exampleInputEmail1" placeholder="Correo Electrónico 1" value="<?php echo $row_solicitud['adm_email1']?>" readonly><br>
				<input type="email" class="form-control" name="adm_email2" id="exampleInputEmail1" placeholder="Correo Electrónico 2" value="<?php echo $row_solicitud['adm_email2']?>" readonly>
			</td>
			<td colspan="2">
				TELÉFONO PERSONA DEL ÁREA ADMINISTRATIVA<br>
				<input type="text" class="form-control" name="adm_tel1" id="exampleInputEmail1" placeholder="Teléfono Área Adminsitrativa 1" value="<?php echo $row_solicitud['adm_tel1']?>" readonly><br>
				<input type="text" class="form-control" name="adm_tel2" id="exampleInputEmail1" placeholder="Teléfono Área Administrativa 2" value="<?php echo $row_solicitud['adm_tel2']?>" readonly>
			</td>
		</tr>	
		<tr >
			<td>NÚMERO DE SOCIOS PRODUCTORES</td>
			<td><input type="text" class="form-control" name="resp1" id="exampleInputEmail1" placeholder="Número de socios" value="<?php echo $row_solicitud['resp1']?>" readonly></td>
			<td>NÚMERO DE SOCIOS PRODUCTORES DEL (DE LOS) PRODUCTO(S) A INCLUIR EN LA CERTIFICACION</td>
			<td><input type="text" class="form-control" name="resp2" id="exampleInputEmail1" placeholder="Número de socios" value="<?php echo $row_solicitud['resp2']?>" readonly></td>
		</tr>

		<tr >
			<td>NÚMERO DE SOCIOS PRODUCTORES</td>
			<td><input type="text" class="form-control" name="resp3" id="exampleInputEmail1" placeholder="Número de socios" value="<?php echo $row_solicitud['resp3']?>" readonly></td>
			<td>NÚMERO DE SOCIOS PRODUCTORES DEL (DE LOS) PRODUCTO(S) A INCLUIR EN LA CERTIFICACION</td>
			<td><input type="text" class="form-control" name="resp4" id="exampleInputEmail1" placeholder="Número de socios" value="<?php echo $row_solicitud['resp4']?>" readonly></td>
		</tr>
		<tr class="success">
			<th colspan="4" class="text-center">DATOS DE OPERACIÓN</th>
		</tr>
		<tr>
			<td colspan="4">
				1. EXPLIQUE SI SE TRATA DE UNA ORGANIZACIÓN DE PEQUEÑOS PRODUCTORES DE 1ER, 2DO, 3ER O 4TO GRADO, ASÍ COMO EL NÚMERO DE OPP DE 3ER, 2DO O 1ER GRADO, Y EL NÚMERO DE COMUNIDADES, ZONAS O GRUPOS DE TRABAJO, EN SU CASO, CON LAS QUE CUENTA:
				<br>
				<textarea class="form-control" name="op_resp1" id="" rows="3" readonly><?php echo $row_solicitud['op_resp1']?></textarea>
				
			</td>
		</tr>
		<tr>
			<td>
				<h5 class="col-xs-12">NÚMERO DE OPP DE 3ER GRADO:</h5>
				<textarea class="col-xs-12 form-control" name="op_area1" id="" cols="10" rows="5" readonly><?php echo $row_solicitud['op_area1']?></textarea>
				
			</td>
			<td>
				<h5 class="col-xs-12">NÚMERO DE OPP DE 2DO GRADO:</h5>	
				<textarea class="col-xs-12 form-control" name="op_area2" id="" cols="10" rows="5" readonly><?php echo $row_solicitud['op_area2']?></textarea>	
			</td>
			<td>
				<h5 class="col-xs-12">NÚMERO DE OPP DE 1ER GRADO:</h5>
				<textarea class="col-xs-12 form-control" name="op_area3" id="" cols="10" rows="5" readonly><?php echo $row_solicitud['op_area3']?></textarea>
			</td>
			<td>
				<h5 class="col-xs-12">NÚMERO DE COMUNIDADES, ZONAS O GRUPOS DE TRABAJO:</h5>
				<textarea class="col-xs-12 form-control" name="op_area4" id="" cols="10" rows="5" readonly><?php echo $row_solicitud['op_area4']?></textarea>
				
			</td>
		</tr>
		<tr>
			<td colspan="4">
				2. ESPECIFIQUE QUÉ PRODUCTO(S) QUIERE INCLUIR EN EL CERTIFICADO DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES PARA LOS CUALES EL ORGANISMO DE CERTIFICACIÓN REALIZARÁ LA EVALUACIÓN.
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<textarea name="op_resp2" id="" class="form-control" rows="3" readonly><?php echo $row_solicitud['op_resp2']?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				3. MENCIONE SI SU ORGANIZACIÓN QUIERE INCLUIR ALGÚN CALIFICATIVO ADICIONAL PARA USO COMPLEMENTARIO CON EL DISEÑO GRÁFICO DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES.<sup>4</sup>
				<br>
				<h6><sup>4</sup> Revisar el Reglamento Gráfico y la lista de Calificativos Complementarios opcionales vigentes.</h6>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<textarea name="op_resp3" id="" class="form-control" rows="3" readonly><?php echo $row_solicitud['op_resp3']?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				4. SELECCIONE EL ALCANCE QUE TIENE LA ORGANIZACIÓN DE PEQUEÑOS PRODUCTORES:
			</td>
		</tr>
		<tr>
<?php 
$texto = $row_solicitud['op_resp4'];
 ?>

      <td colspan="4">
        <div class="col-xs-4">
          <?php 
$cadena_buscada   = 'PRODUCCION';
$posicion_coincidencia = strpos($texto, $cadena_buscada);

            if($posicion_coincidencia === false){
              echo "PRODUCCIÓN <input name='op_resp4[]' type='checkbox' value='PRODUCCION' readonly>";
            }else{
              echo "PRODUCCIÓN <input name='op_resp4[]' type='checkbox' value='PRODUCCION' checked readonly>";
            } 
          ?>
          
        </div>
        <div class="col-xs-4">
          <?php 
$cadena_buscada   = 'PROCESAMIENTO';
$posicion_coincidencia = strpos($texto, $cadena_buscada);

            if($posicion_coincidencia === false){
              echo "PROCESAMIENTO <input name='op_resp4[]' type='checkbox' value='PROCESAMIENTO' readonly>";
            }else{
              echo "PROCESAMIENTO <input name='op_resp4[]' type='checkbox' value='PROCESAMIENTO' checked readonly>";
            } 
          ?>
        </div>
        <div class="col-xs-4">
          <?php
$cadena_buscada   = 'EXPORTACION';
$posicion_coincidencia = strpos($texto, $cadena_buscada);

            if($posicion_coincidencia === false){
              echo "EXPORTACIÓN <input name='op_resp4[]' type='checkbox' value='EXPORTACION' readonly>";
            }else{
              echo "EXPORTACIÓN <input name='op_resp4[]' type='checkbox' value='EXPORTACION' checked readonly>";
            } 
          ?>          
        </div>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				5. ESPECIFIQUE SI SUBCONTRATA LOS SERVICIOS DE PLANTAS DE PROCESAMIENTO, EMPRESAS DE COMERCIALIZACIÓN O EMPRESAS QUE REALICEN LA IMPORTACIÓN O EXPORTACIÓN, SI LA RESPUESTA ES AFIRMATIVA, MENCIONE EL NOMBRE Y EL SERVICIO QUE REALIZA.
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<textarea class="form-control" name="op_resp5" id="" rows="3" readonly><?php echo $row_solicitud['op_resp5']?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				6. SI SUBCONTRATA LOS SERVICIOS DE PLANTAS DE PROCESAMIENTO, EMPRESAS DE COMERCIALIZACIÓN O EMPRESAS QUE REALICEN LA IMPORTACIÓN O EXPORTACIÓN, INDIQUE SI ESTAS EMPRESAS VAN A REALIZAR EL REGISTRO BAJO EL PROGRAMA DEL SPP O SERÁN CONTROLADAS A TRAVÉS DE LA ORGANIZACIÓN DE PEQUEÑOS PRODUCTORES.<sup>5</sup>
				<br>
				<h6><sup>5</sup> Revisar el documento de 'Directrices Generales del Sistema SPP' en su última versión.</h6>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<textarea class="form-control" name="op_resp6" id="" rows="3" readonly><?php echo $row_solicitud['op_resp6']?></textarea>
			</td>
		</tr>		
		<tr>
			<td colspan="4">
				7. ADICIONAL A SUS OFICINAS CENTRALES, ESPECIFIQUE CUÁNTOS CENTROS DE ACOPIO, ÁREAS DE PROCESAMIENTO U OFICINAS ADICIONALES TIENE.
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<textarea class="form-control" name="op_resp7" id="" rows="3" readonly><?php echo $row_solicitud['op_resp7']?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				8. ¿CUENTA CON UN SISTEMA DE CONTROL INTERNO PARA DAR CUMPLIMIENTO A LOS CRITERIOS DE LA NORMA GENERAL DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES?, EN SU CASO, EXPLIQUE.
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<textarea class="form-control" name="op_resp8" id="" rows="3" readonly><?php echo $row_solicitud['op_resp8']?></textarea>
			</td>
		</tr>	
		<tr>
			<td colspan="4">
				9. LLENAR LA TABLA DE ACUERDO A LAS CERTIFICACIONES QUE TIENE, (EJEMPLO: EU, NOP, JASS, FLO, etc).
			</td>
		</tr>
		<tr>


			<td colspan="4">
				<table class="table table-bordered" id="tablaCertificaciones">
					<tr>
						<td>CERTIFICACIÓN</td>
						<td>CERTIFICADORA</td>
						<td>AÑO INICIAL DE CERTIFICACIÓN?</td>
						<td>¿HA SIDO INTERRUMPIDA?</td>	
						<!--<td>
							<button type="button" onclick="tablaCertificaciones()" class="btn btn-primary" aria-label="Left Align">
							  <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
							</button>
							
						</td>-->
					</tr>

          <?php 
          $query_certificacion_detalle = "SELECT * FROM certificaciones WHERE idsolicitud_certificacion = $_GET[idsolicitud]";
          $certificacion_detalle = mysql_query($query_certificacion_detalle, $dspp) or die(mysql_error());
          $contador = 0;
          while($row_certificacion = mysql_fetch_assoc($certificacion_detalle)){
            ?>
            <tr class="text-center">
              <td><input type="text" class="form-control" name="certificacion[$contador]" id="exampleInputEmail1" placeholder="CERTIFICACIÓN" value="<?echo $row_certificacion['certificacion']?>" readonly></td>
              <td><input type="text" class="form-control" name="certificadora[$contador]" id="exampleInputEmail1" placeholder="CERTIFICADORA" value="<?echo $row_certificacion['certificadora']?>" readonly></td>
              <td><input type="date" class="form-control" name="ano_inicial[$contador]" id="exampleInputEmail1" placeholder="AÑO INICIAL" value="<?echo $row_certificacion['ano_inicial']?>" readonly></td>
              <td><input type="text" class="form-control" name="interrumpida[$contador]" id="exampleInputEmail1" placeholder="¿HA SIDO INTERRUMPIDA?" value="<?echo $row_certificacion['interrumpida']?>" readonly></td>
            </tr>
          <?php $contador++; } ?> 
          
   
				</table>			
			</td>
		</tr>
		<tr>
			<td colspan="4">
				10.DE LAS CERTIFICACIONES CON LAS QUE CUENTA, EN SU MÁS RECIENTE EVALUACIÓN INTERNA Y EXTERNA, ¿CUÁNTOS INCUMPLIMIENTOS SE IDENTIFICARON? Y EN SU CASO, ¿ESTÁN RESUELTOS O CUÁL ES SU ESTADO?
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<textarea class="form-control" name="op_resp10" id="" rows="3" readonly><?php echo $row_solicitud['op_resp10']?></textarea>
			</td>
		</tr>	
		<tr>
			<td colspan="4">
				11.DEL TOTAL DE SUS VENTAS ¿QUÉ PORCENTAJE DEL PRODUCTO CUENTA CON LA CERTIFICACIÓN DE ORGÁNICO, COMERCIO JUSTO Y/O SÍMBOLO DE PEQUEÑOS PRODUCTORES?
			</td>
		</tr>	
		<tr>
			<td colspan="4">
				<textarea class="form-control" name="op_resp11" id="" rows="3" readonly><?php echo $row_solicitud['op_resp11']?></textarea>
			</td>
		</tr>	
		<tr>
			<td colspan="4">
				12. ¿TUVO VENTAS SPP DURANTE EL CICLO DE CERTIFICACIÓN ANTERIOR?
			</td>
		</tr>
		<tr>
      <td colspan="4">
        <?php
          if($row_solicitud['op_resp12'] == 'SI'){
              //echo "SI <input type='radio' name='op_resp12'  checked readonly>";
            /*echo "</div>";
            echo "<div class='col-xs-6'>";
              echo "<p class='text-center alert alert-danger'>NO</p>";
              echo "NO <input type='radio' name='op_resp12'  readonly>";
            echo "</div>";*/
        ?>
          <div class="col-xs-6">
            <p class='text-center alert alert-success'><span class='glyphicon glyphicon-ok' aria-hidden='true'></span> SI</p>
          </div>
          <div class="col-xs-6">
            <?php 
              if(empty($row_solicitud['op_resp13'])){
             ?>
              <p class="alert alert-danger">No se proporciono ninguna respuesta.</p>
            <?php 
              }else if($row_solicitud['op_resp13'] == "HASTA $3,000 USD"){
             ?>
              <p class="alert alert-info">HASTA $3,000 USD</p>
            <?php 
              }else if($row_solicitud['op_resp13'] == "ENTRE $3,000 Y $10,000 USD"){
             ?>
             <p class="alert alert-info">ENTRE $3,000 Y $10,000 USD</p>
            <?php 
              }else if($row_solicitud['op_resp13'] == "ENTRE $10,000 A $25,000 USD"){
             ?>
             <p class="alert alert-info">ENTRE $10,000 A $25,000 USD</p>
            <?php 
              }else if($row_solicitud['op_resp13'] != "HASTA $3,000 USD" && $row_solicitud['op_resp13'] != "ENTRE $3,000 Y $10,000 USD" && $row_solicitud['op_resp13'] != "ENTRE $10,000 A $25,000 USD"){
             ?>
             <p class="alert alert-info"><?php echo $row_solicitud['op_resp13']; ?></p>
            <?php 
              }
             ?>
          </div>
        <?php
          }else if($row_solicitud['op_resp12'] == 'NO'){
        ?>
          <div class="col-xs-12">
            <p class='text-center alert alert-danger'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span> NO</p>
          </div>
        
        <?php         
          }
        ?>
      </td>
		</tr>
	
 
  
		<tr>
			<td colspan="4">
				14. FECHA ESTIMADA PARA COMENZAR A USAR EL SÍMBOLO DE PEQUEÑOS PRODUCTORES.
			</td>
		</tr>	
		<tr>
			<td colspan="4">
				<textarea class="form-control" name="op_resp14" id="" rows="3" readonly><?php echo $row_solicitud['op_resp14']?></textarea>
			</td>
		</tr>	
		<tr>
			<td colspan="4">
				15. ANEXAR EL CROQUIS GENERAL DE SU OPP, INDICANDO LAS ZONAS EN DONDE CUENTA CON SOCIOS.
			</td>
		</tr>	
		<tr>
      <?php   $sizeRuta = strlen("../../croquis/"); ?>  
      <?php if(strlen($row_solicitud["op_resp15"])<=$sizeRuta){ ?>
        <td colspan="4">
          
         <p class="alert alert-danger">No Disponible</p>
          
        </td>
      <?php }else{ ?>
        <td colspan="4">
   
          <a class="btn btn-success" href="<?echo $row_solicitud['op_resp15']?>"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Descargar Croquis</a>
      
        </td>
      <?php } ?>
		</tr>	
		<tr class="success">
			<th colspan="4" class="text-center">DATOS DE PRODUCTOS PARA LOS CUALES QUIERE UTILIZAR EL SÍMBOLO<sup>6</sup></th>
		</tr>



		<tr>
			<td colspan="4">
				<table class="table table-bordered" id="tablaProductos">
					<tr>
						<td>Producto</td>
						<td>Volumen Total Estimado a Comercializar</td>
						<td>Producto Terminado</td>
						<td>Materia Prima</td>
						<td>País(es) de Destino</td>
						<td>Marca Propia</td>
						<td>Marca de un Cliente</td>
						<td>Sin cliente aún</td>
						<!--<td>
							<button type="button" onclick="tablaProductos()" class="btn btn-primary" aria-label="Left Align">
							  <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
							</button>
							
						</td>-->					
					</tr>

          <?php 
          $query_producto_detalle = "SELECT * FROM productos WHERE idsolicitud_certificacion = $_GET[idsolicitud]";
          $producto_detalle = mysql_query($query_producto_detalle, $dspp) or die(mysql_error());
          $contador = 0;
          while($row_producto = mysql_fetch_assoc($producto_detalle)){
            ?>

					<tr>
						<td>
							<input type="text" class="form-control" name="producto[$contador]" id="exampleInputEmail1" placeholder="Producto" value="<?echo $row_producto['producto']?>" readonly>
						</td>
						<td>
							<input type="text" class="form-control" name="volumen[$contador]" id="exampleInputEmail1" placeholder="Volumen" value="<?echo $row_producto['volumen']?>" readonly>
						</td>
						<td>
              <?php 
                if($row_producto['terminado'] == 'SI'){
                  echo "SI <input type='radio'  name='terminado".$contador."[$contador]' id=' value='SI' checked readonly><br>";
                }else if($row_producto['terminado'] == 'NO'){
                  echo "NO <input type='radio'  name='terminado".$contador."[$contador]' id=' value='NO' checked readonly>";
                }
               ?>
            </td>          
						<td>

							<textarea cols="30" rows="5" type="text" class="form-control" name="materia[$contador]" id="exampleInputEmail1" placeholder="Materia" readonly><?echo $row_producto['materia']?></textarea>
						</td>
						<td>
							<textarea cols="30" rows="5" type="text" class="form-control" name="destino[$contador]" id="exampleInputEmail1" placeholder="Destino" readonly><?echo $row_producto['destino']?></textarea>
						</td>
						<td>
              <?php 
                if($row_producto['marca_propia'] == 'SI'){
                  echo "SI <input type='radio'  name='marca_propia".$contador."[0]' id=' value='SI' checked readonly><br>";
                }else if($row_producto['marca_propia'] == 'NO'){
                  echo "NO <input type='radio'  name='marca_propia".$contador."[0]' id=' value='NO' checked readonly>";
                }
               ?>
						</td>
						<td>
              <?php 
                if($row_producto['marca_cliente'] == 'SI'){
                  echo "SI <input type='radio'  name='marca_cliente".$contador."[0]' id=' value='SI' checked readonly><br>";
                }else if($row_producto['marca_cliente'] == 'NO'){
                  echo "NO <input type='radio'  name='marca_cliente".$contador."[0]' id=' value='NO' checked readonly>";
                }
               ?>              
						</td>
						<td>
              <?php 
                if($row_producto['sin_cliente'] == 'SI'){
                  echo "SI <input type='radio'  name='sin_cliente".$contador."[0]' id=' value='SI' checked readonly><br>";
                }else if($row_producto['sin_cliente'] == 'NO'){
                  echo "NO <input type='radio'  name='sin_cliente".$contador."[0]' id=' value='NO' checked readonly>";
                }
               ?> 
						</td>
					</tr>

          <?php $contador++; }?>				
					<tr>
						<td colspan="8">
							<h6><sup>6</sup> La información proporcionada en esta sección será tratada con plena confidencialidad. Favor de insertar filas adicionales de ser necesario.</h6>
						</td>
					</tr>
				</table>
			</td>

		</tr>
		<tr>
			<th class="success" colspan="4">
				COMPROMISOS
			</th>
		</tr>
		<tr class="text-justify">
			<td colspan="4">
				1. Con el envío de esta solicitud se manifiesta el interés de recibir una propuesta de Certificación.<br>
				2. El proceso de Certificación comenzará en el momento que se confirme la recepción del pago correspondiente.<br>
				3. La entrega y recepción de esta solicitud no garantiza que el proceso de Certificación será positivo.<br>
				4. Conocer y dar cumplimiento a todos los requisitos de la Norma General del Símbolo de Pequeños Productores que le apliquen como Organización de Pequeños Productores, tanto Críticos como Mínimos, independientemente del tipo de evaluación que se realice.
			</td>
		</tr>
		<tr>
			<td colspan="2">
				Nombre de la persona que se responsabiliza de la veracidad de la información del formato y que le dará seguimiento a la solicitud de parte del solicitante:
			</td>
			<td colspan="2">
				<?php if(isset($row_solicitud['responsable'])){ ?>
				<input type="text" class="form-control" name="responsable" value="<?php echo $row_solicitud['responsable']?>" readonly>
				<?}else{?>
				En Proceso
				<?}?>
			</td>
		</tr>
    <tr>
      <td colspan="2">
        OC que recibe la solicitud:
      </td>
      <td colspan="2">
        <input type="text" class="form-control" name="personal_oc" value="<?echo $row_solicitud['nombreOC']?>" readonly>
      </td>
    </tr>  
    
	</table>
	<input type="hidden" name="MM_insert" value="form1">
	<input type="hidden" name="fecha_elaboracion" value="<?php echo time()?>">
	<input type="hidden" name="mensaje" value="Acción agregada correctamente" />
	<input type="hidden" name="idopp" value="<?php echo $_SESSION['idopp']?>">
	<!--<input class="btn btn-primary" type="submit" value="Enviar Solicitud">-->

</form>



 <script>
var contador=0;
	function tablaCertificaciones()
	{
		contador++;
	var table = document.getElementById("tablaCertificaciones");
	  {
	  var row = table.insertRow(2);
	  var cell1 = row.insertCell(0);
	  var cell2 = row.insertCell(1);
	  var cell3 = row.insertCell(2);
	  var cell4 = row.insertCell(3);

	  cell1.innerHTML = '<input type="text" class="form-control" name="certificadora['+contador+']" id="exampleInputEmail1" placeholder="CERTIFICACIÓN">';
	  cell2.innerHTML = '<input type="text" class="form-control" name="certificacion['+contador+']" id="exampleInputEmail1" placeholder="CERTIFICADORA">';
	  cell3.innerHTML = '<input type="date" class="form-control" name="ano_inicial['+contador+']" id="exampleInputEmail1" placeholder="AÑO INICIAL">';
	  cell4.innerHTML = '<input type="text" class="form-control" name="interrumpida['+contador+']" id="exampleInputEmail1" placeholder="¿HA SIDO INTERRUMPIDA?">';	  
	  }
	}	

  function mostrar(){
    document.getElementById('oculto').style.display = 'block';
  }
  function ocultar()
  {
    document.getElementById('oculto').style.display = 'none';
  }

	function mostrar_ventas(){
		document.getElementById('tablaVentas').style.display = 'block';
	}
	function ocultar_ventas()
	{
		document.getElementById('tablaVentas').style.display = 'none';
	}		

	var cont=0;
	function tablaProductos()
	{

	var table = document.getElementById("tablaProductos");
	  {
	cont++;

	  var row = table.insertRow(1);
	  var cell1 = row.insertCell(0);
	  var cell2 = row.insertCell(1);
	  var cell3 = row.insertCell(2);
	  var cell4 = row.insertCell(3);
	  var cell5 = row.insertCell(4);
	  var cell6 = row.insertCell(5);
	  var cell7 = row.insertCell(6); 
	  var cell8 = row.insertCell(7); 	   	  

	  

	  cell1.innerHTML = '<input type="text" class="form-control" name="producto['+cont+']" id="exampleInputEmail1" placeholder="Producto">';
	  
	  cell2.innerHTML = '<input type="text" class="form-control" name="volumen['+cont+']" id="exampleInputEmail1" placeholder="Volumen">';
	  
	  cell3.innerHTML = 'SI <input type="radio" name="terminado'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="terminado'+cont+'['+cont+']" id="" value="NO">';
	  
	  cell4.innerHTML = '<input type="text" class="form-control" name="materia['+cont+']" id="exampleInputEmail1" placeholder="Materia">';
	  
	  cell5.innerHTML = '<input type="text" class="form-control" name="destino['+cont+']" id="exampleInputEmail1" placeholder="Destino">';
	  
	  cell6.innerHTML = 'SI <input type="radio" name="marca_propia'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="marca_propia'+cont+'['+cont+']" id="" value="NO">';
	  
	  cell7.innerHTML = 'SI <input type="radio" name="marca_cliente'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="marca_cliente'+cont+'['+cont+']" id="" value="NO">';
	  
	  cell8.innerHTML = 'SI <input type="radio" name="sin_cliente'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="sin_cliente'+cont+'['+cont+']" id="" value="NO">';	  

	  }

	}	

</script>

</div>
<?php
mysql_free_result($opp);

mysql_free_result($cta_bn);

mysql_free_result($contacto);

mysql_free_result($oc);

mysql_free_result($pais);

mysql_free_result($contacto_detail);

mysql_free_result($cta_bn_detail);

mysql_free_result($accion_detalle);

mysql_free_result($accion_detail);

mysql_free_result($accion_lateral);
?>
