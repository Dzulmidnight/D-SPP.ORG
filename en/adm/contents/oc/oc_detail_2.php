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
  $insertSQL = sprintf("INSERT INTO contacto (idoc, contacto, cargo, tipo, telefono1, telefono2, email1, email2) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['idoc'], "int"),
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form4")) {
  $insertSQL = sprintf("INSERT INTO cta_bn (idoc, banco, sucursal, cuenta, clabe, propietario) VALUES (%s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['idoc'], "int"),
                       GetSQLValueString($_POST['banco'], "text"),
                       GetSQLValueString($_POST['sucursal'], "text"),
                       GetSQLValueString($_POST['cuenta'], "text"),
                       GetSQLValueString($_POST['clabe'], "text"),
                       GetSQLValueString($_POST['propietario'], "text"));

  mysql_select_db($database_dspp, $dspp);
  $Result1 = mysql_query($insertSQL, $dspp) or die(mysql_error());
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form3")) {
  $updateSQL = sprintf("UPDATE contacto SET idoc=%s, contacto=%s, cargo=%s, tipo=%s, telefono1=%s, telefono2=%s, email1=%s, email2=%s WHERE idcontacto=%s",
                       GetSQLValueString($_POST['idoc'], "int"),
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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE oc SET idf=%s, password=%s, nombre=%s, abreviacion=%s, email=%s, email2=%s, pais=%s, fecha_creacion=%s, numero_socios=%s, razon_social=%s, direccion_fiscal=%s, rfc=%s WHERE idoc=%s",
                       GetSQLValueString($_POST['idf'], "text"),
                       GetSQLValueString($_POST['password'], "text"),
                       GetSQLValueString($_POST['nombre'], "text"),
                       GetSQLValueString($_POST['abreviacion'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['email2'], "text"),
                       GetSQLValueString($_POST['pais'], "text"),
                       GetSQLValueString($_POST['fecha_creacion'], "text"),
                       GetSQLValueString($_POST['numero_socios'], "int"),
                       GetSQLValueString($_POST['razon_social'], "text"),
                       GetSQLValueString($_POST['direccion_fiscal'], "text"),
                       GetSQLValueString($_POST['rfc'], "text"),
                       GetSQLValueString($_POST['idoc'], "int"));

  mysql_select_db($database_dspp, $dspp);
  $Result1 = mysql_query($updateSQL, $dspp) or die(mysql_error());
}

if(isset($_POST['contacto_delete'])){
	$query=sprintf("delete from contacto where idcontacto = %s",GetSQLValueString($_POST['idcontacto'], "text"));
	$ejecutar=mysql_query($query,$dspp) or die(mysql_error());
}

if(isset($_POST['cta_bn_delete'])){
	$query=sprintf("delete from cta_bn where idcta_bn = %s",GetSQLValueString($_POST['idcta_bn'], "text"));
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

$colname_OC = "-1";
if (isset($_GET['idoc'])) {
  $colname_OC = $_GET['idoc'];
}
mysql_select_db($database_dspp, $dspp);
$query_OC = sprintf("SELECT * FROM oc WHERE idoc = %s", GetSQLValueString($colname_OC, "int"));
$OC = mysql_query($query_OC, $dspp) or die(mysql_error());
$row_OC = mysql_fetch_assoc($OC);
$totalRows_OC = mysql_num_rows($OC);


$colname_cta_bn = "-1";
if (isset($_GET['idoc'])) {
  $colname_cta_bn = $_GET['idoc'];
}
$query_cta_bn = sprintf("SELECT * FROM cta_bn WHERE idoc = %s", GetSQLValueString($colname_cta_bn, "int"));
$cta_bn = mysql_query($query_cta_bn, $dspp) or die(mysql_error());
//$row_cta_bn = mysql_fetch_assoc($cta_bn);
$totalRows_cta_bn = mysql_num_rows($cta_bn);

$colname_contacto = "-1";
if (isset($_GET['idoc'])) {
  $colname_contacto = $_GET['idoc'];
}
$query_contacto = sprintf("SELECT * FROM contacto WHERE idoc = %s ORDER BY tipo ASC, contacto asc", GetSQLValueString($colname_contacto, "int"));
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

<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
  <table class="table">
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">IDF</td>
      <td><input type="text"  class="form-control" name="idf" value="<?php echo htmlentities($row_OC['idf'], ENT_COMPAT, ''); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Password</td>
      <td><input type="text"  class="form-control" name="password" value="<?php echo htmlentities($row_OC['password'], ENT_COMPAT, ''); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Nombre</td>
      <td><input type="text"  class="form-control" name="nombre" value="<?php echo $row_OC['nombre']; ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Abreviación</td>
      <td><input type="text"  class="form-control" name="abreviacion" value="<?php echo htmlentities($row_OC['abreviacion'], ENT_COMPAT, ''); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Email 1</td>
      <td><input type="text"  class="form-control" name="email" value="<?php echo htmlentities($row_OC['email'], ENT_COMPAT, ''); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Email 2</td>
      <td><input type="text"  class="form-control" name="email2" value="<?php echo htmlentities($row_OC['email2'], ENT_COMPAT, ''); ?>" size="32" /></td>
    </tr>
    
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">País</td>
      <td>
        <select class="form-control" name="pais">
        <option value="">Selecciona</option>
        <?php 
        while ($row_pais = mysql_fetch_assoc($pais)) {  
        ?>
        <option value="<?php echo utf8_encode($row_pais['nombre']);?>" <?php if(utf8_encode($row_pais['nombre'])==$row_OC['pais']){echo "SELECTED";} ?>><?php echo utf8_encode($row_pais['nombre']);?></option>
        <?php
        } 
        ?>
        </select>
      </td>
    </tr>
    
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Fecha de creación</td>
      <td><input type="text"  class="form-control" name="fecha_creacion" value="<?php echo htmlentities($row_OC['fecha_creacion'], ENT_COMPAT, ''); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Número de socios</td>
      <td><input type="text"  class="form-control" name="numero_socios" value="<?php echo htmlentities($row_OC['numero_socios'], ENT_COMPAT, ''); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Razón social</td>
      <td><input type="text"  class="form-control" name="razon_social" value="<?php echo htmlentities($row_OC['razon_social'], ENT_COMPAT, ''); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Dirección fiscal</td>
      <td><input type="text"  class="form-control" name="direccion_fiscal" value="<?php echo htmlentities($row_OC['direccion_fiscal'], ENT_COMPAT, ''); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">RFC</td>
      <td><input type="text"  class="form-control" name="rfc" value="<?php echo htmlentities($row_OC['rfc'], ENT_COMPAT, ''); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td>
          <button class="btn btn-primary" type="submit" value="Actualizar datos">
            <span class="glyphicon glyphicon-repeat" aria-hidden="true"></span> Actualizar Datos
          </button>         
        
      </td>
    </tr>
  </table>
  <input type="hidden" name="update" value="OC actualizado correctamente" />
  <input type="hidden" name="idoc" value="<?php echo $row_OC['idoc']; ?>" />
  <input type="hidden" name="MM_update" value="form1" />
  <input type="hidden" name="idoc" value="<?php echo $row_OC['idoc']; ?>" />
</form>
</div>
  <div class="col-xs-8">
    
  <ul class="nav nav-pills">
  <li role="presentation"
<? if(isset($_GET['contact'])){?> class="active" <? }?>><a href="?OC&detail&idoc=<? echo $_GET['idoc'];?>&contact">Contactos</a></li>
  <li role="presentation"
<? if(isset($_GET['cta'])){?> class="active" <? }?>><a href="?OC&detail&idoc=<? echo $_GET['idoc'];?>&cta">Cuentas bancarias</a></li>
    
    
  </ul>
  <hr />
    
    
  <? if(isset($_GET['contact'])){?>
  <h4><a class="btn btn-success" href="?OC&detail&idoc=<? echo $_GET['idoc'];?>&contact=add">Agregar</a> <a class="btn btn-default" href="?OC&detail&idoc=<? echo $_GET['idoc'];?>&contact">Contactos</a></h4>
  
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
        <td><input required="required"  class="form-control" type="text" name="cargo" value="" size="32" /></td>
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
        <th nowrap="nowrap" align="right">Emaril 2</th>
        <td><input  class="form-control" type="email" name="email2" value="" size="32" /></td>
        </tr>
      <tr valign="baseline">
        <th nowrap="nowrap" align="right">&nbsp;</th>
        <td><input class="btn btn-primary" type="submit" value="Agregar contacto" /></td>
        </tr>
      </table>
      <input type="hidden" name="mensaje" value="Contacto agregado correctamente" />
    <input type="hidden" name="idoc" value="<? echo $_GET['idoc'];?>" />
    <input type="hidden" name="MM_insert" value="form2" />
  </form>
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
      <td><a class="btn btn-<? if($_GET['idcontacto']==$row_contacto['idcontacto']){echo "danger";}else{echo "default";}?>" href="?OC&detail&idoc=<? echo $_GET['idoc'];?>&contact=update&idcontacto=<?php echo $row_contacto['idcontacto']; ?>" style="width:100%"><?php echo $row_contacto['contacto']; ?></a></td>
      </tr>
    <?php } ?>
  </table>
  </div>
  <div class="col-xs-8">
  <form action="<?php echo $editFormAction; ?>" method="POST" name="form3" id="form3">
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
      <th nowrap="nowrap" align="right">Emaril 2</th>
      <td><input  class="form-control" type="email" name="email2" value="<?php echo htmlentities($row_contacto_detail['email2'], ENT_COMPAT, ''); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">&nbsp;</th>
      <td><input class="btn btn-primary" type="submit" value="Actualizar datos" /></td>
    </tr>
  </table>
  <input type="hidden" name="mensaje" value="Contacto actualizado correctamente" />
  <input type="hidden" name="idcontacto" value="<?php echo $row_contacto_detail['idcontacto']; ?>" />
  <input type="hidden" name="idoc" value="<?php echo htmlentities($row_contacto_detail['idoc'], ENT_COMPAT, ''); ?>" />
  <input type="hidden" name="MM_update" value="form3" />
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
      <td><a class="btn btn-default" href="?OC&detail&idoc=<? echo $_GET['idoc'];?>&contact=update&idcontacto=<?php echo $row_contacto['idcontacto']; ?>" style="width:100%"><?php echo $row_contacto['contacto']; ?></a></td>
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
    <tr><td colspan="9" class="alert alert-info" role="alert">No se encontraron registros</td></tr>
    <? }?>
  </table>
  <? }?>
    
  <? }?>
  <? if(isset($_GET['cta'])){?>
  <h4><a class="btn btn-success" href="?OC&detail&idoc=<? echo $_GET['idoc'];?>&cta=add">Agregar</a> <a class="btn btn-default" href="?OC&detail&idoc=<? echo $_GET['idoc'];?>&cta">Cuentas bancarias</a></h4>
  
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
  <input type="hidden" name="idoc" value="<? echo $_GET['idoc'];?>" />
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
        <td><a class="btn btn-<? if($_GET['idcta_bn']==$row_cta_bn['idcta_bn']){echo "danger";}else{echo "default";}?>" href="?OC&detail&idoc=<? echo $_GET['idoc'];?>&cta=update&idcta_bn=<?php echo $row_cta_bn['idcta_bn']; ?>" style="width:100%"><?php echo $row_cta_bn['banco']; ?></a></td>
        </tr>
      <?php }  ?>
      <? if($cont==0){?>
    <tr><td colspan="5" class="alert alert-info" role="alert">No se encontraron registros</td></tr>
    <? }?>
      </tbody>
  </table>
</div>
<div class="col-xs-8">

<form action="<?php echo $editFormAction; ?>" method="POST" name="form5" id="form5">
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
  <input type="hidden" name="idopp" value="<?php echo htmlentities($row_cta_bn_detail['idopp'], ENT_COMPAT, ''); ?>" />
  <input type="hidden" name="MM_update" value="form5" />
  <input type="hidden" name="idcta_bn" value="<?php echo $row_cta_bn_detail['idcta_bn']; ?>" />
  <input type="hidden" name="MM_update" value="form5" />
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
        <td><a class="btn btn-default" href="?OC&detail&idoc=<? echo $_GET['idoc'];?>&cta=update&idcta_bn=<?php echo $row_cta_bn['idcta_bn']; ?>" style="width:100%"><?php echo $row_cta_bn['banco']; ?></a></td>
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
    
    
  </div>
</div>
<?php
mysql_free_result($opp);

mysql_free_result($cta_bn);

mysql_free_result($contacto);

mysql_free_result($oc);

mysql_free_result($pais);

mysql_free_result($contacto_detail);

mysql_free_result($cta_bn_detail);

mysql_free_result($OC);

?>
