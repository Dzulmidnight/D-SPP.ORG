<?php require_once('../Connections/dspp.php'); ?>
<?php
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
  $insertSQL = sprintf("INSERT INTO contacto (idcom, contacto, cargo, tipo, telefono1, telefono2, email1, email2) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['idcom'], "int"),
                       GetSQLValueString($_POST['contacto'], "text"),
                       GetSQLValueString($_POST['cargo'], "text"),
                       GetSQLValueString($_POST['tipo'], "text"),
                       GetSQLValueString($_POST['telefono1'], "text"),
                       GetSQLValueString($_POST['telefono2'], "text"),
                       GetSQLValueString($_POST['email1'], "text"),
                       GetSQLValueString($_POST['email2'], "text"));

  mysql_select_db($database_dspp, $dspp);
  $Result1 = mysql_query($insertSQL, $dspp) or die(mysql_error());
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form3")) {
  $updateSQL = sprintf("UPDATE contacto SET idcom=%s, contacto=%s, cargo=%s, tipo=%s, telefono1=%s, telefono2=%s, email1=%s, email2=%s WHERE idcontacto=%s",
                       GetSQLValueString($_POST['idcom'], "int"),
                       GetSQLValueString($_POST['contacto'], "text"),
                       GetSQLValueString($_POST['cargo'], "text"),
                       GetSQLValueString($_POST['tipo'], "text"),
                       GetSQLValueString($_POST['telefono1'], "text"),
                       GetSQLValueString($_POST['telefono2'], "text"),
                       GetSQLValueString($_POST['email1'], "text"),
                       GetSQLValueString($_POST['email2'], "text"),
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
  $updateSQL = sprintf("UPDATE com SET idf=%s, password=%s, nombre=%s, abreviacion=%s, sitio_web=%s, telefono=%s, email=%s, pais=%s, ciudad=%s, idoc=%s, direccion=%s, direccion_fiscal=%s, rfc=%s, ruc=%s WHERE idcom=%s",
                       GetSQLValueString($_POST['idf'], "text"),
                       GetSQLValueString($_POST['password'], "text"),
                       GetSQLValueString($_POST['nombre'], "text"),
                       GetSQLValueString($_POST['abreviacion'], "text"),
                       GetSQLValueString($_POST['sitio_web'], "text"),
                       GetSQLValueString($_POST['telefono'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['pais'], "text"),
                       GetSQLValueString($_POST['ciudad'], "text"),
                       GetSQLValueString($_POST['idoc'], "int"),
                       GetSQLValueString($_POST['direccion'], "text"),
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
  
  <div class="col-xs-4">
    
  <? if(isset($_POST['update'])){?>
  <p>
  <div class="alert alert-success" role="alert"><? echo $_POST['update'];?></div>
  </p>
  <? }?>
    
  <form class="form" action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
    <table class="table">
      <tr valign="baseline">
        <th nowrap="nowrap" align="right">IDF</th>
        <input class="form-control" type="hidden" name="idf" value="<?php echo htmlentities($row_com['idf'], ENT_COMPAT, 'UTF-8'); ?>" size="32"/>
        <td>
          <?php echo $row_com['idf']; ?>   
        </td>
       
        </tr>
      <tr valign="baseline">
        <th nowrap="nowrap" align="right">Nombre</th>
        <td><input class="form-control" type="text" name="nombre" value="<?php echo htmlentities($row_com['nombre'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
        </tr>
      <tr valign="baseline">
        <th nowrap="nowrap" align="right">Password</th>
        <td><input class="form-control" type="text" name="password" value="<?php echo htmlentities($row_com['password'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
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
            <select class="form-control" name="pais">
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
      <tr valign="baseline">
        <th nowrap="nowrap" align="right">Ciudad</th>
        <td><input class="form-control" type="text" name="ciudad" value="<?php echo htmlentities($row_com['ciudad'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
        </tr>
      <tr valign="baseline">
        <th nowrap="nowrap" align="right">IDF OC</th>
        <td><select  class="form-control" name="idoc">
          <option value="">Selecciona</option>
          <?php 
            while ($row_oc = mysql_fetch_assoc($oc)) {  
            ?>
              <option value="<?php echo $row_oc['idoc']?>" <?php if (!(strcmp($row_oc['idoc'], htmlentities($row_com['idoc'], ENT_COMPAT, 'UTF-8')))) {echo "SELECTED";} ?>><?php echo $row_oc['nombre']?></option>
              <?php
            } 
          ?>
          </select></td>
        </tr>
      <tr valign="baseline">
        <th nowrap="nowrap" align="right">Dirección_Oficinas</th>
        <td><input class="form-control" type="text" name="direccion" value="<?php echo htmlentities($row_com['direccion'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
        </tr>
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
    <input type="hidden" name="update" value="com actualizado correctamente" />
    <input type="hidden" name="MM_update" value="form1" />
    <input type="hidden" name="idcom" value="<?php echo $row_com['idcom']; ?>" />
  </form>
  </div>
  <div class="col-xs-8">
    
  <ul class="nav nav-pills">
 
  <li role="presentation"
<? if(isset($_GET['contact'])){?> class="active" <? }?>><a href="?COM&detail&idcom=<? echo $_GET['idcom'];?>&contact">Contactos</a></li>
  
  <li role="presentation"
<? if(isset($_GET['cta'])){?> class="active" <? }?>><a href="?COM&detail&idcom=<? echo $_GET['idcom'];?>&cta">Cuentas bancarias</a></li>

 <li role="presentation"
<? if(isset($_GET['action'])){?> class="active" <? }?>><a href="?COM&detail&idcom=<? echo $_GET['idcom'];?>&action">Ultimas acciones</a></li>
    
    
  </ul>
  <hr />
    
    
  <? if(isset($_GET['contact'])){?>
  <h4><a class="btn btn-success" href="?COM&detail&idcom=<? echo $_GET['idcom'];?>&contact=add">Agregar</a> <a class="btn btn-default" href="?COM&detail&idcom=<? echo $_GET['idcom'];?>&contact">Contactos</a></h4>
  
  <? if(isset($_POST['mensaje'])){?>
  <p>
  <div class="alert alert-success" role="alert"><? echo $_POST['mensaje'];?></div>
  </p>
  <? }?>
  
  <? if($_GET['contact']=="add"){?>
  <form class="col-xs-8" action="<?php echo $editFormAction; ?>" method="post" name="form2" id="form2">
    <table class="table">
      <tr valign="baseline">
        <th nowrap="nowrap" align="right">Contacto</th>
        <td><input required="required" autofocus="autofocus" class="form-control"  type="text" name="contacto" value="" size="32" /></td>
        </tr>
      <tr valign="baseline">
        <th nowrap="nowrap" align="right">Cargo</th>
        <td><input  class="form-control" type="text" name="cargo" value="" size="32" /></td>
        </tr>
      <tr valign="baseline">
        <th nowrap="nowrap" align="right">Tipo</th>
        <td><select required  class="form-control"  name="tipo">
          <option value="" >Selecciona</option>
          <option value="ADMINISTRACION" <?php if (!(strcmp("ADMINISTRACION", ""))) {echo "SELECTED";} ?>>ADMINISTRACION</option>
          <option value="CERTIFICACION" <?php if (!(strcmp("CERTIFICACION", ""))) {echo "SELECTED";} ?>>CERTIFICACION</option>
          </select></td>
        </tr>
      <tr valign="baseline">
        <th nowrap="nowrap" align="right">Teléfono 1</th>
        <td><input  class="form-control" type="text" name="telefono1" value="" size="32" /></td>
        </tr>
      <tr valign="baseline">
        <th nowrap="nowrap" align="right">Teléfono 2</th>
        <td><input  class="form-control" type="text" name="telefono2" value="" size="32" /></td>
        </tr>
      <tr valign="baseline">
        <th nowrap="nowrap" align="right">Email 1</th>
        <td><input  class="form-control" type="email" name="email1" value="" size="32" /></td>
        </tr>
      <tr valign="baseline">
        <th nowrap="nowrap" align="right">Email 2</th>
        <td><input  class="form-control" type="email" name="email2" value="" size="32" /></td>
        </tr>
      <tr valign="baseline">
        <th nowrap="nowrap" align="right">&nbsp;</th>
        <td><input class="btn btn-primary" type="submit" value="Agregar contacto" /></td>
        </tr>
      </table>
      <input type="hidden" name="mensaje" value="Contacto agregado correctamente" />
    <input type="hidden" name="idcom" value="<? echo $_GET['idcom'];?>" />
    <input type="hidden" name="MM_insert" value="form2" />
  </form>


<table>
  <tr>
  <td width="20"><?php if ($pageNum_com > 0) { // Show if not first page ?>
  <a href="<?php printf("%s?pageNum_com=%d%s", $currentPage, 0, $queryString_com); ?>">
  <span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span>
  </a>
  <?php } // Show if not first page ?></td>
  <td width="20"><?php if ($pageNum_com > 0) { // Show if not first page ?>
  <a href="<?php printf("%s?pageNum_com=%d%s", $currentPage, max(0, $pageNum_com - 1), $queryString_com); ?>">
  <span class="glyphicon glyphicon-backward" aria-hidden="true"></span>
  </a>
  <?php } // Show if not first page ?></td>
  <td width="20"><?php if ($pageNum_com < $totalPages_com) { // Show if not last page ?>
  <a href="<?php printf("%s?pageNum_com=%d%s", $currentPage, min($totalPages_com, $pageNum_com + 1), $queryString_com); ?>">
  <span class="glyphicon glyphicon-forward" aria-hidden="true"></span>
  </a>
  <?php } // Show if not last page ?></td>
  <td width="20"><?php if ($pageNum_com < $totalPages_com) { // Show if not last page ?>
  <a href="<?php printf("%s?pageNum_com=%d%s", $currentPage, $totalPages_com, $queryString_com); ?>">
  <span class="glyphicon glyphicon-fast-forward" aria-hidden="true"></span>
  </a>
  <?php } // Show if not last page ?></td>
  </tr>
</table>



  <? }else if($_GET['contact']=="update"){?>
  <div class="col-xs-4">
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Lista de contactos</th>
        </tr>
      </thead>
    <?php  while ($row_contacto = mysql_fetch_assoc($contacto)){ ?>
    <tr>
      <td><a class="btn btn-<? if($_GET['idcontacto']==$row_contacto['idcontacto']){echo "danger";}else{echo "default";}?>" href="?COM&detail&idcom=<? echo $_GET['idcom'];?>&contact=update&idcontacto=<?php echo $row_contacto['idcontacto']; ?>" style="width:100%"><?php echo $row_contacto['contacto']; ?></a></td>
      </tr>
    <?php } ?>
  </table>
  </div>
  <div class="col-xs-8">
  <form action="<?php echo $editFormAction; ?>" method="post" name="form3" id="form3">
  <table class="table">
  <thead>
      <tr>
        <th colspan="2">Actualizar contacto</th>
        </tr>
      </thead>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Contacto</th>
      <td><input  class="form-control" type="text" name="contacto" value="<?php echo htmlentities($row_contacto_detail['contacto'], ENT_COMPAT, ''); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Cargo</th>
      <td><input  class="form-control" type="text" name="cargo" value="<?php echo htmlentities($row_contacto_detail['cargo'], ENT_COMPAT, ''); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Tipo</th>
      <td><select  class="form-control" name="tipo">
        <option value="" >Selecciona</option>
        <option value="ADMINISTRACION" <?php if (!(strcmp("ADMINISTRACION", htmlentities($row_contacto_detail['tipo'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>ADMINSITRACION</option>
        <option value="CERTIFICACION" <?php if (!(strcmp("CERTIFICACION", htmlentities($row_contacto_detail['tipo'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>CERTIFICACION</option>
      </select></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Teléfono 1</th>
      <td><input  class="form-control" type="text" name="telefono1" value="<?php echo htmlentities($row_contacto_detail['telefono1'], ENT_COMPAT, ''); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Teléfono 2</th>
      <td><input  class="form-control" type="text" name="telefono2" value="<?php echo htmlentities($row_contacto_detail['telefono2'], ENT_COMPAT, ''); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Email 1</th>
      <td><input  class="form-control" type="email" name="email1" value="<?php echo htmlentities($row_contacto_detail['email1'], ENT_COMPAT, ''); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Email 2</th>
      <td><input  class="form-control" type="email" name="email2" value="<?php echo htmlentities($row_contacto_detail['email2'], ENT_COMPAT, ''); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">&nbsp;</th>
      <td><input class="btn btn-primary" type="submit" value="Actualizar datos" /></td>
    </tr>
  </table>
  <input type="hidden" name="mensaje" value="Contacto actualizado correctamente" />
  <input type="hidden" name="idcontacto" value="<?php echo $row_contacto_detail['idcontacto']; ?>" />
  <input type="hidden" name="idcom" value="<?php echo htmlentities($row_contacto_detail['idcom'], ENT_COMPAT, ''); ?>" />
  <input type="hidden" name="MM_update" value="form3" />
  <input type="hidden" name="idcontacto" value="<?php echo $row_contacto_detail['idcontacto']; ?>" />
</form>
  </div>
  <? }else{?>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Contacto</th>
        <th>Cargo</th>
        <th>Tipo</th>
        <th>Teléfonos</th>
        <th>Email</th>
        <th>Eliminar</th>
        </tr>
      </thead>
    <?php  $cont=0; while ($row_contacto = mysql_fetch_assoc($contacto)){ $cont++;?>
    <tr>
      <td><a class="btn btn-default" href="?COM&detail&idcom=<? echo $_GET['idcom'];?>&contact=update&idcontacto=<?php echo $row_contacto['idcontacto']; ?>" style="width:100%"><?php echo $row_contacto['contacto']; ?></a></td>
      <td><?php echo $row_contacto['cargo']; ?></td>
      <td><?php echo $row_contacto['tipo']; ?></td>
      <td><?php echo $row_contacto['telefono1']; ?><br /><?php echo $row_contacto['telefono2']; ?></td>
      <td><?php echo $row_contacto['email1']; ?><br /><?php echo $row_contacto['email2']; ?></td>
      <td>
      <form action="" method="post">
      <input class="btn btn-danger" type="submit" value="Eliminar" />
      <input type="hidden" value="Contacto eliminado correctamente" name="mensaje" />
      <input type="hidden" value="1" name="contacto_delete" />
      <input type="hidden" value="<?php echo $row_contacto['idcontacto']; ?>" name="idcontacto" />
      </form>
      </td>
      </tr>
    <?php } ?>
    <? if($cont==0){?>
    <tr><td colspan="7" class="alert alert-info" role="alert">No se encontraron registros</td></tr>
    <? }?>
  </table>



  <? }?>
    
  <? }?>
  <? if(isset($_GET['cta'])){?>
  
  <h4><a class="btn btn-success" href="?COM&detail&idcom=<? echo $_GET['idcom'];?>&cta=add">Agregar</a> <a class="btn btn-default" href="?COM&detail&idcom=<? echo $_GET['idcom'];?>&cta">Cuentas bancarias</a></h4>
  
  <? if(isset($_POST['mensaje'])){?>
  <p>
  <div class="alert alert-success" role="alert"><? echo $_POST['mensaje'];?></div>
  </p>
  <? }?>
  
  <? if($_GET['cta']=="add"){?>
  <form class="col-xs-8" action="<?php echo $editFormAction; ?>" method="post" name="form4" id="form4">
  <table class="table">
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Banco</th>
      <td><input autofocus="autofocus"  class="form-control" type="text" name="banco" value="" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Sucursal</th>
      <td><input  class="form-control" type="text" name="sucursal" value="" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Cuenta</th>
      <td><input  class="form-control" type="text" name="cuenta" value="" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">CLABE</th>
      <td><input  class="form-control" type="text" name="clabe" value="" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Propietario</th>
      <td><input  class="form-control" type="text" name="propietario" value="" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td><input class="btn btn-primary" type="submit" value="Agregar cuenta" /></td>
    </tr>
  </table>
  <input type="hidden" name="mensaje" value="Cuenta agregada correctamente" />
  <input type="hidden" name="idcom" value="<? echo $_GET['idcom'];?>" />
  <input  type="hidden" name="MM_insert" value="form4" />
</form>
<? }else if($_GET['cta']=="update"){?>
<div class="col-xs-4">
<table class="table table-striped">
    <thead>
      <tr>
        <th>Banco</th>
        </tr>
      </thead>
    <tbody>
      <?php $cont=0; while ($row_cta_bn = mysql_fetch_assoc($cta_bn)){ $cont++;?>
      <tr>
        <td><a class="btn btn-<? if($_GET['idcta_bn']==$row_cta_bn['idcta_bn']){echo "danger";}else{echo "default";}?>" href="?COM&detail&idcom=<? echo $_GET['idcom'];?>&cta=update&idcta_bn=<?php echo $row_cta_bn['idcta_bn']; ?>" style="width:100%"><?php echo $row_cta_bn['banco']; ?></a></td>
        </tr>
      <?php }  ?>
      <? if($cont==0){?>
    <tr><td colspan="5" class="alert alert-info" role="alert">No se encontraron registros</td></tr>
    <? }?>
      </tbody>
  </table>
</div>
<div class="col-xs-8">

<form action="<?php echo $editFormAction; ?>" method="post" name="form5" id="form5">
  <table class="table">
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Banco</th>
      <td><input class="form-control" type="text" name="banco" value="<?php echo htmlentities($row_cta_bn_detail['banco'], ENT_COMPAT, ''); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Sucursal</th>
      <td><input class="form-control" type="text" name="sucursal" value="<?php echo htmlentities($row_cta_bn_detail['sucursal'], ENT_COMPAT, ''); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Cuenta</th>
      <td><input class="form-control" type="text" name="cuenta" value="<?php echo htmlentities($row_cta_bn_detail['cuenta'], ENT_COMPAT, ''); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">CLABE</th>
      <td><input class="form-control" type="text" name="clabe" value="<?php echo htmlentities($row_cta_bn_detail['clabe'], ENT_COMPAT, ''); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Propietario</th>
      <td><input class="form-control" type="text" name="propietario" value="<?php echo htmlentities($row_cta_bn_detail['propietario'], ENT_COMPAT, ''); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">&nbsp;</th>
      <td><input class="form-control" type="submit" value="Actualizar datos" /></td>
    </tr>
  </table>
  <input type="hidden" name="mensaje" value="Cuenta actualizada correctamente" />
  <input type="hidden" name="idcta_bn" value="<?php echo $row_cta_bn_detail['idcta_bn']; ?>" />
  <input type="hidden" name="idcom" value="<?php echo htmlentities($row_cta_bn_detail['idcom'], ENT_COMPAT, ''); ?>" />
  <input type="hidden" name="MM_update" value="form5" />
  <input type="hidden" name="idcta_bn" value="<?php echo $row_cta_bn_detail['idcta_bn']; ?>" />
</form>
</div>
<? }else{?>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Banco</th>
        <th>Sucursal</th>
        <th>Cuenta</th>
        <th>CLABE</th>
        <th>Propietario</th>
        <th>Eliminar</th>
        </tr>
      </thead>
    <tbody>
      <?php $cont=0; while ($row_cta_bn = mysql_fetch_assoc($cta_bn)){ $cont++;?>
      <tr>
        <td><a class="btn btn-default" href="?COM&detail&idcom=<? echo $_GET['idcom'];?>&cta=update&idcta_bn=<?php echo $row_cta_bn['idcta_bn']; ?>" style="width:100%"><?php echo $row_cta_bn['banco']; ?></a></td>
        <td><?php echo $row_cta_bn['sucursal']; ?></td>
        <td><?php echo $row_cta_bn['cuenta']; ?></td>
        <td><?php echo $row_cta_bn['clabe']; ?></td>
        <td><?php echo $row_cta_bn['propietario']; ?></td>
        <td>
      <form action="" method="post">
      <input class="btn btn-danger" type="submit" value="Eliminar" />
      <input type="hidden" value="Cuenta eliminada correctamente" name="mensaje" />
      <input type="hidden" value="1" name="cta_bn_delete" />
      <input type="hidden" value="<?php echo $row_cta_bn['idcta_bn']; ?>" name="idcta_bn" />
      </form>
      </td>
        </tr>
      <?php }  ?>
      <? if($cont==0){?>
    <tr><td colspan="6" class="alert alert-info" role="alert">No se encontraron registros</td></tr>
    <? }?>
      </tbody>
  </table>
  <? }?>
    
  <? }?>
  
  <? if(isset($_GET['action'])){?>
  
  <h4><a class="btn btn-success" href="?COM&detail&idcom=<? echo $_GET['idcom'];?>&action=add">Agregar</a> <a class="btn btn-default" href="?COM&detail&idcom=<? echo $_GET['idcom'];?>&action">Ultimas acciones</a></h4>
  
  <? if(isset($_POST['mensaje'])){?>
  <p>
  <div class="alert alert-success" role="alert"><? echo $_POST['mensaje'];?></div>
  </p>
  <? }?>
  
  <? if($_GET['action']=="add"){?>
  <form class="col-xs-8" action="<?php echo $editFormAction; ?>" method="post" name="form6" id="form6">
  <table class="table">
  
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Ultima acción</th>
      <td><input required="required" autofocus="autofocus"  class="form-control" type="text" name="ultima_accion" value="" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Persona</th>
      <td><input required="required" class="form-control"  type="text" name="persona" value="" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Fecha</th>
      <td><input required="required" class="form-control"  type="date" name="fecha" value="" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Observación</th>
      <td><input  class="form-control" type="text" name="observacion" value="" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">&nbsp;</th>
      <td><input class="btn btn-primary" type="submit" value="Agregar accion" /></td>
    </tr>
  </table>
  <input type="hidden" name="mensaje" value="Acción agregada correctamente" />
  <input type="hidden" name="idcom" value="<? echo $_GET['idcom'];?>" />
  <input type="hidden" name="MM_insert" value="form6" />
</form>
<p>&nbsp;</p>
<? }else if($_GET['action']=="update"){?>
<div class="col-xs-4">
  <table class="table table-striped">
   <thead>
    <tr>
      <th colspan="2">Ultima Acción</th>
    </tr>
      </thead>  
    <?php do { ?>
      <tr>
        <td><a href="?COM&detail&idcom=<? echo $_GET['idcom']?>&action=update&idultima_accion=<? echo $row_accion_lateral['idultima_accion'];?>" class="btn btn-<? if($_GET['idultima_accion']==$row_accion_lateral['idultima_accion']){echo "danger";}else{echo "default";}?>" style="width:100%"><?php echo $row_accion_lateral['ultima_accion']; ?></a></td>
      </tr>
      <?php } while ($row_accion_lateral = mysql_fetch_assoc($accion_lateral)); ?>
  </table>
</div>
<div class="col-xs-8">

  <form action="<?php echo $editFormAction; ?>" method="post" name="form7" id="form7">
    <table class="table">  <thead>
      <tr>
        <th colspan="2">Actualizar Acción</th>
        </tr>
      </thead>
      
      <tr valign="baseline">
        <th nowrap="nowrap" align="right">Ultima acción</th>
        <td><input type="text" class="form-control" name="ultima_accion" value="<?php echo htmlentities($row_accion_detail['ultima_accion'], ENT_COMPAT, ''); ?>" size="32" /></td>
      </tr>
      <tr valign="baseline">
        <th nowrap="nowrap" align="right">Persona</th>
        <td><input type="text" class="form-control" name="persona" value="<?php echo htmlentities($row_accion_detail['persona'], ENT_COMPAT, ''); ?>" size="32" /></td>
      </tr>
      <tr valign="baseline">
        <th nowrap="nowrap" align="right">Fecha</th>
        <td><input type="date" class="form-control" name="fecha" value="<?php echo htmlentities($row_accion_detail['fecha'], ENT_COMPAT, ''); ?>" size="32" /></td>
      </tr>
      <tr valign="baseline">
        <th nowrap="nowrap" align="right">Observación</th>
        <td><input type="text" class="form-control" name="observacion" value="<?php echo htmlentities($row_accion_detail['observacion'], ENT_COMPAT, ''); ?>" size="32" /></td>
      </tr>
      <tr valign="baseline">
        <th nowrap="nowrap" align="right">&nbsp;</th>
        <td><input type="submit" class="btn btn-primary" value="Actualizar datos" /></td>
      </tr>
    </table>
    <input type="hidden" name="mensaje" value="Acción actualizada correctamente" />
    <input type="hidden" name="MM_update" value="form7" />
    <input type="hidden" name="idultima_accion" value="<?php echo $row_accion_detail['idultima_accion']; ?>" />
  </form>
  <p>&nbsp;</p>
</div>
<? }else{?>
<table class="table table-striped">
<thead>
  <tr>
    <th>ultima acción</th>
    <th>Persona</th>
    <th>Fecha</th>
    <th>Observación</th>
    <th>Eliminar</th>
  </tr>
</thead>
<tbody>
  <?php $cont=0; while ($row_accion_detalle = mysql_fetch_assoc($accion_detalle)){ $cont++; ?>
    <tr>
      <td><a href="?COM&detail&idcom=<? echo $_GET['idcom']?>&action=update&idultima_accion=<? echo $row_accion_detalle['idultima_accion'];?>" class="btn btn-default" style="width:100%"><?php echo $row_accion_detalle['ultima_accion']; ?></a></td>
      <td><?php echo $row_accion_detalle['persona']; ?></td>
      <td><?php echo $row_accion_detalle['fecha']; ?></td>
      <td><?php echo $row_accion_detalle['observacion']; ?></td>
      <td>
      <form action="" method="post">
      <input class="btn btn-danger" type="submit" value="Eliminar" />
      <input type="hidden" value="Acción eliminada correctamente" name="mensaje" />
      <input type="hidden" value="1" name="action_delete" />
      <input type="hidden" value="<?php echo $row_accion_detalle['idultima_accion']; ?>" name="idultima_accion" />
      </form>
      </td>
    </tr>
    <?php }  ?>
    <? if($cont==0){?>
    <tr><td colspan="5" class="alert alert-info" role="alert">No se encontraron registros</td></tr>
    <? }?>
    </tbody>
</table>
<? }?>  



  
  <? }?>
      
    
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