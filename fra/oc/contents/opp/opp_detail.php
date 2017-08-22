<?php 
require_once('../Connections/dspp.php'); 
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
$idopp = $_GET['idopp'];

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
  $insertSQL = sprintf("INSERT INTO contacto (idopp, contacto, cargo, tipo, telefono1, telefono2, email1, email2) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['idopp'], "int"),
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
  $updateSQL = sprintf("UPDATE contacto SET idopp=%s, contacto=%s, cargo=%s, tipo=%s, telefono1=%s, telefono2=%s, email1=%s, email2=%s WHERE idcontacto=%s",
                       GetSQLValueString($_POST['idopp'], "int"),
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

if(isset($_POST['actualizar_opp']) && $_POST['actualizar_opp'] == 1){
  if(isset($_POST['ver_password'])){
    $ver_password = $_POST['ver_password'];
  }else{
    $ver_password = '';
  }
  $insertar = sprintf("UPDATE opp SET nombre = %s , abreviacion = %s, sitio_web = %s, email = %s, telefono = %s, ciudad  = %s, razon_social = %s, direccion_oficina = %s, direccion_fiscal  = %s, rfc  = %s, ruc  = %s WHERE idopp = %s",
      GetSQLValueString($_POST['nombre'], "text"),
      GetSQLValueString($_POST['abreviacion'], "text"),
      GetSQLValueString($_POST['sitio_web'], "text"),
      GetSQLValueString($_POST['email'], "text"),
      GetSQLValueString($_POST['telefono'], "text"),
      GetSQLValueString($_POST['ciudad'], "text"),
      GetSQLValueString($_POST['razon_social'], "text"),
      GetSQLValueString($_POST['direccion_oficina'], "text"),
      GetSQLValueString($_POST['direccion_fiscal'], "text"),
      GetSQLValueString($_POST['rfc'], "text"),
      GetSQLValueString($_POST['ruc'], "text"),
      GetSQLValueString($idopp, "int"));
  $actualizar = mysql_query($insertar,$dspp) or die(mysql_error());

  $mensaje = "Datos Actualizados Correctamente";
}

$query = "SELECT * FROM opp WHERE idopp = $idopp";
$row_opp = mysql_query($query,$dspp) or die(mysql_error());
$opp = mysql_fetch_assoc($row_opp);

?>


<div class="row">
  <div class="col-md-12">
  <?php 
  if(isset($mensaje)){
  ?>
    <div class="alert alert-success alert-dismissible" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <?php echo $mensaje; ?>
    </div>
  <?php
  }
  ?>
    <h3>Information de l'organisation</h3>
    <form action="" method="POST">
      <table class="table table-condensed">
        <tr>
          <td>#SPP</td>
          <td>
            <?php echo $opp['spp']; ?>
          </td>
        </tr>
        <tr>
          <td>Nom de l'organisation</td>
          <td>
            <input class="form-control" id="" name="nombre" value="<?php echo $opp['nombre']; ?>">
          </td>
        </tr>
        <tr>
          <td>Sigle ou nom gégé</td>
          <td>
            <input class="form-control" id="" name="abreviacion" value="<?php echo $opp['abreviacion']; ?>">
          </td>
        </tr>
        <tr>
          <td>Mot de passe</td>
          <td>
            <?php 
            if($opp['ver_password']){
              echo $opp['password'];
            }else{
              echo "<p class='alert alert-warning' style='padding:7px;'>Information non disponible</p>";
            }
             ?>
          </td>
        </tr>
        <tr>
          <td>Site Web</td>
          <td>
            <input class="form-control" id="" name="sitio_web" value="<?php echo $opp['sitio_web']; ?>">
          </td>
        </tr>
        <tr>
          <td style="width:300px;">Email<br>(<small>Le courrier électronique auquel les notifications seront envoyées</small>)</td>
          <td>
            <input class="form-control" id="" name="email" value="<?php echo $opp['email']; ?>">
          </td>
        </tr>
        <tr>
          <td>Téléphone</td>
          <td>
            <input class="form-control" id="" name="telefono" value="<?php echo $opp['telefono']; ?>">
          </td>
        </tr>
        <tr>
          <td>Pays</td>
          <td>
            <?php echo $opp['pais']; ?>
          </td>
        </tr>
        <tr>
          <td>Ville</td>
          <td>
            <input class="form-control" id="" name="ciudad" value="<?php echo $opp['ciudad']; ?>">
          </td>
        </tr>
        <tr>
          <td>Adresse du bureau</td>
          <td>
            <input class="form-control" id="" name="direccion_oficina" value="<?php echo $opp['direccion_oficina']; ?>">
          </td>
        </tr>

        <tr class="warning">
          <td colspan="2" class="text-center"><strong>Données fiscales</strong></td>
        </tr>
        <tr>
          <td>Raison sociale</td>
          <td>
            <input class="form-control" id="" name="razon_social" value="<?php echo $opp['razon_social']; ?>">
          </td>
        </tr>
        <tr>
          <td>Adresse fiscale</td>
          <td>
            <input class="form-control" id="" name="direccion_fiscal" value="<?php echo $opp['direccion_fiscal']; ?>">
          </td>
        </tr>

        <tr>
          <td>RFC</td>
          <td>
            <input class="form-control" id="" name="rfc" value="<?php echo $opp['rfc']; ?>">
          </td>
        </tr>
        <tr>
          <td>RUC</td>
          <td>
            <input class="form-control" id="" name="ruc" value="<?php echo $opp['ruc']; ?>">
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <?php 
            if($opp['ver_password']){
            ?>
              <input class="btn btn-success" type="submit" value="Mettre à jour l'information">
              <input type="hidden" name="actualizar_opp" value="1">
            <?php
            }
             ?>

          </td>
        </tr>
      </table>
    </form>
  </div>
</div>

