<?php require_once('../Connections/dspp.php'); 
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
  $insertSQL = sprintf("INSERT INTO contacto (idcom, contacto, cargo, tipo, telefono1, telefono2, email1, emaril2) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['idcom'], "int"),
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
  $updateSQL = sprintf("UPDATE contacto SET idcom=%s, contacto=%s, cargo=%s, tipo=%s, telefono1=%s, telefono2=%s, email1=%s, emaril2=%s WHERE idcontacto=%s",
                       GetSQLValueString($_POST['idcom'], "int"),
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
  $updateSQL = sprintf("UPDATE cta_bn SET idcom=%s, banco=%s, sucursal=%s, cuenta=%s, clabe=%s, propietario=%s WHERE idcta_bn=%s",
                       GetSQLValueString($_POST['idcom'], "int"),
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
  $insertSQL = sprintf("INSERT INTO ultima_accion (idcom, ultima_accion, persona, fecha, observacion) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['idcom'], "int"),
                       GetSQLValueString($_POST['ultima_accion'], "text"),
                       GetSQLValueString($_POST['persona'], "text"),
                       GetSQLValueString($_POST['fecha'], "text"),
                       GetSQLValueString($_POST['observacion'], "text"));

  mysql_select_db($database_dspp, $dspp);
  $Result1 = mysql_query($insertSQL, $dspp) or die(mysql_error());
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form4")) {
  $insertSQL = sprintf("INSERT INTO cta_bn (idcom, banco, sucursal, cuenta, clabe, propietario) VALUES (%s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['idcom'], "int"),
                       GetSQLValueString($_POST['banco'], "text"),
                       GetSQLValueString($_POST['sucursal'], "text"),
                       GetSQLValueString($_POST['cuenta'], "text"),
                       GetSQLValueString($_POST['clabe'], "text"),
                       GetSQLValueString($_POST['propietario'], "text"));

  mysql_select_db($database_dspp, $dspp);
  $Result1 = mysql_query($insertSQL, $dspp) or die(mysql_error());
}

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
if (isset($_GET['idcom'])) {
  $colname_accion_detalle = $_GET['idcom'];
}
mysql_select_db($database_dspp, $dspp);
$query_accion_detalle = sprintf("SELECT * FROM ultima_accion WHERE idcom = %s", GetSQLValueString($colname_accion_detalle, "int"));
$query_limit_accion_detalle = sprintf("%s LIMIT %d, %d", $query_accion_detalle, $startRow_accion_detalle, $maxRows_accion_detalle);
$accion_detalle = mysql_query($query_limit_accion_detalle, $dspp) or die(mysql_error());
//$row_accion_detalle = mysql_fetch_assoc($accion_detalle);

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
$query_accion_lateral = "SELECT idultima_accion, idcom, ultima_accion FROM ultima_accion ORDER BY fecha DESC";
$accion_lateral = mysql_query($query_accion_lateral, $dspp) or die(mysql_error());
$row_accion_lateral = mysql_fetch_assoc($accion_lateral);
$totalRows_accion_lateral = mysql_num_rows($accion_lateral);

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE com SET idf=%s, password=%s, nombre=%s, abreviacion=%s, sitio_web=%s, telefono=%s, email=%s, pais=%s, idoc=%s, direccion=%s, direccion_fiscal=%s, rfc=%s, ruc=%s WHERE idcom=%s",
                       GetSQLValueString($_POST['idf'], "text"),
                       GetSQLValueString($_POST['password'], "text"),
                       GetSQLValueString($_POST['nombre'], "text"),
                       GetSQLValueString($_POST['abreviacion'], "text"),
                       GetSQLValueString($_POST['sitio_web'], "text"),
                       GetSQLValueString($_POST['telefono'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['pais'], "text"),
                       GetSQLValueString($_POST['idoc'], "int"),
                       GetSQLValueString($_POST['direccion_oficinas'], "text"),
                       GetSQLValueString($_POST['direccion_fiscal'], "text"),
                       GetSQLValueString($_POST['rfc'], "text"),
                       GetSQLValueString($_POST['ruc'], "text"),
                       GetSQLValueString($_POST['idcom'], "int"));

  $Result1 = mysql_query($updateSQL, $dspp) or die(mysql_error());
}

$colname_com = "-1";
if (isset($_GET['idcom'])) {
  $colname_com = $_GET['idcom'];
}
$query_com = sprintf("SELECT * FROM com WHERE idcom = %s", GetSQLValueString($colname_com, "int"));
$com = mysql_query($query_com, $dspp) or die(mysql_error());
$row_com = mysql_fetch_assoc($com);
$totalRows_com = mysql_num_rows($com);

$colname_cta_bn = "-1";
if (isset($_GET['idcom'])) {
  $colname_cta_bn = $_GET['idcom'];
}
$query_cta_bn = sprintf("SELECT * FROM cta_bn WHERE idcom = %s", GetSQLValueString($colname_cta_bn, "int"));
$cta_bn = mysql_query($query_cta_bn, $dspp) or die(mysql_error());
//$row_cta_bn = mysql_fetch_assoc($cta_bn);
$totalRows_cta_bn = mysql_num_rows($cta_bn);

$colname_contacto = "-1";
if (isset($_GET['idcom'])) {
  $colname_contacto = $_GET['idcom'];
}
$query_contacto = sprintf("SELECT * FROM contacto WHERE idcom = %s ORDER BY tipo ASC, contacto asc", GetSQLValueString($colname_contacto, "int"));
$contacto = mysql_query($query_contacto, $dspp) or die(mysql_error());
//$row_contacto = mysql_fetch_assoc($contacto);
$totalRows_contacto = mysql_num_rows($contacto);

$query_oc = "SELECT * FROM oc WHERE idoc = $_SESSION[idoc] ORDER BY nombre ASC";
$oc = mysql_query($query_oc, $dspp) or die(mysql_error());
$row_oc = mysql_fetch_assoc($oc);
$totalRows_oc = mysql_num_rows($oc);

$query_pais = "SELECT * FROM paises ORDER BY nombre ASC";
$pais = mysql_query($query_pais, $dspp) or die(mysql_error());
//$row_pais = mysql_fetch_assoc($pais);
$totalRows_pais = mysql_num_rows($pais);
?>
<div class="row-xs-12">
  
  <div class="col-xs-12">
    
  <? if(isset($_POST['update'])){?>
  <p>
  <div class="alert alert-success" role="alert"><? echo $_POST['update'];?></div>
  </p>
  <? }?>
    
  <form class="form" action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
    <table class="table">
      <tr valign="baseline">
        <th nowrap="nowrap" align="right">IDF</th>
        <td><input class="form-control" type="text" name="idf" value="<?php echo htmlentities($row_com['idf'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
        </tr>
      <tr valign="baseline">
        <th nowrap="nowrap" align="right">Password</th>
        <td><input class="form-control" type="text" name="password" value="<?php echo htmlentities($row_com['password'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
        </tr>
      <tr valign="baseline">
        <th nowrap="nowrap" align="right">Nombre</th>
        <td><input class="form-control" type="text" name="nombre" value="<?php echo htmlentities($row_com['nombre'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
        </tr>
      <tr valign="baseline">
        <th nowrap="nowrap" align="right">Abreviación</th>
        <td><input class="form-control" type="text" name="abreviacion" value="<?php echo htmlentities($row_com['abreviacion'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
        </tr>
      <tr valign="baseline">
        <th nowrap="nowrap" align="right">Sitio_web</th>
        <td><input class="form-control" type="text" name="sitio_web" value="<?php echo htmlentities($row_com['sitio_web'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
        </tr>
      <tr valign="baseline">
        <th nowrap="nowrap" align="right">Teléfono Oficinas</th>
        <td><input class="form-control" type="text" name="telefono" value="<?php echo htmlentities($row_com['telefono'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
        </tr>
      <tr valign="baseline">
        <th nowrap="nowrap" align="right">Email</th>
        <td><input class="form-control" type="text" name="email" value="<?php echo htmlentities($row_com['email'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
        </tr>
      <tr valign="baseline">
        <th nowrap="nowrap" align="right">País</th>
        <td>
  <select class="form-control" name="pais" readonly>
  <option value="">Selecciona</option>
  <?php 
while ($row_pais = mysql_fetch_assoc($pais)) {  
?>
  <option value="<?php echo utf8_encode($row_pais['nombre']);?>" <?php if(utf8_encode($row_pais['nombre'])==$row_com['pais']){echo "SELECTED";} ?>><?php echo utf8_encode($row_pais['nombre']);?></option>
  <?php
} 
?>
  </select>
  </td>
        </tr>
      <tr> </tr>
      <tr valign="baseline">
        <th nowrap="nowrap" align="right">IDF OC</th>
        <td>
           <input class="form-control" type="text" value="<?php echo htmlentities($row_oc['nombre'], ENT_COMPAT, 'UTF-8'); ?>" size="32" disabled/>
        </td>
      </tr>
      <tr> </tr>
      <tr valign="baseline">
        <th nowrap="nowrap" align="right">Direccion_Oficinas</th>
        <td><input class="form-control" type="text" name="direccion_oficinas" value="<?php echo htmlentities($row_com['direccion'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
        </tr>
      <tr valign="baseline">
      <tr valign="baseline">
        <th nowrap="nowrap" align="right">Direccion_fiscal</th>
        <td><input class="form-control" type="text" name="direccion_fiscal" value="<?php echo htmlentities($row_com['direccion_fiscal'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
        </tr>
      <tr valign="baseline">
        <th nowrap="nowrap" align="right">RFC</th>
        <td><input class="form-control" type="text" name="rfc" value="<?php echo htmlentities($row_com['rfc'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
        </tr>
      <tr valign="baseline">
        <th nowrap="nowrap" align="right">RUC</th>
        <td><input class="form-control" type="text" name="ruc" value="<?php echo htmlentities($row_com['ruc'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
        </tr>
      <tr valign="baseline">
        <th nowrap="nowrap" align="right">&nbsp;</th>
        <td>
          <button class="btn btn-primary" type="submit" value="Actualizar datos">
            <span class="glyphicon glyphicon-repeat" aria-hidden="true"></span> Actualizar Datos
          </button>  
        </td>
        </tr>
      </table>
    <input type="hidden" name="idcom" value="<?php echo $row_com['idcom']; ?>" />
    <input type="hidden" name="idoc" value="<?php echo $row_oc['idoc']?>">
    <input type="hidden" name="update" value="com actualizado correctamente" />
    <input type="hidden" name="MM_update" value="form1" />
    <input type="hidden" name="idcom" value="<?php echo $row_com['idcom']; ?>" />
  </form>
  </div>






</div>
<?php
mysql_free_result($com);

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
