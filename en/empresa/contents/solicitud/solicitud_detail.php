<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php');

mysql_select_db($database_dspp, $dspp);

if (!isset($_SESSION)) {
  session_start();
  
  $redireccion = "../index.php?EMPRESA";

  if(!$_SESSION["autentificado"]){
    header("Location:".$redireccion);
  }
}

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

$idsolicitud_registro = $_GET['idsolicitud'];
$charset='utf-8'; 
$ruta_croquis = "../../archivos/empresaArchivos/croquis/";

if(isset($_POST['actualizar_solicitud']) && $_POST['actualizar_solicitud'] == 1){

  /*
  SE ACTUALIZA LA SOLICITUD
  LA INFORMACION DE empresa
  NUMERO DE SOCIOS
  CONTACTOS
  PRODUCTOS
  CERTIFICACIONES
  */


  if(isset($_POST['op_preg12'])){
    $op_preg12 = $_POST['op_preg12'];
  }else{
    $op_preg12 = "";
  }

///CAPTURAMOS SI HUBO VENTAS ////
  if(isset($_POST['preg6'])){
    $preg6 = $_POST['preg6'];
  }else{
    $preg6 = "";
  }
  if(isset($_POST['preg13'])){
    $preg13 = $_POST['preg13'];
  }else{
    $preg13 = "";
  }
  if(isset($_POST['preg14'])){
    $preg14 = $_POST['preg14'];
  }else{
    $preg14 = "";
  }


  /*if(!empty($_FILES['op_preg15']['name'])){
      $_FILES["op_preg15"]["name"];
        move_uploaded_file($_FILES["op_preg15"]["tmp_name"], $ruta_croquis.date("Ymd H:i:s")."_".$_FILES["op_preg15"]["name"]);
        $croquis = $ruta_croquis.basename(date("Ymd H:i:s")."_".$_FILES["op_preg15"]["name"]);
  }else{
    $croquis = NULL;
  }*/
  if(!empty($_POST['comprador'])){
    $comprador = $_POST['comprador'];
  }else{
    $comprador = '';
  }
  if(!empty($_POST['intermediario'])){
    $intermediario = $_POST['intermediario'];
  }else{
    $intermediario = '';
  }
  if(!empty($_POST['maquilador'])){
    $maquilador = $_POST['maquilador'];
  }else{
    $maquilador = '';
  }


  if(!empty($_POST['produccion'])){
    $produccion = $_POST['produccion'];
  }else{
    $produccion = '';
  }
  if(!empty($_POST['procesamiento'])){
    $procesamiento = $_POST['procesamiento'];
  }else{
    $procesamiento = '';
  }
  if(!empty($_POST['importacion'])){
    $importacion = $_POST['importacion'];
  }else{
    $importacion = '';
  }

  /*if(!empty($_FILES['preg9']['name'])){
      $_FILES["preg9"]["name"];
        move_uploaded_file($_FILES["preg9"]["tmp_name"], $ruta_croquis.date("Ymd H:i:s")."_".$_FILES["preg9"]["name"]);
        $preg9 = $ruta_croquis.basename(date("Ymd H:i:s")."_".$_FILES["preg9"]["name"]);
  }else{
    $preg9 = $_POST['preg9'];
  }*/


  // ACTUALIZAMOS LA INFORMACION DE LA SOLICITUD
  $updateSQL = sprintf("UPDATE solicitud_registro SET comprador_final = %s, intermediario = %s, maquilador = %s, preg1 = %s, preg2 = %s, preg3 = %s, preg4 = %s, produccion = %s, procesamiento = %s, importacion = %s, preg6 = %s, preg7 = %s, preg8 = %s, preg10 = %s, preg12 = %s, preg14 = %s, preg15 = %s WHERE idsolicitud_registro = %s",
        GetSQLValueString($comprador, "int"),
        GetSQLValueString($intermediario, "int"),
        GetSQLValueString($maquilador, "int"),
         GetSQLValueString($_POST['preg1'], "text"),
         GetSQLValueString($_POST['preg2'], "text"),
         GetSQLValueString($_POST['preg3'], "text"),
         GetSQLValueString($_POST['preg4'], "text"),
         GetSQLValueString($produccion, "int"),
         GetSQLValueString($procesamiento, "int"),
         GetSQLValueString($importacion, "int"),
         GetSQLValueString($preg6, "text"),
         GetSQLValueString($_POST['preg7'], "text"),
         GetSQLValueString($_POST['preg8'], "text"),

         GetSQLValueString($_POST['preg10'], "text"),
         //GetSQLValueString($op_preg12, "text"),
         //GetSQLValueString($op_preg13, "text"),
         GetSQLValueString($_POST['preg12'], "text"),
         //GetSQLValueString($preg13, "text"),
         GetSQLValueString($preg14, "text"),
         GetSQLValueString($_POST['preg15'], "text"),
         GetSQLValueString($idsolicitud_registro, "int"));
  $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());



  // ACTUALIZAMOS LA INFORMACION DE LA empresa
  $updateSQL = sprintf("UPDATE empresa SET nombre = %s, pais = %s, direccion_oficina = %s, email = %s, telefono = %s, sitio_web = %s, razon_social = %s, direccion_fiscal = %s, rfc = %s, ruc = %s, comprador = %s, intermediario = %s, maquilador = %s WHERE idempresa = %s",
    GetSQLValueString($_POST['nombre'], "text"),
    GetSQLValueString($_POST['pais'], "text"),
    GetSQLValueString($_POST['direccion_oficina'], "text"),
    GetSQLValueString($_POST['email'], "text"),
    GetSQLValueString($_POST['telefono'], "text"),
    GetSQLValueString($_POST['sitio_web'], "text"),
    GetSQLValueString($_POST['razon_social'], "text"),
    GetSQLValueString($_POST['direccion_fiscal'], "text"),
    GetSQLValueString($_POST['rfc'], "text"),
    GetSQLValueString($_POST['ruc'], "text"),
    GetSQLValueString($comprador, "int"),
    GetSQLValueString($intermediario, "int"),
    GetSQLValueString($maquilador, "int"),
    GetSQLValueString($_POST['idempresa'], "int"));
  $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());



  ////ACTUALIZAMOS LOS PORCENTAJES DE VENTAS

  if(!empty($_POST['organico']) || !empty($_POST['comercio_justo']) || !empty($_POST['spp']) || !empty($_POST['sin_certificado'])){
    $row_ventas = mysql_query("SELECT * FROM porcentaje_productoVentas WHERE idsolicitud_registro = $_GET[idsolicitud]", $dspp) or die(mysql_error());
    $existe_venta = mysql_num_rows($row_ventas);
    if($existe_venta){
      $updateSQL = sprintf("UPDATE porcentaje_productoVentas SET organico = %s, comercio_justo = %s, spp = %s, sin_certificado = %s WHERE idsolicitud_registro = %s",
        GetSQLValueString($_POST['organico'], "text"),
        GetSQLValueString($_POST['comercio_justo'], "text"),
        GetSQLValueString($_POST['spp'], "text"),
        GetSQLValueString($_POST['sin_certificado'], "text"),
        GetSQLValueString($idsolicitud_registro, "int"));
      $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());
    }else{
      $insertSQL = sprintf("INSERT INTO porcentaje_productoVentas (organico, comercio_justo, spp, sin_certificado, idsolicitud_registro, idempresa) VALUES (%s, %s, %s, %s, %s, %s)",
        GetSQLValueString($_POST['organico'], "text"),
        GetSQLValueString($_POST['comercio_justo'], "text"),
        GetSQLValueString($_POST['spp'], "text"),
        GetSQLValueString($_POST['sin_certificado'], "text"),
        GetSQLValueString($idsolicitud_registro, "int"),
        GetSQLValueString($_POST['idempresa'], "int"));
      $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
    }

  }

  /*************************** INICIA INSERTAR CERTIFICACIONES ***************************/

    if(isset($_POST['certificadora'])){
      $certificadora = $_POST['certificadora'];
    }else{
      $certificadora = NULL;
    }

    if(isset($_POST['ano_inicial'])){
      $ano_inicial = $_POST['ano_inicial'];
    }else{
      $ano_inicial = NULL;
    }

    if(isset($_POST['interrumpida'])){
      $interrumpida = $_POST['interrumpida'];
    }else{
      $interrumpida = NULL;
    }

    if(isset($_POST['certificacion'])){
      $certificacion = $_POST['certificacion'];
      
      for($i=0;$i<count($certificacion);$i++){
        if($certificacion[$i] != NULL){
          #for($i=0;$i<count($certificacion);$i++){
          $insertSQL = sprintf("INSERT INTO certificaciones (idsolicitud_registro, certificacion, certificadora, ano_inicial, interrumpida) VALUES (%s, %s, %s, %s, %s)",
              GetSQLValueString($idsolicitud_registro, "int"),
              GetSQLValueString(strtoupper($certificacion[$i]), "text"),
              GetSQLValueString(strtoupper($certificadora[$i]), "text"),
              GetSQLValueString($ano_inicial[$i], "text"),
              GetSQLValueString($interrumpida[$i], "text"));

          $Result = mysql_query($insertSQL, $dspp) or die(mysql_error());
          #}
        }
      }

    }
  /*************************** INICIA INSERTAR CERTIFICACIONES ***************************/
    // SE ACTUALIZAN LAS CERTIFICACIONES

    if(isset($_POST['idcertificacion'])){
      $idcertificacion = $_POST['idcertificacion'];
      if(isset($_POST['certificacion_actual'])){
        $certificacion_actual = $_POST['certificacion_actual'];
      }else{
        $certificacion_actual = NULL;
      }


      if(isset($_POST['certificadora_actual'])){
        $certificadora_actual = $_POST['certificadora_actual'];
      }else{
        $certificadora_actual = NULL;
      }

      if(isset($_POST['ano_inicial_actual'])){
        $ano_inicial_actual = $_POST['ano_inicial_actual'];
      }else{
        $ano_inicial_actual = NULL;
      }

      if(isset($_POST['interrumpida_actual'])){
        $interrumpida_actual = $_POST['interrumpida_actual'];
      }else{
        $interrumpida_actual = NULL;
      }


      for($i=0;$i<count($certificacion_actual);$i++){
        if($certificacion_actual[$i] != NULL){
          #for($i=0;$i<count($certificacion_actual);$i++){

          $updateSQL = sprintf("UPDATE certificaciones SET certificacion = %s, certificadora = %s, ano_inicial = %s, interrumpida = %s WHERE idcertificacion = %s",
            GetSQLValueString(strtoupper($certificacion_actual[$i]), "text"),
            GetSQLValueString(strtoupper($certificadora_actual[$i]), "text"),
            GetSQLValueString($ano_inicial_actual[$i], "text"),
            GetSQLValueString($interrumpida_actual[$i], "text"),
            GetSQLValueString($idcertificacion[$i], "int"));

          //$updateSQL = "UPDATE certificaciones SET certificacion= '".$certificacion[$i]."', certificadora='".$certificadora_actual[$i]."', ano_inicial= '".$ano_inicial_actual[$i]."', interrumpida= '".$interrumpida[$i]."' WHERE idcertificacion= '".$idcertificacion[$i]."'";

          $Result1 = mysql_query($updateSQL, $dspp) or die(mysql_error());
          }
      }
    }
    /*************************** INICIA INSERTAR PRODUCTOS ***************************/

    if(isset($_POST['volumen_estimado'])){
      $volumen_estimado = $_POST['volumen_estimado'];
    }

    if(isset($_POST['volumen_terminado'])){
      $volumen_terminado = $_POST['volumen_terminado'];
    }

    if(isset($_POST['volumen_materia'])){
      $volumen_materia = $_POST['volumen_materia'];
    }

    if(isset($_POST['destino'])){
      $destino = $_POST['destino'];
    }

    if(isset($_POST['origen'])){
      $origen = $_POST['origen'];
    }

    if(isset($_POST['producto'])){
      $producto = $_POST['producto'];


    for ($i=0;$i<count($producto);$i++) { 
      if($producto[$i] != NULL){


          //$terminado = $_POST[$array1[$i]];
          //$marca_propia = $_POST[$array2[$i]];
          //$marca_cliente = $_POST[$array3[$i]];
          //$sin_cliente = $_POST[$array4[$i]];

          $str = iconv($charset, 'ASCII//TRANSLIT', $producto[$i]);
          $producto[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

          $str = iconv($charset, 'ASCII//TRANSLIT', $destino[$i]);
          $destino[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

          $str = iconv($charset, 'ASCII//TRANSLIT', $origen[$i]);
          $origen[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));


            $insertSQL = sprintf("INSERT INTO productos (idempresa, idsolicitud_registro, producto, volumen_estimado, volumen_terminado, volumen_materia, origen, destino) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
              GetSQLValueString($_POST['idempresa'], "int"),
                  GetSQLValueString($idsolicitud_registro, "int"),
                  GetSQLValueString($producto[$i], "text"),
                  GetSQLValueString($volumen_estimado[$i], "text"),
                  GetSQLValueString($volumen_terminado[$i], "text"),
                  GetSQLValueString($volumen_materia[$i], "text"),
                  GetSQLValueString($origen[$i], "text"),
                  GetSQLValueString($destino[$i], "text"));

          $Result = mysql_query($insertSQL, $dspp) or die(mysql_error());
      }
    }

    }
    /*$marca_propia = $_POST['marca_propia'];
    $marca_cliente = $_POST['marca_cliente'];
    $sin_cliente = $_POST['sin_cliente'];*/


    /***************************** TERMINA INSERTAR PRODUCTOS ******************************/


    // SE ACTUALIZAN LOS PRODUCTOS
      $producto_2 = $_POST['producto_2'];
      $volumen_estimado_2 = $_POST['volumen_estimado_2'];
      $volumen_materia_2 = $_POST['volumen_materia_2'];
      $volumen_terminado_2 = $_POST['volumen_terminado_2'];
      $origen_2 = $_POST['origen_2'];
      $destino_2 = $_POST['destino_2'];
      $idproducto = $_POST['idproducto'];
      /*$marca_propia = $_POST['marca_propia'];
      $marca_cliente = $_POST['marca_cliente'];
      $sin_cliente = $_POST['sin_cliente'];*/

    for ($i=0;$i<count($producto_2);$i++) { 
      if($producto_2[$i] != NULL){


          $str = iconv($charset, 'ASCII//TRANSLIT', $producto_2[$i]);
          $producto_2[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

          $str = iconv($charset, 'ASCII//TRANSLIT', $origen_2[$i]);
          $origen_2[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));
         
          $str = iconv($charset, 'ASCII//TRANSLIT', $destino_2[$i]);
          $destino_2[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));


      $updateSQL = sprintf("UPDATE productos SET producto = %s, volumen_estimado = %s, volumen_terminado = %s, volumen_materia = %s, origen = %s, destino = %s WHERE idproducto = %s",
        GetSQLValueString($producto_2[$i], "text"),
        GetSQLValueString($volumen_estimado_2[$i], "text"),
        GetSQLValueString($volumen_terminado_2[$i], "text"),
        GetSQLValueString($volumen_materia_2[$i], "text"),
        GetSQLValueString($origen_2[$i], "text"),
        GetSQLValueString($destino_2[$i], "text"),
        GetSQLValueString($idproducto[$i], "int"));
      $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

      }
    }


  $mensaje = "Correctly Updated Data";
}
 

