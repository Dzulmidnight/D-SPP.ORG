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

/*
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
$query_accion_detalle = sprintf("SELECT solicitud_registro.*, oc.idoc, oc.nombre AS 'nombreOC', oc.abreviacion AS 'abreviacionOC' FROM solicitud_registro INNER JOIN oc ON solicitud_registro.idoc = oc.idoc WHERE idsolicitud_registro = %s", GetSQLValueString($colname_accion_detalle, "int"));
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
$query_accion_lateral = "SELECT idultima_accion, idcom, ultima_accion FROM ultima_accion ORDER BY fecha DESC";
$accion_lateral = mysql_query($query_accion_lateral, $dspp) or die(mysql_error());
$row_accion_lateral = mysql_fetch_assoc($accion_lateral);
$totalRows_accion_lateral = mysql_num_rows($accion_lateral);

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE com SET idf=%s, password=%s, nombre=%s, abreviacion=%s, sitio_web=%s, email=%s, pais=%s, idoc=%s, razon_social=%s, direccion_fiscal=%s, rfc=%s WHERE idcom=%s",
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
                       GetSQLValueString($_POST['idcom'], "int"));

  $Result1 = mysql_query($updateSQL, $dspp) or die(mysql_error());
}

$colname_com = "-1";
 
$colname_com = $_SESSION['idcom'];

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
  <thead>
      <tr>
        <th colspan="" class="text-center"><h3>Application for Buyers’, Registration</h3></th>
        <th class="text-center success"><h3><?php echo $row_solicitud['abreviacionOC']; ?></h3></th>
      </tr> 
      <?php
        $procedimiento = $row_solicitud['procedimiento'];
      ?>    
      <tr>
        <th colspan="2">
          
                  <div class="col-xs-12 text-center">
                    <div class="row">
                  <h4>Registration Procedures <br><small>(by OC)</small></h4>
                    </div>
                  </div>
                  <div class="col-xs-3 text-center">
                    <div class="row">
                      <div class="col-xs-12">
                        <p style="font-size:10px;"><b>DOCUMENT-BASED SHORTENED</b></p> 
                      </div>       
                      <div class="col-xs-12">
                        <input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="my-checkbox" value='DOCUMENTAL "ACORTADO"' <?php if($procedimiento == 'DOCUMENTAL "ACORTADO"'){echo "checked";}else{echo "readonly";} ?> >
        
                      </div>                        
                    </div>
                  </div>
                  <div class="col-xs-3 text-center">
                    <div class="row">
                      <div class="col-xs-12">
                        <p style="font-size:10px;"><b>DOCUMENT-BASED NORMAL</b></p> 
                      </div>
                      <div class="col-xs-12">
                        <input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="my-checkbox" value='DOCUMENTAL "NORMAL"' <?php if($procedimiento == 'DOCUMENTAL "NORMAL"'){echo "checked";}else{echo "readonly";} ?> >
        
                      </div>                
                    </div>
                  </div>
                  <div class="col-xs-3 text-center">
                    <div class="row">
                      <div class="col-xs-12">
                        <p style="font-size:10px;"><b>COMPLETE ON-SITE</b></p>  
                      </div>
                      <div class="col-xs-12">
                        <input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="my-checkbox" value='COMPLETO "IN SITU"' <?php if($procedimiento == 'COMPLETO "IN SITU"'){echo "checked";}else{echo "readonly";} ?> >
        
                      </div>                
                    </div>
                  </div>
                  <div class="col-xs-3 text-center">
                    <div class="row">
                      <div class="col-xs-12">
                        <p style="font-size:10px;"><b>COMPLETE REMOTE</b></p>  
                      </div>
                      <div class="col-xs-12">
                        <input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="my-checkbox" value='COMPLETO "A DISTANCIA"' <?php if($procedimiento == 'COMPLETO "A DISTANCIA"'){echo "checked";}else{echo "readonly";} ?> >
        
                      </div>                
                    </div>
                  </div>    
        </th>
      </tr>
  </thead>
  <tbody>
    <tr>
      <td>
        <p>COMPANY NAME</p>
      </td>
      <td>
        <input type="text" class="form-control" value="<?php echo $row_com['nombre']?>" readonly>
      </td>
    </tr>
    <tr>
      <td>
        <p>COMPLETE ADDRESS FOR COMPANY LOCATION (STREET, DISTRICT, TOWN / CITY, REGION)</p>
      </td>
      <td>
        <input type="text" class="form-control" value="<?php echo $row_com['direccion'];?>" placeholder="Dirección de las Oficinas" readonly>
      </td>
    </tr>
    <tr>
      <td>
        <p>COMPANY EMAIL ADDRESS:</p> 
        <input type="text" class="form-control" value="<?php echo $row_com['email']?>" readonly>
      </td>
      <td>
        <p>COMPANY TELEPHONES(COUNTRY CODE+AREA CODE+NUMBER)</p> 
        <input type="text" class="form-control" value="<?php echo $row_com['telefono']?>" readonly>
      </td>
    </tr>
    <tr>
      <td>
        <p>COUNTRY</p>
        <input type="text" class="form-control" value="<?php echo $row_com['pais']?>" readonly>
      </td>
      <td>
        <p>WEB SITE</p>
        <input type="text" class="form-control" value="<?php echo $row_com['sitio_web']?>" readonly>
      </td>
    </tr>
    <tr>
      <td>
        <p>CITY</p>
        <input type="text" class="form-control" value="<?php echo $row_com['ciudad']?>" readonly>
      </td>
      <td>
        <p>FISCAL ADDRESS</p>
        <input type="text" class="form-control" value="<?php echo $row_com['direccion_fiscal']?>" readonly>
      </td>
    </tr>
    <tr>
      <td>
        <p>RUC</p>
        <input type="text" class="form-control" value="<?php echo $row_com['ruc']?>" readonly>
      </td>
      <td>
        <p>RFC</p>
        <input type="text" class="form-control" value="<?php echo $row_com['rfc']?>" readonly>
      </td>
    </tr>
    <!------------------------------------------ INICIA DATOS DE CONTACTO ---------------------------------------->
    <tr>
      <td colspan="2" class="text-center alert alert-warning"> CONTACT </td>
    </tr>
    <tr>
      <td colspan="2">
        <p>CONTACT PERSON(S) OF APPLICATION</p>
        <div class="col-xs-6">
          <input type="text" class="form-control" name="p1_nombre" value="<?php echo $row_solicitud['p1_nombre']; ?>" placeholder="Name 1" readonly required>
          <input type="email" class="form-control" name="p1_correo" value="<?php echo $row_solicitud['p1_correo']; ?>" placeholder="Email 1" readonly required>
        </div>
        <div class="col-xs-6">
          <input type="text" class="form-control" name="p1_cargo" value="<?php echo $row_solicitud['p1_cargo']; ?>" placeholder="Position 1" readonly required>
          <input type="text" class="form-control" name="p1_telefono" value="<?php echo $row_solicitud['p1_telefono']; ?>" placeholder="Telephone 1" readonly required>
        </div>
        <div class="col-xs-12"><br></div>
        <div class="col-xs-6">
          <input type="text" class="form-control" name="p2_nombre" value="<?php echo $row_solicitud['p2_nombre']; ?>" placeholder="Name 2" readonly>
          <input type="email" class="form-control" name="p2_correo" value="<?php echo $row_solicitud['p2_correo']; ?>" placeholder="Email 2" readonly>
        </div>
        <div class="col-xs-6">
          <input type="text" class="form-control" name="p2_cargo" value="<?php echo $row_solicitud['p2_cargo']; ?>" placeholder="Position 2" readonly>
          <input type="text" class="form-control" name="p2_telefono" value="<?php echo $row_solicitud['p2_telefono']; ?>" placeholder="Telephone 2" readonly>
        </div>
      </td>
    </tr>
    <tr>
      <td colspan="2" class="text-center alert alert-warning">
        ADMINISTRATIVE AREA
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <p>PERSON(S) OF THE ADMINISTRATIVE AREA </p>
        <div class="col-xs-6">
          <input type="text" class="form-control" name="adm1_nombre" value="<?php echo $row_solicitud['adm1_nombre']; ?>" placeholder="Name 1" readonly required>
          <input type="email" class="form-control" name="adm1_correo" value="<?php echo $row_solicitud['adm1_correo']; ?>" placeholder="Email 1" readonly required>
        </div>
        <div class="col-xs-6">

          <input type="text" class="form-control" name="adm1_telefono" value="<?php echo $row_solicitud['adm1_telefono']; ?>" placeholder="Telephone 1" readonly required>
        </div>
        <div class="col-xs-12"><br></div>
        <div class="col-xs-6">
          <input type="text" class="form-control" name="adm2_nombre" value="<?php echo $row_solicitud['adm2_nombre']; ?>" placeholder="Name 2" readonly>
          <input type="email" class="form-control" name="adm2_correo" value="<?php echo $row_solicitud['adm2_correo']; ?>" placeholder="Email 2" readonly>
        </div>
        <div class="col-xs-6">

          <input type="text" class="form-control" name="adm2_telefono" value="<?php echo $row_solicitud['adm2_telefono']; ?>" placeholder="Telephone 2" readonly>
        </div>
      </td>
    </tr>
    <!----------------------------------------------------------- INICIA DATOS DE OPERACION -------------------------------------------------------------------------->
    <tr class="text-center alert alert-success">
      <td colspan="2">INFORMATION ON OPERATION</td>
    </tr>
    <tr>
      <td colspan="2">
        <p>SELECT THE TYPE OF COMPANY FOR WHICH THE SPP REGISTRATION IS REQUESTED. AN INTERMEDIARY CAN NOT BE REGISTERED UNDER SPP IF IT DOES NOT HAVE A REGISTERED SPP-FINAL BUYER (OR IN THE PROCESS OF SPP REGISTRATION) TO WHOM IT WILL SELL.</p>

      <?php 
        $texto = $row_solicitud['tipo_empresa'];
       ?>
        <div class="col-xs-4">
         <?php 
          $cadena_buscada = "comprador_final";
          $posicion_coincidencia = strpos($texto, $cadena_buscada);
          if($posicion_coincidencia === false){
            echo 'FINAL BUYER <input class="form-control" name="tipo_empresa[]" type="checkbox" value="comprador_final"  readonly>';
          }else{
            echo 'FINAL BUYER <input class="form-control" name="tipo_empresa[]" type="checkbox" value="comprador_final" checked readonly>';
          }
          ?>
        </div>
        <div class="col-xs-4">
         <?php 
          $cadena_buscada = "intermediario";
          $posicion_coincidencia = strpos($texto, $cadena_buscada);
          if($posicion_coincidencia === false){
            echo 'INTERMEDIARY: <input class="form-control" name="tipo_empresa[]" type="checkbox" value="intermediario"  readonly>';
          }else{
            echo 'INTERMEDIARY: <input class="form-control" name="tipo_empresa[]" type="checkbox" value="intermediario" checked readonly>';
          }
          ?>
        </div>
        <div class="col-xs-4">
         <?php 
          $cadena_buscada = "maquilador";
          $posicion_coincidencia = strpos($texto, $cadena_buscada);
          if($posicion_coincidencia === false){
            echo 'MAQUILA COMPANY: <input class="form-control" name="tipo_empresa[]" type="checkbox" value="maquilador"  readonly>';
          }else{
            echo 'MAQUILA COMPANY: <input class="form-control" name="tipo_empresa[]" type="checkbox" value="maquilador" checked readonly>';
          }
          ?>
        </div>

      </td>
    </tr>
    <tr>
      <td colspan="2">
        <p>1.- FROM WHICH SMALL PRODUCERS’ ORGANIZATIONS DO YOU MAKE PURCHASES OR ATTEMPT TO DO SO UNDER THE SMALL PRODUCERS’ SYMBOL SCHEME? </p>
        <textarea name="resp1" id="" class="form-control" value="" readonly><?php echo $row_solicitud['resp1']; ?></textarea>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <p>2.- WHO IS/ARE THE OWNER(S) OF THE COMPANY?</p>
        <textarea name="resp2" id="" class="form-control" value="" readonly><?php echo $row_solicitud['resp2']; ?></textarea>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <p>3.- SPECIFY WHICH PRODUCT (S) YOU WANT TO INCLUDE IN THE CERTIFICATE OF THE SMALL PRODUCERS’ SYMBOL FOR WHICH THE CERTIFICATION ENTITY WILL CONDUCT THE ASSESSMENT.</p>
        <textarea name="resp3" id="" class="form-control" value="" readonly><?php echo $row_solicitud['resp3']; ?></textarea>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <p>4.- IF YOUR COMPANY IS A FINAL BUYER, MENTION IF YOUR ORGANIZATION WOULD LIKE TO INCLUDE AN ADDITIONAL DESCRIPTOR FOR COMPLEMENTARY USE WITH THE GRAPHIC DESIGN OF THE SMALL PRODUCERS’ SYMBOL <sup>4</sup></p>
        
        <textarea name="resp4" id="" class="form-control" value="" readonly><?php echo $row_solicitud['resp4']; ?></textarea>
        <p><small><sup>4</sup> Review “Regulation on Graphics” and the “List of Optional Complementary Criteria”</small></p>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <p>5.- SELECT THE SCOPE OF THE COMPANY</p>

      <?php 
        $texto = $row_solicitud['resp5'];
       ?>
        <div class="col-xs-4">
         <?php 
          $cadena_buscada = "PRODUCCION";
          $posicion_coincidencia = strpos($texto, $cadena_buscada);
          if($posicion_coincidencia === false){
            echo 'PRODUCTION <input class="form-control" name="resp5[]" type="checkbox" value="PRODUCCION"  readonly>';
          }else{
            echo 'PRODUCTION <input class="form-control" name="resp5[]" type="checkbox" value="PRODUCCION" checked readonly>';
          }
          ?>
        </div>
        <div class="col-xs-4">
         <?php 
          $cadena_buscada = "PROCESAMIENTO";
          $posicion_coincidencia = strpos($texto, $cadena_buscada);
          if($posicion_coincidencia === false){
            echo 'PROCESSING <input class="form-control" name="resp5[]" type="checkbox" value="PROCESAMIENTO"  readonly>';
          }else{
            echo 'PROCESSING <input class="form-control" name="resp5[]" type="checkbox" value="PROCESAMIENTO" checked readonly>';
          }
          ?>
        </div>
        <div class="col-xs-4">
         <?php 
          $cadena_buscada = "IMPORTACION";
          $posicion_coincidencia = strpos($texto, $cadena_buscada);
          if($posicion_coincidencia === false){
            echo 'TRADING <input class="form-control" name="resp5[]" type="checkbox" value="IMPORTACION"  readonly>';
          }else{
            echo 'TRADING <input class="form-control" name="resp5[]" type="checkbox" value="IMPORTACION" checked readonly>';
          }
          ?>
        </div>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <p>6. SELECT IF YOUR COMPANY SUBCONTRACT THE SERVICES OF PROCESSING PLANTS, TRADING COMPANIES OR COMPANIES THAT CARRY OUT THE IMPORT OR EXPORT.</p>
        <div class="col-xs-6">
          <?php 
            if($row_solicitud['resp6'] == "SI"){
              echo 'YES <input type="radio" class="form-control" name="resp6" onclick="mostrar_empresas()" id="resp6" value="SI" checked>';
            }else{
              echo 'YES <input type="radio" class="form-control" name="resp6" onclick="mostrar_empresas()" id="resp6" value="SI">';
            }
           ?>
        </div>
        <div class="col-xs-6">
          <?php 
            if($row_solicitud['resp6'] == "NO"){
              echo 'NO <input type="radio" class="form-control" name="resp6" onclick="ocultar_empresas()" id="resp6" value="NO" checked>';
            }else{
              echo 'NO <input type="radio" class="form-control" name="resp6" onclick="ocultar_empresas()" id="resp6" value="NO">';
            }
           ?>
        </div>
        <!--<input type="text" class="form-control" name="resp6">-->
      </td>
    </tr>

    <tr >
      <td colspan="2" >
        <p>IF THE ANSWER IS AFFIRMATIVE, MENTION THE NAME AND THE SERVICE THAT PERFORMS.</p>
        <div id="contenedor_tablaEmpresas" class="col-xs-12" style="display:block">

        <table class="table table-bordered" id="tablaEmpresas">
          <tr>
            <td>NAME OF THE COMPANY</td>
            <td>SERVICE OFFERED</td>

            <!--<td>
              <button type="button" onclick="tablaProductos()" class="btn btn-primary" aria-label="Left Align">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
              </button>
              
            </td>-->          
          </tr>

          <?php 
          $query_empresa_detalle = "SELECT * FROM subEmpresas WHERE idsolicitud_registro = $_GET[idsolicitud]";
          $empresa_detalle = mysql_query($query_empresa_detalle, $dspp) or die(mysql_error());
          $contador = 0;
          while($row_empresa = mysql_fetch_assoc($empresa_detalle)){
            ?>

          <tr>
            <td>
              <input type="text" class="form-control claseModificar" name="resp6_empresa[$contador]" id="exampleInputEmail1" placeholder="Product" value="<?echo $row_empresa['nombre']?>" readonly>
            </td>
            <td>
              <input type="text" class="form-control claseModificar" name="resp6_servicio[$contador]" id="exampleInputEmail1" placeholder="Service" value="<?echo $row_empresa['servicio']?>" readonly>
            </td>
          </tr>

          <?php $contador++; }?>    


        </table>




        </div>    
      </td>
    </tr>


    <tr>
      <td colspan="2">
        <p>7. IF YOU SUBCONTRACT THE SERVICES OF PROCESSING PLANTS, TRADING COMPANIES OR COMPANIES THAT CARRY OUT THE IMPORT OR EXPORT, INDICATE WHETHER THESE COMPANIES ARE GOING TO APPLYFOR THE REGISTRATION UNDER SPP CERTIFICATION PROGRAM </p>
        
        <textarea name="resp7" id="" class="form-control" value="" readonly><?php echo $row_solicitud['resp7']; ?></textarea>
        
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <p>8. IN ADDITION TO YOUR MAIN OFFICES, PLEASE SPECIFY HOW MANY COLLECTION CENTERS, PROCESSING AREAS AND ADDITIONAL OFFICES YOU HAVE.      </p>
        
        <textarea name="resp8" id="" class="form-control" value="" readonly><?php echo $row_solicitud['resp8']; ?></textarea>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <p>9. IF YOU HAVE COLLECTION CENTERS, PROCESSING AREAS OR ADDITIONAL OFFICES, PLEASE ATTACH A GENERAL MAP INDICATING WHERE THEY ARE LOCATED.</p>
        <textarea name="resp9" id="" class="form-control" value="" readonly><?php echo $row_solicitud['resp9']; ?></textarea>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <p>10.  IF THE APPLICANT HAS AN INTERNAL CONTROL SYSTEM FOR COMPLYING WITH THE CRITERIA IN THE GENERAL STANDARD OF THE SMALL PRODUCERS’ SYMBOL, PLEASE EXPLAIN HOW IT WORKS.</p>
        <textarea name="resp10" id="" class="form-control" value="" readonly><?php echo $row_solicitud['resp10']; ?></textarea>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <p>11. FILL OUT THE TABLE ACCORDING YOUR CERTIFICATIONS, (example: EU, NOP, JASS, FLO, etc).</p>
        <!--<table class="table table-bordered" id="tablaCertificaciones">
          <tr>
            <td>CERTIFICACIÓN</td>
            <td>CERTIFICADORA</td>
            <td>AÑO INICIAL DE CERTIFICACIÓN?</td>
            <td>¿HA SIDO INTERRUMPIDA?</td> 
            <td>
              <button type="button" onclick="tablaCertificaciones()" class="btn btn-primary" aria-label="Left Align">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
              </button>
              
            </td>
          </tr>
          <tr class="text-center">
            <td><input type="text" class="form-control" name="certificacion[0]" id="exampleInputEmail1" placeholder="CERTIFICACIÓN"></td>
            <td><input type="text" class="form-control" name="certificadora[0]" id="exampleInputEmail1" placeholder="CERTIFICADORA"></td>
            <td><input type="date" class="form-control" name="ano_inicial[0]" id="exampleInputEmail1" placeholder="AÑO INICIAL"></td>
            <td>
              <div class="col-xs-6">SI<input type="radio" class="form-control" name="interrumpida[0]" value="SI"></div>
              <div class="col-xs-6">NO<input type="radio" class="form-control" name="interrumpida[0]" value="NO"></div>
            </td>
          </tr>

        </table> -->
        <table class="table table-bordered" id="tablaCertificaciones">
          <tr>
            <td>CERTIFICATION</td>
            <td>CERTIFICATION ENTITY</td>
            <td>INITIAL YEAR OF CERTIFICATION</td>
            <td>HAS BEEN INTERRUPTED?</td> 
            <!--<td>
              <button type="button" onclick="tablaCertificaciones()" class="btn btn-primary" aria-label="Left Align">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
              </button>
              
            </td>-->
          </tr>

          <?php 
          $query_certificacion_detalle = "SELECT * FROM certificaciones WHERE idsolicitud_registro = $_GET[idsolicitud]";
          $certificacion_detalle = mysql_query($query_certificacion_detalle, $dspp) or die(mysql_error());
          $contador = 0;
          while($row_certificacion = mysql_fetch_assoc($certificacion_detalle)){
            ?>
            <tr class="text-center">
              <td><input type="text" class="form-control claseModificar" name="certificacion[$contador]" id="exampleInputEmail1" placeholder="CERTIFICATION" value="<?echo $row_certificacion['certificacion']?>" readonly></td>
              <td><input type="text" class="form-control claseModificar" name="certificadora[$contador]" id="exampleInputEmail1" placeholder="CERTIFICATION ENTITY" value="<?echo $row_certificacion['certificadora']?>" readonly></td>
              <td><input type="date" class="form-control claseModificar" name="ano_inicial[$contador]" id="exampleInputEmail1" placeholder="INITIAL YEAR" value="<?echo $row_certificacion['ano_inicial']?>" readonly></td>
              <td><input type="text" class="form-control claseModificar" name="interrumpida[$contador]" id="exampleInputEmail1" placeholder="HAS BEEN INTERRUPTED?" value="<?echo $row_certificacion['interrumpida']?>" readonly></td>
            </tr>
          <?php $contador++; } ?> 
          
   
        </table>




      </td>
    </tr>
    <tr>
      <td colspan="2">
        <p>12.-  ACCORDING THE CERTIFICATIONS, IN ITS MOST RECENT INTERNAL AND EXTERNAL EVALUATIONS, HOW MANY CASES OF NON COMPLIANCE WERE INDENTIFIED? PLEASE EXPLAIN IF THEY HAVE BEEN RESOLVED OR WHAT THEIR STATUS IS?</p>
        <textarea name="resp12" id="" class="form-control" value="" readonly><?php echo $row_solicitud['resp12']; ?></textarea>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <p>13.- OF THE APPLICANT’S TOTAL TRADING DURING THE PREVIOUS CYCLE, WHAT PERCENTAGE WAS CONDUCTED UNDER THE SCHEMES OF CERTIFICATION FOR ORGANIC, FAIR TRADE AND/OR THE SMALL PRODUCERS’ SYMBOL?</p>
        <textarea name="resp13" id="" class="form-control" value="" readonly><?php echo $row_solicitud['resp13']; ?></textarea>
      </td>
    </tr>

      <td colspan="2" name="tablaOculta">
        <p>14.- DID YOU HAVE SPP PURCHASES DURING THE PREVIOUS CERTIFICATION CYCLE?</p>
        <?php
          if($row_solicitud['resp14'] == 'SI'){
              //echo "SI <input type='radio' name='op_resp14'  checked readonly>";
            /*echo "</div>";
            echo "<div class='col-xs-6'>";
              echo "<p class='text-center alert alert-danger'>NO</p>";
              echo "NO <input type='radio' name='op_resp14'  readonly>";
            echo "</div>";*/
        ?>
          <div class="col-xs-6">
            <p class='text-center alert alert-success'><span class='glyphicon glyphicon-ok' aria-hidden='true'></span> YES</p>
          </div>
          <div class="col-xs-6">
            <?php 
              if(empty($row_solicitud['resp14_15'])){
             ?>
              <p class="alert alert-danger">No response was provided.</p>
            <?php 
              }else if($row_solicitud['resp14_15'] == "HASTA $3,000 USD"){
             ?>
              <p class="alert alert-info">UP TO  $3,000 USD</p>
            <?php 
              }else if($row_solicitud['resp14_15'] == "ENTRE $3,000 Y $10,000 USD"){
             ?>
             <p class="alert alert-info">BETWEEN $3,000 AND $10,000 USD</p>
            <?php 
              }else if($row_solicitud['resp14_15'] == "ENTRE $10,000 A $25,000 USD"){
             ?>
             <p class="alert alert-info">BETWEEN $10,000 AND $25,000 USD</p>
            <?php 
              }else if($row_solicitud['resp14_15'] != "HASTA $3,000 USD" && $row_solicitud['resp14_15'] != "ENTRE $3,000 Y $10,000 USD" && $row_solicitud['resp14_15'] != "ENTRE $10,000 A $25,000 USD"){
             ?>
             <p class="alert alert-info"><?php echo $row_solicitud['resp14_15']; ?></p>
             
            <?php 
              }
             ?>
          </div>
        <?php
          }else if($row_solicitud['resp14'] == 'NO'){
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
      <td colspan="2">
        <p>16.- ESTIMATED DATE FOR BEGINNING TO USE THE SMALL PRODUCERS’ SYMBOL</p>
        <textarea name="resp16" id="" class="form-control" value="" readonly><?php echo $row_solicitud['resp16']; ?></textarea>
      </td>
    </tr>

    <tr class="text-center alert alert-success">
      <td colspan="2">
        INFORMATION ON PRODUCTS FOR WHICH APPLICANT WISHES TO USE SYMBOL
        <sup>6</sup>
      </td>
    </tr>

    <tr>
      <td colspan="2">

        <table class="table table-bordered" id="tablaProductos">
          <tr>
            <td>Product</td>
            <td>Total Estimated Volume to be Sold</td>
            <td>Volume of Finished Product</td>
            <td>Volume of Raw Material</td>
            <td>Country/Countries of Origin (<small>Please separate comma</small>)</td>
            <td>Country/Countries of Destination (<small>Please separate comma</small>)</td>

            <!--<td>
              <button type="button" onclick="tablaProductos()" class="btn btn-primary" aria-label="Left Align">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
              </button>
              
            </td>-->          
          </tr>

          <?php 
          $query_producto_detalle = "SELECT * FROM productos WHERE idsolicitud_registro = $_GET[idsolicitud]";
          $producto_detalle = mysql_query($query_producto_detalle, $dspp) or die(mysql_error());
          $contador = 0;
          while($row_producto = mysql_fetch_assoc($producto_detalle)){
            ?>

          <tr>
            <td>
              <textarea name="producto[$contador]" id="" class="form-control" readonly><?php echo $row_producto['producto']; ?></textarea>
            </td>
            <td>
              <textarea name="volumenEstimado[$contador]" id="" class="form-control" readonly><?php echo $row_producto['volumenEstimado']; ?></textarea>
            </td>
            <td>
              <textarea name="volumenTerminado[$contador]" id="" class="form-control" readonly><?php echo $row_producto['volumenTerminado']; ?></textarea>
            </td>
            <td>
              <textarea name="materia[$contador]" id="" class="form-control" readonly><?php echo $row_producto['materia']; ?></textarea>
            </td>
            <td>
              <textarea class="form-control" name="paisOrigen[$contador]" id="exampleInputEmail1" placeholder="Origin" readonly><?echo $row_producto['origen']?></textarea>
            </td>         
            <td>
              <textarea class="form-control" name="paisDestino[$contador]" id="exampleInputEmail1" placeholder="Destination" readonly><?echo $row_producto['destino']?></textarea>
            </td>

          </tr>

          <?php $contador++; }?>    
          <tr>
            <td colspan="8">
              <h6><sup>6</sup> Information provided in this section will be handled with complete confidentiality. Please insert additional lines necessary</h6>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr class="text-center alert alert-success">
      <td colspan="2">COMMITMENTS</td>
    </tr>
    <tr>
      <td colspan="2">
        <p>1. By signing and sending in this document, the applicant expresses its interest in receiving a proposal for Registration with the Small Producers’ Symbol.</p>
        <p>2. The registration process will begin when it is confirmed that the payment corresponding to the proposal has been received.</p>
        <p>3. The fact that this application is delivered and received does not guarantee that the results of the registration process will be positive.</p>
        <p>4. The applicant will become familiar with and comply with all the applicable requirements in the General Standard of the Small Producers’ Symbol for Buyers, Collective Trading Companies owned by Small Producers’ Organizations, Intermediaries and Maquila Companies, including both Critical and Minimum Criteria, and independently of the type of evaluation conducted.</p>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <p>
          Name of the person who is responsible for the accuracy of the information on this form, and who, on behalf of the Applicant, will follow up on the application:
        </p>

        <input type="text" name="responsable" class="form-control" value="<?php echo $row_solicitud['responsable'] ?>" readonly>
      </td>
    </tr>
    <tr style="background-color:#ccc">
      <td colspan="2">
        <p>Entity who receives the application:</p>
        <input type="text" name="nombreOC" class="form-control" value="<?php echo $row_solicitud['nombreOC'] ?>" readonly>
      </td>
    </tr>
    <tr>
      <td style="border:hidden">
        <div class="col-xs-12">
          <input type="hidden" name="MM_insert" value="form1">
          <input type="hidden" name="fecha_elaboracion" value="<?php echo time()?>">
          <input type="hidden" name="status_publico" value="<?php echo $estadoPublico;?>">
          <input type="hidden" name="status_interno" value="<?php echo $estadoInterno;?>">
          <input type="hidden" name="mensaje" value="Acción agregada correctamente" />
          <input type="hidden" name="idcom" value="<?php echo $_SESSION['idcom']?>">
          <input type="hidden" name="abreviacion" value="<?php echo $row_com['abreviacion'];?>">
          <input type="hidden" name="nombreCOM" value="<?php echo $row_com['nombre']; ?>">
          <input type="hidden" name="paisCOM" value="<?php echo $row_com['pais']; ?>">
        </div>

          <!--<button style="width:200px;" class="btn btn-primary" type="submit" value="Enviar Solicitud" aria-label="Left Align" onclick="return validar()">
            <span class="glyphicon glyphicon-open-file" aria-hidden="true"></span> Enviar
          </button>-->

        <!--<input type="submit" class="btn btn-primary" style="width:200px" value="Enviar Solicitud">-->
      </td>
    </tr>


  </tbody>
</table>
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
    cell4.innerHTML = '<div class="col-xs-6">SI<input type="radio" class="form-control" name="interrumpida['+contador+']" value="SI"></div><div class="col-xs-6">NO<input type="radio" class="form-control" name="interrumpida['+contador+']" value="NO"></div>';
    }
  } 
  function tablaEmpresas()
  {
    contador++;
  var table = document.getElementById("tablaEmpresas");
    {
    var row = table.insertRow(2);
    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);


    cell1.innerHTML = '<input type="text" class="form-control" name="resp6_empresa['+contador+']" id="exampleInputEmail1" placeholder="EMPRESA">';
    cell2.innerHTML = '<input type="text" class="form-control" name="resp6_servicio['+contador+']" id="exampleInputEmail1" placeholder="SERVICIO">';

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

  function mostrar_empresas(){
    document.getElementById('contenedor_tablaEmpresas').style.display = 'block';
  }
  function ocultar_empresas()
  {
    document.getElementById('contenedor_tablaEmpresas').style.display = 'none';
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
    




    cell1.innerHTML = '<input type="text" class="form-control" name="producto['+cont+']" id="exampleInputEmail1" placeholder="Producto">';
    
    cell2.innerHTML = '<input type="text" class="form-control" name="volumenEstimado['+cont+']" id="exampleInputEmail1" placeholder="Volumen">';
    
    cell3.innerHTML = '<input type="text" class="form-control" name="volumenTerminado['+cont+']" id="exampleInputEmail1" placeholder="Volumen">';
    
    cell4.innerHTML = '<input type="text" class="form-control" name="materia['+cont+']" id="exampleInputEmail1" placeholder="Materia">';
    
    //cell4.innerHTML = '<input type="text" class="form-control" name="destino['+cont+']" id="exampleInputEmail1" placeholder="Destino">';
    
    //cell6.innerHTML = 'SI <input type="radio" name="marca_propia'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="marca_propia'+cont+'['+cont+']" id="" value="NO">';

    //cell5.innerHTML = '<select  class="form-control chosen-select-deselect" data-placeholder="Buscar por país" name="paisOrigen0[]" id="" multiple><option value="">Selecciona un país</option></select>';
    
    cell5.innerHTML = '<textarea class="form-control" name="paisOrigen['+cont+']" id="" cols="30" rows="3" placeholder="Pais de origen"></textarea>';

    cell6.innerHTML = '<textarea class="form-control" name="paisDestino['+cont+']" id="" cols="30" rows="3" placeholder="Pais de destino"></textarea>';


    }

  } 

</script>

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