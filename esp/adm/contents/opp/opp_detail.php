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
  $insertar = sprintf("UPDATE opp SET nombre = %s , abreviacion = %s, password = %s, sitio_web = %s, email = %s, telefono = %s, pais = %s, ciudad  = %s, razon_social = %s, direccion_oficina = %s, direccion_fiscal  = %s, rfc  = %s, ruc  = %s, ver_password = %s WHERE idopp = %s",
      GetSQLValueString($_POST['nombre'], "text"),
      GetSQLValueString($_POST['abreviacion'], "text"),
      GetSQLValueString($_POST['password'], "text"),
      GetSQLValueString($_POST['sitio_web'], "text"),
      GetSQLValueString($_POST['email'], "text"),
      GetSQLValueString($_POST['telefono'], "text"),
      GetSQLValueString($_POST['pais'], "text"),
      GetSQLValueString($_POST['ciudad'], "text"),
      GetSQLValueString($_POST['razon_social'], "text"),
      GetSQLValueString($_POST['direccion_oficina'], "text"),
      GetSQLValueString($_POST['direccion_fiscal'], "text"),
      GetSQLValueString($_POST['rfc'], "text"),
      GetSQLValueString($_POST['ruc'], "text"),
      GetSQLValueString($ver_password, "int"),
      GetSQLValueString($_GET['idopp'], "int"));
  $actualizar = mysql_query($insertar,$dspp) or die(mysql_error());

  $mensaje = "Datos Actualizados Correctamente";
}
if(isset($_POST['agregar_contacto']) && $_POST['agregar_contacto'] == 1){
  $insertSQL = sprintf("INSERT INTO contactos (idopp, nombre, cargo, telefono1, telefono2, email1, email2) VALUES (%s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['idopp'], "int"),
    GetSQLValueString($_POST['nombre'], "text"),
    GetSQLValueString($_POST['cargo'], "text"),
    GetSQLValueString($_POST['telefono1'], "text"),
    GetSQLValueString($_POST['telefono2'], "text"),
    GetSQLValueString($_POST['email1'], "text"),
    GetSQLValueString($_POST['email2'], "text"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
  $mensaje = "Nuevo Contacto Agregado";
}

if(isset($_POST['actualizar_contacto']) && $_POST['actualizar_contacto'] == 1){
  $updateSQL = sprintf("UPDATE contactos SET nombre = %s, cargo = %s, telefono1 = %s, telefono2 = %s, email1 = %s, email2 = %s WHERE idcontacto = %s",
    GetSQLValueString($_POST['nombre'], "text"),
    GetSQLValueString($_POST['cargo'], "text"),
    GetSQLValueString($_POST['telefono1'], "text"),
    GetSQLValueString($_POST['telefono2'], "text"),
    GetSQLValueString($_POST['email1'], "text"),
    GetSQLValueString($_POST['email2'], "text"),
    GetSQLValueString($_POST['idcontacto'], "int"));
  $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());
  $mensaje = "Contacto Actualizado Correctamente";
}
if(isset($_POST['eliminar_contacto']) && $_POST['eliminar_contacto'] == 1){
  $deleteSQL = sprintf("DELETE FROM contactos WHERE idcontacto = %s",
    GetSQLValueString($_POST['idcontacto'], "int"));
  $eliminar = mysql_query($deleteSQL, $dspp) or die(mysql_error());
  $mensaje = "Contacto Eliminado";
}

$query = "SELECT * FROM opp WHERE idopp = $_GET[idopp]";
$row_opp = mysql_query($query,$dspp) or die(mysql_error());
$opp = mysql_fetch_assoc($row_opp);

?>

<div class="row">
  <div class="col-md-8">
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
  
    <?php 
    if(isset($_GET['contacto']) && !empty($_GET['contacto'])){
      $row_contacto = mysql_query("SELECT * FROM contactos WHERE idcontacto = $_GET[contacto]", $dspp) or die(mysql_error());
      $detalle_contacto = mysql_fetch_assoc($row_contacto);
    ?>
      <h4>Contacto de: <span style="color:red"><?php echo $opp['nombre']; ?></span> </h4>
        <form action="" id="actualizar_contacto" method="POST" class="form-horizontal">

          <div class="form-group">
            <label for="nombre" class="col-sm-2 control-label">Nombre</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $detalle_contacto['nombre']; ?>" placeholder="Nombre del Contacto" autofocus required>
            </div>
          </div>

          <div class="form-group">
            <label for="cargo" class="col-sm-2 control-label">Cargo</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="cargo" name="cargo" value="<?php echo $detalle_contacto['cargo']; ?>" placeholder="Cargo del Contacto" autofocus>
            </div>
          </div>
          <div class="form-group">
            <label for="telefono1" class="col-sm-2 control-label">Telefono 1</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="telefono1" value="<?php echo $detalle_contacto['telefono1']; ?>" placeholder="Escriba el Telefono" name="telefono1" required>
            </div>
          </div>
          <div class="form-group">
            <label for="telefono2" class="col-sm-2 control-label">Telefono 2</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="telefono2" name="telefono2" value="<?php echo $detalle_contacto['telefono2']; ?>" placeholder="Escriba el Telefono 2">
            </div>
          </div>
          <div class="form-group">
            <label for="email1" class="col-sm-2 control-label">Email 1</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="email1" name="email1" value="<?php echo $detalle_contacto['email1']; ?>" placeholder="Escriba el correo electronico">
            </div>
          </div>
          <div class="form-group">
            <label for="email2" class="col-sm-2 control-label">Email 2</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="email2" name="email2" value="<?php echo $detalle_contacto['email2']; ?>" placeholder="Escriba el correo electronico">
            </div>
          </div>
          <input type="hidden" name="idcontacto" value="<?php echo $detalle_contacto['idcontacto']; ?>">
          <input type="hidden" name="actualizar_contacto" value="1">
          <button type="submit" class="btn btn-success form-control" style="color:white"><span class="glyphicon glyphicon-repeat" aria-hidden="true"></span> Actualizar Contacto</button>
        </form>
    <?php
    }else if(isset($_GET['addContacto'])){ //////// INICIA FORMULARIO PARA AGREGAR UN CONTACTO
    ?>
      <h4>Nuevo Contacto de: <span style="color:red"><?php echo $opp['nombre']; ?></span> </h4>
        <form action="" id="agregar_contacto" method="POST" class="form-horizontal">
          <div class="panel panel-info">
            <div class="panel-heading">
              <h3 class="panel-title">Formulario de Contacto</h3>
            </div>
            <div class="panel-body">
              <div class="form-group">
                <label for="nombre" class="col-sm-2 control-label">Nombre</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre del Contacto" autofocus required>
                </div>
              </div>

              <div class="form-group">
                <label for="cargo" class="col-sm-2 control-label">Cargo</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="cargo" name="cargo" placeholder="Cargo del Contacto" autofocus>
                </div>
              </div>
              <div class="form-group">
                <label for="telefono1" class="col-sm-2 control-label">Telefono 1</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="telefono1" placeholder="Escriba el Telefono" name="telefono1" required>
                </div>
              </div>
              <div class="form-group">
                <label for="telefono2" class="col-sm-2 control-label">Telefono 2</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="telefono2" name="telefono2" placeholder="Escriba el Telefono 2">
                </div>
              </div>
              <div class="form-group">
                <label for="email1" class="col-sm-2 control-label">Email 1</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="email1" name="email1" placeholder="Escriba el correo electronico">
                </div>
              </div>
              <div class="form-group">
                <label for="email2" class="col-sm-2 control-label">Email 2</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="email2" name="email2" placeholder="Escriba el correo electronico">
                </div>
              </div>
              <input type="hidden" name="idopp" value="<?php echo $_GET['idopp']; ?>">
              <input type="hidden" name="agregar_contacto" value="1">
              <button type="submit" class="btn btn-success form-control" style="color:white"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> Agregar Nuevo Contacto</button>
            </div>
          </div>
        </form>
    <?php
    //////////// TERMINA FORMULARIO PARA AGREGAR CONTACTO
    }else{
    ?>
    <h4>Datos de: <span style="color:red"><?php echo $opp['nombre']; ?></span> </h4>
    <form action="" id="detalle_contacto" method="POST">
      <table class="table table-condensed">
        <tr>
          <td>#SPP</td>
          <td>
            <?php echo $opp['spp']; ?>
          </td>
        </tr>
        <tr>
          <td>Nombre</td>
          <td>
            <input class="form-control" id="" name="nombre" value="<?php echo $opp['nombre']; ?>">
          </td>
        </tr>
        <tr>
          <td>Abreviación</td>
          <td>
            <input class="form-control" id="" name="abreviacion" value="<?php echo $opp['abreviacion']; ?>">
          </td>
        </tr>
        <tr>
          <td>Password</td>
          <td>
            <input class="form-control" id="" name="password" value="<?php echo $opp['password']; ?>">
            <label>
              <input type="checkbox" name="ver_password" value="1" <?php if(isset($opp['ver_password'])){ echo 'checked'; } ?>> ocultar mi contraseña al OC
            </label>
          </td>
        </tr>
        <tr>
          <td>Sitio Web</td>
          <td>
            <input class="form-control" id="" name="sitio_web" value="<?php echo $opp['sitio_web']; ?>">
          </td>
        </tr>
        <tr>
          <td style="width:300px;">Email<br>(<small>email al que seran enviadas las notificaciones</small>)</td>
          <td>
            <input class="form-control" id="" name="email" value="<?php echo $opp['email']; ?>">
          </td>
        </tr>
        <tr>
          <td>Teléfono</td>
          <td>
            <input class="form-control" id="" name="telefono" value="<?php echo $opp['telefono']; ?>">
          </td>
        </tr>
        <tr>
          <td>País</td>
          <td>
            <select name="pais" id="">
              <option value="">...</option>
              <?php 
              $row_pais = mysql_query("SELECT * FROM paises", $dspp) or die(mysql_error());
              while($pais = mysql_fetch_assoc($row_pais)){
                if(utf8_encode($pais['nombre']) == $opp['pais']){
                  echo "<option value='".utf8_encode($pais['nombre'])."' selected>".utf8_encode($pais['nombre'])."</option>";
                  //echo "<option value='".$pais['nombre']."' selected>".$pais['nombre']."</option>";
                }else{
                  echo "<option value='".utf8_encode($pais['nombre'])."'>".utf8_encode($pais['nombre'])."</option>";
                  //echo "<option value='".$pais['nombre']."'>".$pais['nombre']."</option>";
                }
              }
               ?>
            </select>
          </td>
        </tr>
        <tr>
          <td>Ciudad</td>
          <td>
            <input class="form-control" id="" name="ciudad" value="<?php echo $opp['ciudad']; ?>">
          </td>
        </tr>
        <tr>
          <td>Dirección Oficina</td>
          <td>
            <input class="form-control" id="" name="direccion_oficina" value="<?php echo $opp['direccion_oficina']; ?>">
          </td>
        </tr>

        <tr class="warning">
          <td colspan="2" class="text-center"><strong>Datos Fiscales</strong></td>
        </tr>
        <tr>
          <td>Razón Social</td>
          <td>
            <input class="form-control" id="" name="razon_social" value="<?php echo $opp['razon_social']; ?>">
          </td>
        </tr>
        <tr>
          <td>Dirección Fiscal</td>
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
            <input class="btn btn-success" style="width:100%" type="submit" value="Actualizar Información">
            <input type="hidden" name="actualizar_opp" value="1">
          </td>
        </tr>
      </table>
    </form>
    <?php
    }
     ?>
  </div>
  
  <!---- INICIA SECCIÓN CONTACTOS ---->
  <?php 
  //$row_contactos = mysql_query("SELECT * FROM contactos WHERE idopp = $_GET[idopp]", $dspp) or die(mysql_error());
  
  $query_contactos = "SELECT contacto1_nombre, contacto1_cargo, contacto1_email, contacto1_telefono, contacto2_nombre, contacto2_cargo, contacto2_email, contacto2_telefono, adm1_nombre, adm1_email, adm1_telefono, adm2_nombre, adm2_email, adm2_telefono FROM solicitud_certificacion WHERE idopp = '$_GET[idopp]'";
  $row_contactos = mysql_query($query_contactos);
  $num_contactos = mysql_num_rows($row_contactos);

  $contacto = mysql_fetch_assoc($row_contactos);

   ?>
  <div class="col-md-4">
    <h4>Contacto(s) de la Organización</h4>
    <a class="btn btn-sm btn-primary" href="?OPP&detail&idopp=<?php echo $_GET['idopp']; ?>&addContacto" style="width:100%"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> Nuevo Contacto</a>

    <table class="table table-hover table-condensed">
      <thead>
        <tr class="success" >
          <th>Nombre</th>
          <th colspan="2">Cargo</th>
        </tr>
      </thead>
      <tbody style="font-size:12px;">
        <?php
            if(isset($contacto['contacto1_nombre'])){
              ?>
              <tr>
                <td>
                  <?php echo $contacto['contacto1_nombre']; ?>
                </td>
                <td>
                  <?php echo $contacto['contacto1_cargo']; ?>
                </td>  
              </tr>
              <?php
            }if(isset($contacto['contacto2_nombre'])){
              ?>
                <tr>
                  <td>
                    <?php echo $contacto['contacto2_nombre']; ?>
                  </td>
                  <td>
                    <?php echo $contacto['contacto2_cargo']; ?>
                  </td>
                </tr>
              <?php
            }if(isset($contacto['adm1_nombre'])){
              ?>
                <tr>
                  <td>
                    <?php echo $contacto['adm1_nombre']; ?>
                  </td>
                  <td>
                    <?php echo 'Administrativo'; ?>
                  </td>
                </tr>
              <?php
            }if(isset($contacto['adm2_nombre'])){
              ?>
                <tr>
                  <td>
                    <?php echo $contacto['adm2_nombre']; ?>
                  </td>
                  <td>
                    <?php echo 'Administrativo'; ?>
                  </td>
                </tr>
              <?php
            }
        ?>    
        <!--<td><?php echo '<a href="?OPP&detail&idopp='.$_GET['idopp'].'&contacto='.$contacto['idcontacto'].'"><span class="glyphicon glyphicon-search" aria-hidden="true"></span> '.$contacto['nombre'].'</a>'; ?><a href=""></a> </td>
              <td><?php echo $contacto['cargo']; ?></td>
              <td>
                <form action="" method="post" name="formularioEliminar" ONSUBMIT="return preguntar();">
                  <button class="btn btn-sm btn-danger" type="subtmit" value="Eliminar" data-toggle="tooltip" data-placement="top" title="Eliminar">
                    <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                  </button>        

                  <input type="hidden" value="1" name="eliminar_contacto" />
                  <input type="hidden" value="<?php echo $contacto['idcontacto']; ?>" name="idcontacto" />
                </form>
              </td>-->
      </tbody>
    </table>
  </div>
  <!---- TERMINA SECCIÓN CONTACTOS ---->
  
</div>

<script language="JavaScript"> 
function preguntar(){ 
    if(!confirm('¿Estas seguro de eliminar el registro?')){ 
       return false; } 
} 
</script>