$query = "SELECT solicitud_registro.*, empresa.idempresa AS 'id_empresa', empresa.nombre, empresa.spp AS 'spp_empresa', empresa.sitio_web, empresa.email, empresa.telefono, empresa.pais, empresa.ciudad, empresa.razon_social, empresa.direccion_oficina, empresa.direccion_fiscal, empresa.rfc, empresa.ruc, oc.abreviacion AS 'abreviacionOC', porcentaje_productoVentas.* FROM solicitud_registro INNER JOIN empresa ON solicitud_registro.idempresa = empresa.idempresa INNER JOIN oc ON solicitud_registro.idoc = oc.idoc LEFT JOIN porcentaje_productoVentas ON solicitud_registro.idsolicitud_registro = porcentaje_productoVentas.idsolicitud_registro WHERE solicitud_registro.idsolicitud_registro = $idsolicitud_registro";
$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
$solicitud = mysql_fetch_assoc($ejecutar);

$row_pais = mysql_query("SELECT * FROM paises", $dspp) or die(mysql_error());
?>

<div class="row">
  <?php 
  if(isset($mensaje)){
  ?>
  <div class="col-md-12 alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 style="font-size:14px;" class="text-center"><?php echo $mensaje; ?><h4/>
  </div>
  <?php
  }
  ?>
