<?php require_once('../Connections/dspp.php'); ?>
<?php
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO oc (idf, password, nombre, abreviacion, email, email2, pais, fecha_creacion, numero_socios, razon_social, direccion_fiscal, rfc) VALUES (%s ,%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
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
                       GetSQLValueString($_POST['rfc'], "text"));

  mysql_select_db($database_dspp, $dspp);
  $Result1 = mysql_query($insertSQL, $dspp) or die(mysql_error());

  $insertGoTo = "main_menu.php?OC&add&mensaje=OC agregado correctamente";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_dspp, $dspp);
$query_pais = "SELECT nombre FROM paises ORDER BY nombre ASC";
$pais = mysql_query($query_pais, $dspp) or die(mysql_error());
$row_pais = mysql_fetch_assoc($pais);
$totalRows_pais = mysql_num_rows($pais);
?>
<br>

<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
  <table  class="table col-xs-8">
    <tr valign="baseline">
      <th nowrap="nowrap" align="right" width="1">IDF</th>
      <td><input autofocus="autofocus" type="text"  class="form-control" name="idf" value="OC-" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Password</th>
      <td><input type="text"  class="form-control" name="password" value="" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Nombre</th>
      <td><input required="required" type="text"  class="form-control" name="nombre" value="" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Abreviación</th>
      <td><input type="text"  class="form-control" name="abreviacion" value="" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Email 1</th>
      <td><input type="text"  class="form-control" name="email" value="" size="32" /></td>
    </tr>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Email 2</th>
      <td><input type="text"  class="form-control" name="email2" value="" size="32" /></td>
    </tr>

    <tr valign="baseline">
      <th nowrap="nowrap" align="right">País</th>
      <td>
      
<select required class="form-control" name="pais">
<option value="">Selecciona</option>
<?php 
do {  
?>
<option class="form-control" value="<?php echo utf8_encode($row_pais['nombre']);?>" ><?php echo utf8_encode($row_pais['nombre']);?></option>
<?php
} while ($row_pais = mysql_fetch_assoc($pais));
?>
</select>
      
      </td>
    </tr>
    <tr> </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Fecha de creación</th>
      <td><input type="text"  class="form-control" name="fecha_creacion" value="" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Número de socios</th>
      <td><input type="text"  class="form-control" name="numero_socios" value="" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Razón social</th>
      <td><input type="text"  class="form-control" name="razon_social" value="" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">Dirección fiscal</th>
      <td><input type="text"  class="form-control" name="direccion_fiscal" value="" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">RFC</th>
      <td><input type="text"  class="form-control" name="rfc" value="" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <th nowrap="nowrap" align="right">&nbsp;</th>
      <td><input type="submit" class="btn btn-primary" value="Agregar OC" /></td>
    </tr>
  </table>
  <input type="hidden" name="MM_insert" value="form1" />
</form>

<?php
mysql_free_result($pais);
?>