</div>

<div class="row" style="font-size:12px;">

  <form action="" name="" method="POST" enctype="multipart/form-data">
    <fieldset>
      <div class="col-md-12 alert alert-primary" style="padding:7px;">
        <h3 class="text-center">Application for Buyers´, Registration</h3>
      </div>


      <div class="col-md-12 text-center alert alert-success" style="padding:7px;"><b>GENERAL INFORMATION</b></div>

      <div class="col-lg-12 alert alert-info" style="padding:7px;">
        <div class="col-md-4">
          <div class="col-xs-12">
            <b>Send to Certification Entity:</b>
          </div>
          <div class="col-xs-12">
            <input type="text" class="form-control" value="<?php echo $solicitud['abreviacionOC']; ?>" readonly>
          </div>
        </div>
        <div class="col-md-4">
          <div class="col-xs-12">
            <b>TYPE OF APPLICATION</b>
          </div>
          <div class="col-xs-6">
            <input type="text" class="form-control" value="<?php echo $solicitud['tipo_solicitud']; ?>"readonly>
          </div>
          
        </div>
        <div class="col-md-4">

          <input type="hidden" name="actualizar_solicitud" value="1">
          <button style="color:white" type="submit" class="btn btn-warning form-control"><span class="glyphicon glyphicon-refresh" aria-hidden="true"></span> UPDATE APPLICATION</button>
        </div>

      </div>

      <!------ INICIA INFORMACION GENERAL Y DATOS FISCALES ------>
      <div class="col-lg-12">
        <div class="col-md-6">
          <div class="col-md-12 text-center alert alert-warning" style="padding:7px;">GENERAL INFORMATION</div>
          <label for="fecha_elaboracion">DATE OF ELABORATION</label>
          <input type="text" class="form-control" id="fecha_elaboracion" name="fecha_elaboracion" value="<?php echo date('Y-m-d', time()); ?>" readonly>  

          <label for="spp">SPP IDENTIFICATION CODE(#SPP): </label>
          <input type="text" class="form-control" id="spp" name="spp" value="<?php echo $solicitud['spp_empresa']; ?>" readonly>

          <label for="nombre">COMPANY NAME: </label>
          <textarea name="nombre" id="nombre" class="form-control"><?php echo $solicitud['nombre']; ?></textarea>

          <label for="pais">COUNTRY:</label>
           <select name="pais" id="pais" class="form-control">
            <option value="">Select a Country</option>
            <?php 
            while($pais = mysql_fetch_assoc($row_pais)){
              if(utf8_encode($pais['nombre']) == $solicitud['pais']){
                echo "<option value='".utf8_encode($pais['nombre'])."' selected>".utf8_encode($pais['nombre'])."</option>";
              }else{
                echo "<option value='".utf8_encode($pais['nombre'])."'>".utf8_encode($pais['nombre'])."</option>";
              }
            }
             ?>
           </select>

          <label for="direccion_oficina">COMPLETE ADDRESS FOR COMPANY LOCATION (STREET, DISTRICT, TOWN / CITY, REGION):</label>
          <textarea name="direccion_oficina" id="direccion_oficina"  class="form-control"><?php echo $solicitud['direccion_oficina']; ?></textarea>

          <label for="email">EMAIL:</label>
          <input type="email" class="form-control" id="email" name="email" value="<?php echo $solicitud['email']; ?>">

          <label for="telefono">COMPANY TELEPHONES(COUNTRY CODE+AREA CODE+NUMBER):</label>
          <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo $solicitud['telefono']; ?>">  

          <label for="sitio_web">WEBSITE:</label>
          <input type="text" class="form-control" id="sitio_web" name="sitio_web" value="<?php echo $solicitud['sitio_web']; ?>">

        </div>

        <div class="col-md-6">
          <div class="col-md-12 text-center alert alert-warning" style="padding:7px;">FISCAL DATA</div>

          <label for="razon_social">RBUSINESS NAME</label>
          <input type="text" class="form-control" id="razon_social" name="razon_social" value="<?php echo $solicitud['razon_social']; ?>">

          <label for="direccion_fiscal">FISCAL ADDRESS</label>
          <textarea class="form-control" name="direccion_fiscal" id="direccion_fiscal"><?php echo $solicitud['direccion_fiscal']; ?></textarea>

          <label for="rfc">RFC</label>
          <input type="text" class="form-control" id="rfc" name="rfc" value="<?php echo $solicitud['rfc']; ?>">

          <label for="ruc">RUC</label>
          <input type="text" class="form-control" id="ruc" name="ruc" value="<?php echo $solicitud['ruc']; ?>">
        </div>
      </div>
      <!------ INICIA INFORMACION GENERAL Y DATOS FISCALES ------>


      <!------ INICIA INFORMACION CONTACTOS Y AREA ADMINISTRATIVA ------>
      <div class="col-lg-12">
        <div class="col-md-6">
          <div class="col-md-12 text-center alert alert-warning" style="padding:7px;">CONTACT PERSON(S) OF APPLICATION</div>

          <label for="persona1">CONTACT PERSON(S)</label>
          <input type="text" class="form-control" id="persona1" value="<?php echo $solicitud['contacto1_nombre']; ?>"  readonly>
          <input type="text" class="form-control" id="" value="<?php echo $solicitud['contacto2_nombre']; ?>" placeholder="Name Person 2" readonly>

          <label for="cargo">POSITION(S)</label>
          <input type="text" class="form-control" id="cargo" value="<?php echo $solicitud['contacto1_cargo']; ?>" placeholder="* Cargo Persona 1" readonly>
          <input type="text" class="form-control" id="" value="<?php echo $solicitud['contacto2_cargo']; ?>" palceholder="Cargo Persona 2" readonly>

          <label for="email">EMAIL</label>
          <input type="email" class="form-control" id="email" value="<?php echo $solicitud['contacto1_email']; ?>" placeholder="* Email Person 1" readonly>
          <input type="email" class="form-control" id="" value="<?php echo $solicitud['contacto2_email']; ?>" placeholder="Email Person 2" readonly>

          <label for="telefono">TELEPHONE(S)  FOR CONTACT PERSON(S):</label>
          <input type="text" class="form-control" id="telefono" value="<?php echo $solicitud['contacto1_telefono']; ?>" placeholder="* Telephone Person 1" readonly>
          <input type="text" class="form-control" id="" value="<?php echo $solicitud['contacto2_telefono']; ?>" placeholder="Telephone Person 2" readonly>

        </div>

        <div class="col-md-6">
          <div class="col-md-12 text-center alert alert-warning" style="padding:7px;">PERSON(S) OF THE ADMINISTRATIVE AREA:</div>

          <label for="persona_adm">PERSON(S) OF THE ADMINISTRATIVE AREA</label>
          <input type="text" class="form-control" id="persona_adm" value="<?php echo $solicitud['adm1_nombre']; ?>" placeholder="Name Person 1" readonly>
          <input type="text" class="form-control" id="" value="<?php echo $solicitud['adm2_nombre']; ?>" placeholder="Name Person 2" readonly>

          <label for="email_adm">EMAIL</label>
          <input type="email" class="form-control" id="email_adm" value="<?php echo $solicitud['adm1_email']; ?>" placeholder="Email Person 1" readonly>
          <input type="email" class="form-control" id="" value="<?php echo $solicitud['adm2_email']; ?>" placeholder="Email Person 2" readonly>

          <label for="telefono_adm">TELEPHONE</label>
          <input type="text" class="form-control" id="telefono_adm" value="<?php echo $solicitud['adm1_telefono']; ?>" placeholder="Telephone Person 1" readonly>
          <input type="text" class="form-control" id="" value="<?php echo $solicitud['adm2_telefono']; ?>" placeholder="Telephone Person 2" readonly>
        </div>
      </div>
      <!------ FIN INFORMACION CONTACTOS Y AREA ADMINISTRATIVA ------>



      <!------ INICIA INFORMACION DATOS DE OPERACIÓN ------>

      <div class="col-md-12 alert alert-info">
        <div>
          <label for="alcance_opp">
            SELECT TYPE OF COMPANY SPP FOR WHICH REGISTRATION IS SOUGHT. INTERMEDIARY MAY NOT SIGN SPP IF NOT HAVE A BUYER OR FINAL SPP REGISTERED REGISTRATION PROCESS.           </label>
        </div>

        <div class="checkbox">
          <label class="col-sm-4">
            <input type="checkbox"name="comprador" <?php if($solicitud['comprador_final']){echo "checked"; } ?> value="1"> FINAL BUYER
          </label>
          <label class="col-sm-4">
            <input type="checkbox"name="intermediario" <?php if($solicitud['intermediario']){echo "checked"; } ?> value="1"> INTERMEDIARY
          </label>
          <label class="col-sm-4">
            <input type="checkbox"name="maquilador" <?php if($solicitud['maquilador']){echo "checked"; } ?> value="1"> MAQUILA COMPANY
          </label>
        </div>
      </div>



      <div class="col-md-12 text-center alert alert-success" style="padding:7px;">INFORMATION ON OPERATION</div>

      <div class="col-lg-12">
        <div class="col-md-12">
          <label for="preg1">
            1.  FROM WHICH SMALL PRODUCERS’ ORGANIZATIONS DO YOU MAKE PURCHASES OR ATTEMPT TO DO SO UNDER THE SMALL PRODUCERS’ SYMBOL SCHEME? 
          </label>
          <textarea name="preg1" id="preg1" class="form-control"><?php echo $solicitud['preg1']; ?></textarea>

          <label for="preg2">
            2.  WHO IS/ARE THE OWNER(S) OF THE COMPANY? 
          </label>
          <textarea name="preg2" id="preg2" class="form-control"><?php echo $solicitud['preg2']; ?></textarea>


          <label for="preg3">
            3.  SPECIFY WHICH PRODUCT (S) YOU WANT TO INCLUDE IN THE CERTIFICATE OF THE SMALL PRODUCERS’  SYMBOL FOR WHICH THE CERTIFICATION ENTITY WILL CONDUCT THE ASSESSMENT.<sup>4 Check the Regulation on Graphics and the List of  Optional Complementary Criteria.</sup>>
          </label>
          <input type="text" class="form-control" id="preg3" name="preg3" value="<?php echo $solicitud['preg3']; ?>">

          <label for="preg4">
            4.  IF YOUR COMPANY IS A FINAL BUYER, MENTION IF YOUR ORGANIZATION WOULD LIKE TO INCLUDE AN ADDITIONAL DESCRIPTOR FOR COMPLEMENTARY USE WITH THE GRAPHIC DESIGN OF THE SMALL PRODUCERS’ SYMBOL 
          </label>
          <textarea name="preg4" id="preg4" class="form-control"><?php echo $solicitud['preg4']; ?></textarea>

          <div >
            <label for="alcance_opp">
              5. SELECT THE SCOPE OF THE COMPANY:
            </label>
          </div>
          <div class="col-md-4">
            <label>PRODUCTION</label>
            <input type="checkbox" name="produccion" class="form-control" <?php if($solicitud['produccion']){echo "checked";} ?> value="1">
          </div>
          <div class="col-md-4">
            <label>PROCESSING</label>
            <input type="checkbox" name="procesamiento" class="form-control" <?php if($solicitud['procesamiento']){echo "checked";} ?> value="1">
          </div>
          <div class="col-md-4">
            <label>TRADING</label>
            <input type="checkbox" name="importacion" class="form-control" <?php if($solicitud['importacion']){echo "checked";} ?> value="1">
          </div>

        <p><b>6.  SPECIFY IF YOUR COMPANY SUBCONTRACT THE SERVICES OF PROCESSING PLANTS, TRADING COMPANIES OR COMPANIES THAT CARRY OUT THE IMPORT OR EXPORT, IF THE ANSWER IS AFFIRMATIVE, MENTION THE NAME AND THE SERVICE THAT PERFORMS</b></p>
        <div class="col-md-6">
          YES <input type="radio" class="form-control" name="preg6" onclick="mostrar_empresas()" id="preg6" <?php if($solicitud['preg6'] == 'SI'){echo "checked"; } ?> value="SI">
        </div>
        <div class="col-md-6">
          NO <input type="radio" class="form-control" name="preg6" onclick="ocultar_empresas()" id="preg6" <?php if($solicitud['preg6'] == 'NO'){echo "checked"; } ?> value="NO">
        </div>

        <p>IF THE RESPONSE IS AFFIRMATIVE, MENTION THE NAME AND THE SERVICE THAT IT REALIZES</p>
        <div id="contenedor_tablaEmpresas" class="col-md-12" style="display:none">
          <table class="table table-bordered" id="tablaEmpresas">
            <tr>
              <td>NAME OF THE COMPANY</td>
              <td>SERVICE THAT IT REALIZES</td>
            </tr>
            <?php 
            $query_subempresa = "SELECT * FROM sub_empresas WHERE idsolicitud_registro = $idsolicitud_registro";
            $subempresa_detalle = mysql_query($query_subempresa, $dspp) or die(mysql_error());
            $contador = 0;
            while($row_subempresa = mysql_fetch_assoc($subempresa_detalle)){
            ?>
            <tr class="text-center">
              <td><input type="text" class="form-control" name="subempresa[]" id="exampleInputEmail1" placeholder="EMPRESA" value="<?php echo $row_subempresa['nombre']; ?>"></td>
              <td><input type="text" class="form-control" name="servicio[]" id="exampleInputEmail1" placeholder="SERVICIO" value="<?php echo $row_subempresa['servicio']; ?>"></td>
            </tr>
            <?php 
              $contador++; 
            } 
            ?> 
          </table>  
        </div>  

          <label for="preg7">
            7.  IF YOU SUBCONTRACT THE SERVICES OF PROCESSING PLANTS, TRADING COMPANIES OR COMPANIES THAT CARRY OUT THE IMPORT OR EXPORT, INDICATE WHETHER THESE COMPANIES ARE GOING TO APPLYFOR THE REGISTRATION UNDER SPP CERTIFICATION PROGRAM. <sup>5</sup>
            <br>
            <small><sup>5</sup> Check the document General Application Guidelines SPP System.</small>
          </label>
          <textarea name="preg7" id="preg7" class="form-control"><?php echo $solicitud['preg7']; ?></textarea>

          <label for="preg8">
            8.  IN ADDITION TO YOUR MAIN OFFICES, PLEASE SPECIFY HOW MANY COLLECTION CENTERS, PROCESSING AREAS AND ADDITIONAL OFFICES YOU HAVE.
          </label>
          <textarea name="preg8" id="preg8" class="form-control"><?php echo $solicitud['preg8']; ?></textarea>

          <label for="preg9">
            9. IF YOU HAVE COLLECTION CENTERS, PROCESSING AREAS OR ADDITIONAL OFFICES, PLEASE ATTACH A GENERAL MAP INDICATING WHERE THEY ARE LOCATED.
          </label>
          <?php 
          if(empty($solicitud['preg9'])){
          ?>
            <input type="file" id="preg9" name="preg9" class="form-control">
          <?php
          }else{
          ?>
            <input type="text" name="preg9" value="<?php echo $solicitud['preg9']; ?>">
            <a href="<?php echo $solicitud['preg9']; ?>" target="_blank" class="btn btn-success form-control">Download the sketch</a>
          <?php
          }
           ?>
          <label for="preg10">
            10. IF THE APPLICANT HAS AN INTERNAL CONTROL SYSTEM FOR COMPLYING WITH THE CRITERIA IN THE GENERAL STANDARD OF THE SMALL PRODUCERS’ SYMBOL, PLEASE EXPLAIN HOW IT WORKS.
          </label>
          <textarea name="preg10" id="preg10" class="form-control"><?php echo $solicitud['preg10'] ?></textarea>

          <p class="alert alert-info">11. FILL OUT THE TABLE ACCORDING YOUR CERTIFICATIONS, (example: EU, NOP, JASS, FLO, etc)</p>
          
          <table class="table table-bordered" id="tablaCertificaciones">
            <tr>
              <td>CERTIFICATION</td>
              <td>CERTIFICATION ENTITY</td>
              <td>INITIAL YEAR OF CERTIFICATION</td>
              <td>HAS BEEN INTERRUPTED?</td>
            </tr>
            <?php 
            $query_certificacion_detalle = "SELECT * FROM certificaciones WHERE idsolicitud_registro = $idsolicitud_registro";
            $certificacion_detalle = mysql_query($query_certificacion_detalle, $dspp) or die(mysql_error());
            $contador = 0;
            while($row_certificacion = mysql_fetch_assoc($certificacion_detalle)){
            ?>
              <tr class="text-center">
                <td><input type="text" class="form-control" name="certificacion[]" id="exampleInputEmail1" placeholder="CERTIFICATION" value="<?echo $row_certificacion['certificacion']?>"></td>
                <td><input type="text" class="form-control" name="certificadora[]" id="exampleInputEmail1" placeholder="CERTIFICATION ENTITY" value="<?echo $row_certificacion['certificadora']?>"></td>
                <td><input type="text" class="form-control" name="ano_inicial[]" id="exampleInputEmail1" placeholder="AÑO INICIAL" value="<?echo $row_certificacion['ano_inicial']?>"></td>
                <td><input type="text" class="form-control" name="interrumpida[]" id="exampleInputEmail1" placeholder="HAS BEEN INTERRUPTED" value="<?echo $row_certificacion['interrumpida']?>"></td>
                <input type="hidden" name="idcertificacion[]" value="<?echo $row_certificacion['idcertificacion']?>">
              </tr>
            <?php 
              $contador++; 
            } 
            ?> 
          </table>

          <label for="preg12">
           12.  ACCORDING THE CERTIFICATIONS, IN ITS MOST RECENT INTERNAL AND EXTERNAL EVALUATIONS, HOW MANY CASES OF NON COMPLIANCE WERE INDENTIFIED? PLEASE EXPLAIN IF THEY HAVE BEEN RESOLVED OR WHAT THEIR STATUS IS?</label>
          <textarea name="preg12" id="preg12" class="form-control"><?php echo $solicitud['preg12']; ?></textarea>

          <p for="op_preg11">
            <b>13.  OF THE APPLICANT’S TOTAL TRADING DURING THE PREVIOUS CYCLE, WHAT PERCENTAGE WAS CONDUCTED UNDER THE SCHEMES OF CERTIFICATION FOR ORGANIC, FAIR TRADE AND/OR THE SMALL PRODUCERS’ SYMBOL?</b>
          </p>
          <p><i>(* Enter percentage)</i></p>
            <div class="col-xs-3">
              <label for="organico">% ORGANIC</label>
              <input type="number" step="any" class="form-control" id="organico" name="organico" value="<?php echo $solicitud['organico']; ?>" placeholder="Ej: 0.0">
            </div>
            <div class="col-xs-3">
              <label for="comercio_justo">% FAIR TRADE</label>
              <input type="number" step="any" class="form-control" id="comercio_justo" name="comercio_justo" value="<?php echo $solicitud['comercio_justo']; ?>" placeholder="Ej: 0.0">
            </div>
            <div class="col-xs-3">
              <label for="spp">SMALL PRODUCERS´ SYMBOL</label>
              <input type="number" step="any" class="form-control" id="spp" name="spp" value="<?php echo $solicitud['spp']; ?>" placeholder="Ej: 0.0">
              
            </div>
            <div class="col-xs-3">
              <label for="otro">OTHER</label>
              <input type="number" step="any" class="form-control" id="otro" name="sin_certificado" value="<?php echo $solicitud['sin_certificado']; ?>" placeholder="Ej: 0.0">
              
            </div>


          <p><b>14. DID YOU HAVE SPP PURCHASES DURING THE PREVIOUS CERTIFICATION CYCLE?</b></p>
          <div class="col-xs-12 ">
                <?php
                  if($solicitud['preg13'] == 'SI'){
                      //echo "SI <input type='radio' name='op_preg12'  checked readonly>";
                    /*echo "</div>";
                    echo "<div class='col-xs-6'>";
                      echo "<p class='text-center alert alert-danger'>NO</p>";
                      echo "NO <input type='radio' name='op_preg12'  readonly>";
                    echo "</div>";*/
                ?>
                  <div class="col-xs-6">
                    <p class='text-center alert alert-success'><span class='glyphicon glyphicon-ok' aria-hidden='true'></span> YES</p>
                  </div>
                  <div class="col-xs-6">
                    <?php 
                      if(empty($solicitud['op_preg14'])){
                     ?>
                      <p class="alert alert-danger">No response was provided.</p>
                    <?php 
                      }else if($solicitud['op_preg14'] == "HASTA $3,000 USD"){
                     ?>
                      <p class="alert alert-info">UP TO  $3,000 USD</p>
                    <?php 
                      }else if($solicitud['op_preg14'] == "ENTRE $3,000 Y $10,000 USD"){
                     ?>
                     <p class="alert alert-info">BETWEEN $3,000 AND $10,000 USD</p>
                    <?php 
                      }else if($solicitud['op_preg14'] == "ENTRE $10,000 A $25,000 USD"){
                     ?>
                     <p class="alert alert-info">BETWEEN $10,000 AND $25,000 USD</p>
                    <?php 
                      }else if($solicitud['op_preg14'] != "HASTA $3,000 USD" && $solicitud['op_preg14'] != "ENTRE $3,000 Y $10,000 USD" && $solicitud['op_preg14'] != "ENTRE $10,000 A $25,000 USD"){
                     ?>
                     <p class="alert alert-info"><?php echo $solicitud['op_preg14']; ?></p>
                     
                    <?php 
                      }
                     ?>
                  </div>

                <?php
                  }else if($solicitud['preg13'] == 'NO'){
                ?>
                  <div class="col-xs-12">
                    <p class='text-center alert alert-danger'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span> NO</p>
                  </div>
                
                <?php         
                  }
                ?>
          </div>

          <label for="preg15">
            16. ESTIMATED DATE FOR BEGINNING TO USE THE SMALL PRODUCERS’ SYMBOL:
          </label>
          <input type="text" class="form-control" id="preg15" name="preg15" value="<?php echo $solicitud['preg15']; ?>">



      <div class="col-md-12 text-center alert alert-success" style="padding:7px;">INFORMATION ON PRODUCTS FOR WHICH APPLICAT WISHES TO USE SYMBOL<sup>6</sup></div>
      <div class="col-lg-12">
        <table class="table table-bordered" id="tablaProductos">
          <tr>
            <td>Product</td>
            <td>Total Estimated Volume to be Sold</td>
            <td>Volume of Finished Product</td>
            <td>Volume of Raw Material</td>
            <td>Country/Countries of Origin</td>
            <td>Country/Countries of Destination</td>          
          </tr>
          <?php 
          $query_producto_detalle = "SELECT * FROM productos WHERE idsolicitud_registro = $idsolicitud_registro";
          $producto_detalle = mysql_query($query_producto_detalle, $dspp) or die(mysql_error());
          $contador = 0;
          while($row_producto = mysql_fetch_assoc($producto_detalle)){
          ?>
            <tr>
              <td>
                <input type="text" class="form-control" name="producto[]" id="exampleInputEmail1" placeholder="Product" value="<?echo $row_producto['producto']?>">
              </td>
              <td>
                <input type="text" class="form-control" name="volumen_estimado[]" id="exampleInputEmail1" placeholder="Estimated Volume" value="<?echo $row_producto['volumen_estimado']?>">
              </td>
        
              <td>
                <input type="text" class="form-control" name="volumen_terminado[]" id="exampleInputEmail1" placeholder="Volumen Finished" value="<?echo $row_producto['volumen_terminado']?>">
              </td>
              <td>
                <input type="text" class="form-control" name="volumen_materia[]" id="exampleInputEmail1" placeholder="Volumen of Material" value="<?echo $row_producto['volumen_materia']?>">
              </td>
              <td>
                <input type="text" class="form-control" name="origen[]" id="exampleInputEmail1" placeholder="Origin" value="<?echo $row_producto['origen']?>">
              </td>
              <td>
                <input type="text" class="form-control" name="destino[]" id="exampleInputEmail1" placeholder="Destination" value="<?echo $row_producto['destino']?>">
              </td>

                <input type="hidden" name="idproducto[]" value="<?echo $row_producto['idproducto']?>">                     
            </tr>
          <?php 
          $contador++;
          }
          ?>        
          <tr>
            <td colspan="6">
              <h6><sup>6</sup> Information provided in this section will be handled with complete confidentiality. Please insert additional lines necessary.</h6>
            </td>
          </tr>
        </table>
      </div>


      <div class="col-lg-12 text-center alert alert-success" style="padding:7px;">
        <b>COMMITMENTS</b>
      </div>
      <div class="col-lg-12 text-justify">
        <p>1. By signing and sending in this document, the applicant expresses its interest in receiving a proposal for Registration with the Small Producers’ Symbol.</p>
        <p>2. The registration process will begin when it is confirmed that the payment corresponding to the proposal has been received. </p>
        <p>3. The fact that this application is delivered and received does not guarantee that the results of the registration process will be positive. </p>
        <p>4. The applicant will become familiar with and comply with all the applicable requirements in the General Standard of the Small Producers’ Symbol for Buyers, Collective Trading Companies owned by Small Producers’ Organizations, Intermediaries and Maquila Companies, including both Critical and Minimum Criteria, and independently of the type of evaluation conducted.  </p>
      </div>
      <div class="col-lg-12">

        <p style="font-size:14px;"><strong>Name of the person who is responsible for the accuracy of the information on this form, and who, on behalf of the Applicant, will follow up on the application:</strong></p>

        <input type="hidden" name="idempresa" value="<?php echo $solicitud['idempresa']; ?>">
        <input type="hidden" name="fecha_registro" value="<?php echo $solicitud['fecha_registro']; ?>">
        <input type="text" class="form-control" id="responsable" value="<?php echo $solicitud['responsable']; ?>" > 

        <p>
          <b>Certification Entity who receives the application:</b>
        </p>
        <p class="alert alert-info" style="padding:7px;">
          <?php echo $solicitud['abreviacionOC']; ?>
        </p>  
      </div>


    </fieldset>
  </form>
</div>


<script>
  
  function validar(){

    tipo_solicitud = document.getElementsByName("tipo_solicitud");
     
    var seleccionado = false;
    for(var i=0; i<tipo_solicitud.length; i++) {    
      if(tipo_solicitud[i].checked) {
        seleccionado = true;
        break;
      }
    }
     
    if(!seleccionado) {
      alert("You must select a type of Application");
      return false;
    }

    return true
  }

</script>

<script>
var contador=0;
  function tablaCertificaciones()
  {

  var table = document.getElementById("tablaCertificaciones");
    {
    var row = table.insertRow(2);
    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
    var cell3 = row.insertCell(2);
    var cell4 = row.insertCell(3);

    cell1.innerHTML = '<input type="text" class="form-control" name="certificacion['+contador+']" id="exampleInputEmail1" placeholder="CERTIFICACIÓN">';
    cell2.innerHTML = '<input type="text" class="form-control" name="certificadora['+contador+']" id="exampleInputEmail1" placeholder="CERTIFICADORA">';
    cell3.innerHTML = '<input type="text" class="form-control" name="ano_inicial['+contador+']" id="exampleInputEmail1" placeholder="AÑO INICIAL">';
    cell4.innerHTML = '<div class="col-xs-6">SI<input type="radio" class="form-control" name="interrumpida['+contador+']" value="SI"></div><div class="col-xs-6">NO<input type="radio" class="form-control" name="interrumpida['+contador+']" value="NO"></div>';
    }
    contador++;
  } 

  function tablaEmpresas()
  {
    contador++;
  var table = document.getElementById("tablaEmpresas");
    {
    var row = table.insertRow(1);
    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);


    cell1.innerHTML = '<input type="text" class="form-control" name="subempresa['+contador+']" id="exampleInputEmail1" placeholder="EMPRESA">';
    cell2.innerHTML = '<input type="text" class="form-control" name="servicio['+contador+']" id="exampleInputEmail1" placeholder="SERVICIO">';

    }
  } 
  function mostrar_empresas(){
    document.getElementById('contenedor_tablaEmpresas').style.display = 'block';
  }
  function ocultar_empresas()
  {
    document.getElementById('contenedor_tablaEmpresas').style.display = 'none';
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


    var row = table.insertRow(1);
    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
    var cell3 = row.insertCell(2);
    var cell4 = row.insertCell(3);
    var cell5 = row.insertCell(4);
    var cell6 = row.insertCell(5);   
    
    cell1.innerHTML = '<input type="text" class="form-control" name="producto['+cont+']" id="exampleInputEmail1" placeholder="Producto">';
    
    cell2.innerHTML = '<input type="text" class="form-control" name="volumen_estimado['+cont+']" id="exampleInputEmail1" placeholder="Volumen Estimado">';
    
    cell3.innerHTML = '<input type="text" class="form-control" name="volumen_terminado['+cont+']" id="exampleInputEmail1" placeholder="Volumen Terminado">';
    
    cell4.innerHTML = '<input type="text" class="form-control" name="volumen_materia['+cont+']" id="exampleInputEmail1" placeholder="Volumen Materia">';
    
    cell5.innerHTML = '<input type="text" class="form-control" name="origen['+cont+']" id="exampleInputEmail1" placeholder="Origen">';
    
    cell6.innerHTML = '<input type="text" class="form-control" name="destino['+cont+']" id="exampleInputEmail1" placeholder="Destino">';
     
  cont++;
    }

  } 

</script>