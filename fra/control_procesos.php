<?php require_once('Connections/dspp.php');
mysql_select_db($database_dspp, $dspp);

 ?>


<!DOCTYPE html>
<html lang="es">
  <head>

    
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <!-- Enjoy Hint -->

  <!--<link href="enjoyhint/enjoyhint.css" rel="stylesheet">
  <script src="enjoyhint/enjoyhint.min.js"></script>
    <!-- Enjoy Hint -->



    <title>D-SPP.ORG</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap-theme.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

    <!--<link href="css/fileinput.css" media="all" rel="stylesheet" type="text/css" /> 
    <script src="js/fileinput.min.js" type="text/javascript"/>-->

    <!-- Custom styles for this template -->
    <!-- <link href="login.css" rel="stylesheet"> -->

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

 
  </head>



<?php 
  if(isset($_POST['control_procesos']) && $_POST['control_procesos'] == 10){

    /***************** DATOS OPP  **********************/
    $identificacion = $_POST['identificacion'];
    $nombre = $_POST['nombre'];
    $abreviacion = $_POST['abreviacion'];
    $sitio_web = $_POST['sitio_web'];
    $emailOPP = $_POST['emailOPP'];
    $telefonoOpp = $_POST['telefonoOpp'];
    $paisOpp = $_POST['paisOpp'];
    $direccion = $_POST['direccion'];
    $direccion_fiscal = $_POST['direccion_fiscal'];
    /***************** DATOS OPP  **********************/


    /***************** DATOS CONTACTO  **********************/    
    /*$personaContacto = $_POST['personaContacto'];
    $cargoContacto = $_POST['cargoContacto'];
    $emailContacto = $_POST['emailContacto'];
    $telefonoContacto = $_POST['telefonoContacto'];
    /***************** DATOS CONTACTO  **********************/

    $consultar = "SELECT * FROM opp WHERE idf LIKE '%$identificacion%' OR nombre LIKE '%$nombre%'";
    $ejecutar = mysql_query($consultar,$dspp) or die(mysql_error());
    $filas = mysql_num_rows($ejecutar);


    if(empty($filas)){
      $query = "INSERT INTO opp(idf, nombre, abreviacion, sitio_web, email, telefono, pais, direccion ,direccion_fiscal) VALUES ('$identificacion', '$nombre', '$abreviacion', '$sitio_web', '$emailOPP', '$telefonoOpp', '$paisOpp', '$direccion', '$direccion_fiscal')";    
      $ejecutar = mysql_query($query,$dspp) or die(mysql_error());

      $idopp = mysql_insert_id($dspp); 


      $personaContacto = $_POST['personaContacto'];
      $cargoContacto = $_POST['cargoContacto'];
      $emailContacto1 = $_POST['emailContacto1'];
      $telefonoContacto1 = $_POST['telefonoContacto1'];
      $telefonoContacto2 = $_POST['telefonoContacto2'];

      for($i=0;$i<count($personaContacto);$i++){
        if($personaContacto[$i] != NULL){
          #for($i=0;$i<count($certificacion);$i++){
          $query = "INSERT INTO contacto (idopp, contacto, cargo, telefono1, telefono2, email1) VALUES ('$idopp' ,'$personaContacto[$i]', '$cargoContacto[$i]', '$telefonoContacto1[$i]', '$telefonoContacto2[$i]', '$emailContacto1[$i]')";
          $ejecutar = mysql_query($query,$dspp) or die(mysql_error());

        }
      }


      /***************** DATOS PRODUCTO  **********************/
      $producto = $_POST['producto'];
      $pais = $_POST['pais'];
      /***************** DATOS PRODUCTO  **********************/

      for($i=0;$i<count($producto);$i++){
        if($producto[$i] != NULL){
          #for($i=0;$i<count($certificacion);$i++){
          $query = "INSERT INTO productos (idopp, producto, destino) VALUES ($idopp, '$producto[$i]', '$pais[$i]')";
          $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
        }
      }    




      /***************** DATOS ULTIMAS ACCIONES  **********************/
      $accion = $_POST['accion'];
      $observacion = $_POST['observacion'];
      /***************** DATOS ULTIMAS ACCIONES  **********************/
      if(!empty($accion) || !empty($observacion)){
        $query = "INSERT INTO ultima_accion (idopp, ultima_accion, observacion) VALUES($idopp, '$accion', '$observacion')";
        $ejecutar = mysql_query($query,$dspp);
      }


      echo "<script>
      alert('".$idopp."');
      </script>";
    }else{
      $coincidencia = mysql_fetch_assoc($ejecutar);
      $idopp = $coincidencia['idopp'];

      echo "<script>
      alert('Se enconto coincidencia ".$idopp."');
      </script>";


      $actualizar = "UPDATE opp SET idf='$identificacion', nombre='$nombre', abreviacion='$abreviacion', sitio_web='$sitio_web', email='$emailOPP', telefono='$telefonoOpp', pais='$paisOpp', direccion_fiscal='$direccion' WHERE idopp = $idopp";
      $resultado = mysql_query($actualizar,$dspp) or die(mysql_error());


      $personaContacto = $_POST['personaContacto'];
      $cargoContacto = $_POST['cargoContacto'];
      $emailContacto1 = $_POST['emailContacto1'];
      $telefonoContacto1 = $_POST['telefonoContacto1'];
      $telefonoContacto2 = $_POST['telefonoContacto2'];

      for($i=0;$i<count($personaContacto);$i++){
        if($personaContacto[$i] != NULL){
          #for($i=0;$i<count($certificacion);$i++){
          $query = "INSERT INTO contacto (idopp, contacto, cargo, telefono1, telefono2, email1) VALUES ('$idopp' ,'$personaContacto[$i]', '$cargoContacto[$i]', '$telefonoContacto1[$i]', '$telefonoContacto2[$i]', '$emailContacto1[$i]')";
          $ejecutar = mysql_query($query,$dspp) or die(mysql_error());

        }
      }


      /***************** DATOS PRODUCTO  **********************/
      $producto = $_POST['producto'];
      $pais = $_POST['pais'];
      /***************** DATOS PRODUCTO  **********************/

      for($i=0;$i<count($producto);$i++){
        if($producto[$i] != NULL){
          #for($i=0;$i<count($certificacion);$i++){
          $query = "INSERT INTO productos (idopp, producto, destino) VALUES ($idopp, '$producto[$i]', '$pais[$i]')";
          $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
        }
      }    


      /***************** DATOS ULTIMAS ACCIONES  **********************/
      $accion = $_POST['accion'];
      $observacion = $_POST['observacion'];
      /***************** DATOS ULTIMAS ACCIONES  **********************/

      if(!empty($accion) || !empty($observacion)){
        $query = "INSERT INTO ultima_accion (idopp, ultima_accion, observacion) VALUES($idopp, '$accion', '$observacion')";
        $ejecutar = mysql_query($query,$dspp);
      }

    }


  }

$query_pais = "SELECT nombre FROM paises ORDER BY nombre ASC";
$pais = mysql_query($query_pais, $dspp) or die(mysql_error());
$row_pais = mysql_fetch_assoc($pais);
$totalRows_pais = mysql_num_rows($pais);


                      $consultaFecha = "SELECT idfecha FROM fecha WHERE idexterno = 44 AND identificador = 'OPP' AND status = 20";
                      $ejecutar = mysql_query($consultaFecha,$dspp) or die(mysql_error());
                      $total = mysql_num_rows($ejecutar);

                      ECHO "EL TOTAL ES :".$total;
                      if(!empty($total)){
                        echo "esta madre debe de salir";
                      }
 ?>


<?php 
    $archivoDictamen = '';
    $archivoExtra = '';


    $anexoNombres = "";
    $query_anexos = "SELECT * FROM anexos WHERE idstatus_interno = 8";
    $row_anexos = mysql_query($query_anexos,$dspp) or die(mysql_error());

    while($datos_anexos = mysql_fetch_assoc($row_anexos)){
      $anexoNombres .= "<p><i class='fa fa-check-circle-o fa-2x'></i><a href=http://d-spp.org/".$datos_anexos['archivo']." target='_blank'>".$datos_anexos['anexo']."</a></p>";
    }

    if(isset($_POST['enviarCorreo']) && $_POST['enviarCorreo'] == 1){
      include_once("PHPMailer/class.phpmailer.php");
      include_once("PHPMailer/class.smtp.php");

    //AL NOMBRE DEL ARCHIVO(FORMATO) LE CONCATENO EL TIME
     /* $rutaArchivo = "formatos/anexos/";
      if(!empty($_FILES['archivoAdjunto']['name'])){
          $_FILES["archivoAdjunto"]["name"];
            move_uploaded_file($_FILES["archivoAdjunto"]["tmp_name"], $rutaArchivo.time()."_".$_FILES["archivoAdjunto"]["name"]);
            $archivoAdjunto = $rutaArchivo.basename(time()."_".$_FILES["archivoAdjunto"]["name"]);
      }else{
        $archivoAdjunto = NULL;
      }*/
    $anexoNombres = "";
    $query_anexos = "SELECT * FROM anexos WHERE idstatus_interno = 8";
    $row_anexos = mysql_query($query_anexos,$dspp) or die(mysql_error());




      $nombre = $_POST['nombre'];
      $apellido = $_POST['apellido'];
      $mensaje = $_POST['mensaje'];

      $mail = new PHPMailer();
      $mail->IsSMTP();
      $mail->SMTPAuth = true;
      $mail->SMTPSecure = "ssl";
      $mail->Host = "smtp.gmail.com";
      $mail->Port = 465;
      $mail->From = "soporteinforganic@gmail.com";
      $mail->AddAddress("yasser.midnight@gmail.com");
      $mail->Username = "yasser.midnight@gmail.com";
      $mail->Password = "yasser@midnight";
      $mail->Subject = $mensaje;
      $mail->Body = "este es el cuerpo del mensaje";
      $mail->MsgHTML($mensaje);

    while($datos_anexos = mysql_fetch_assoc($row_anexos)){
      $mail->AddAttachment($datos_anexos['archivo']);
    }
      

      if($mail->Send()){
        
        echo "<script>alert('Formulario enviado exitosamente, le responderemos lo más pronto posible.');location.href ='javascript:history.back()';</script>";
      }else{
            echo "<script>alert('Error al enviar el formulario');location.href ='javascript:history.back()';</script>";
   
      }



    }
 ?>

  <body>
  
<style>
  .dspp {
    background-color:#ffc477;
    -moz-border-radius:13px;
    -webkit-border-radius:13px;
    border-radius:13px;
    border:2px solid #eeb44f;
    display:inline-block;
    cursor:pointer;
    color:#ffffff;
    font-family:Arial;
    font-size:17px;
    padding:9px 21px;
    text-decoration:none;
    text-shadow:0px 1px 0px #cc9f52;
  }
  .dspp:hover {
    background-color:#fb9e25;
  }
  .dspp:active {
    position:relative;
    top:1px;
  }
</style>

<?php $IDFOC = " OC-GUA-11-001"; ?>

<a href="index.php?OPP&IDFOC=<?php echo trim($IDFOC);?>" target="_new" class="dspp">www.d-spp.org</a>

<script>
  function contrasenia(){
<?php 
$logitud = 8;
$psswd = substr( md5(microtime()), 1, $logitud);
//echo $psswd;


//echo "alert('$psswd');";
 ?>
alert('<? echo $psswd;?>');
    
  }  
</script>
             

         



              <button class="btn btn-success" id="cotizacion" name="cotizacion" type="button" value="Enviar" aria-label="Left Align" onclick="contrasenia()">
                <span class="glyphicon glyphicon-open-file" aria-hidden="true"></span> Enviar Cotización
              </button>



<hr>


  <div class="container">
    <div class="row">
<div class="col-xs-12">


</div>
  
  <div class="col-xs-12">
    <form action="" method="post" id="statusCertificado" enctype="multipart/form-data">
      <input type="text" name="nombre" placeholder="nombre">
      <input type="text" name="apellido" placeholder="apellido">
      <input type="text" name="mensaje" placeholder="mensaje">
      <!--<input type="file" name="archivoAdjunto">-->
      <input type="submit" value="enviar">
      <input type="text" name="enviarCorreo" value="1">
    </form>
  </div>
  <div class="col-xs-12">
                <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
                  <tbody>
                    <tr>
                      <th rowspan="2" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                      <th scope="col" align="left" width="280"><p>Asunto: <span style="color:red">Contrato de uso del Simbolo de Pequeños Productores - SPP</span></p></th>

                    </tr>
                    <tr>
                     <th scope="col" align="left" width="280"><p>Para: <span style="color:red">OPP de pRUEBA</span></p></th>
                    </tr>

                    <tr >
                      <td colspan="2">
                        <p>Reciban ustedes un cordial y atento saludo, así como el deseo de éxito en todas y cada una de sus actividades</p>
                        <p>La presente tiene por objetivo hacerles llegar el documentro <strong>Contrato de Uso del Simbolo de Pequeños Productores y Acuse de Recibido</strong>; documentos que se requieren sean leidos y entendidos, una vez revisada la información de los documentos mencionados, por favor proceder a firmarlos y envíar los mismos por este medio.</p>
                        <p>El Contrato de Uso menciona como anexo el documento Manual del SPP y este Manual a su vez menciona como anexos los siguientes documentos.</p>
                      </td>
                    </tr>
                    <tr>
                      <td><p><strong>Documentos Anexos</strong></p></td>
                    </tr>
                    <tr>
                      <td>
                        <?php echo $anexoNombres; ?>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <p><strong>Memresía SPP</strong></p>
                        <p>Asi mismo se anexan los datos bancarios para el respectivo pago de la membresía SPP</p>
                        <p>El monto total de la membresía SPP es de: </p>
                      </td>

                    </tr>
                  </tbody>
                </table>

  </div>

      <div class="col-xs-12">
        
        <div class="col-xs-12">
          <h3 class="text-center">FORMULARIO CONTROL PROCESOS</h3>
        </div>
     
        <form action="" method="POST" target="" accept-charset="UTF-8" enctype="application/x-www-form-urlencoded">

          <div class="col-xs-12">
            <h5 class="alert alert-danger">DATOS OPP</h5>
          </div>
          <div class="col-xs-12">
            <p>IDENTIFICACIÓN FUNDEPPO</p>
            <input type="text" class="form-control col-xs-6" name="identificacion">
          </div>
          <div class="col-xs-12">
            <p>NOMBRE DE LA ORGANIZACIÓN</p>
            <input type="text" class="form-control col-xs-6" name="nombre">
          </div>
          <div class="col-xs-12">
            <p>ABREVIACIÓN</p>
            <input type="text" class="form-control col-xs-6" name="abreviacion">
          </div>
          <div class="col-xs-12">
            <p>PAÍS</p>
            <select required class="form-control" name="paisOpp">
              <option value="">Selecciona</option>
              <?php 
                do {  
                ?>
                <option class="form-control" value="<?php echo utf8_encode($row_pais['nombre']);?>" ><?php echo utf8_encode($row_pais['nombre']);?></option>
                <?php
                } while ($row_pais = mysql_fetch_assoc($pais));
              ?>
            </select>



          </div>
          <div class="col-xs-12">
            <p>Nº DE SOCIOS -----*</p>
            <input type="text" class="form-control col-xs-6" name="" disabled>
          </div>
          <div class="col-xs-12">
            <p>DIRECCIÓN</p>
            <input type="text" class="form-control col-xs-6" name="direccion">
          </div>
          <div class="col-xs-12">
            <p>DIRECCIÓN FISCAL</p>
            <input type="text" class="form-control col-xs-6" name="direccion_fiscal">
          </div>
          <div class="col-xs-12">
            <p>CORRREO ELETRÓNICO</p>
            <input type="text" class="form-control col-xs-6" name="emailOPP">
          </div>
          <div class="col-xs-12">
            <p>SITIO WEB</p>
            <input type="text" class="form-control col-xs-6" name="sitio_web">
          </div>
          <div class="col-xs-12">
            <p>TELÉFONO OPP</p>
            <input type="text" class="form-control col-xs-6" name="telefonoOpp">
          </div>
          <hr>


          <div class="col-xs-12">
            <h5 class="alert alert-danger">DATOS CONTACTO</h5>
          </div>

          <div class="col-xs-12">
            <table class="table table-bordered" id="tablaContactos">
              <tr>
                <td>PERSONA DE CONTACTO</td>
                <td>CARGO</td>
                <td>CORREO ELECTRONICO</td>
                <td>TELEFONO 1</td>
                <td>TELEFONO 2</td>
                <td>
                  <button type="button" onclick="tablaContactos()" class="btn btn-primary" aria-label="Left Align">
                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                  </button>
                  
                </td> 

              </tr>
             <tr>
                <td><input type="text" class="form-control" name="personaContacto[0]"></td>
                <td><input type="text" class="form-control" name="cargoContacto[0]"></td>
                <td><input type="text" class="form-control" name="emailContacto1[0]"></td>
                <td><input type="text" class="form-control" name="telefonoContacto1[0]"></td>
                <td><input type="text" class="form-control" name="telefonoContacto2[0]"></td>
              </tr>

            </table>
          </div>


    <!--      <div class="col-xs-12">
            <p>PERSONAS DE CONTACTO</p>
            <input type="text" class="form-control col-xs-6" name="personaContacto">
          </div>
          <div class="col-xs-12">
            <p>CARGOS</p>
            <input type="text" class="form-control col-xs-6" name="cargosContacto">
          </div>
          <div class="col-xs-12">
            <p>CORREO ELECTRONICO</p>
            <input type="text" class="form-control col-xs-6" name="emailContacto">
          </div>
          <div class="col-xs-12">
            <p>TELEFONOS</p>
            <input type="text" class="form-control col-xs-6" name="telefonoContacto">
          </div>
-->


      <!--   <div class="col-xs-12">
            <h5 class="alert alert-danger">DATOS PRODUCTOS</h5>
          </div>
          <div class="col-xs-12">
            <p>PRODUCTOS</p>
            <input type="text" class="form-control col-xs-6" name="productos">
          </div>
          <div class="col-xs-12">
            <p>PAISES(DESTINO)</p>
            <input type="text" class="form-control col-xs-6" name="paises">
          </div>-->

     <div class="col-xs-12">
            <table class="table table-bordered" id="tablaProductos">
              <tr>
                <td>PRODUCTO</td>
                <td>PAIS (DESTINO)</td>

                <td>
                  <button type="button" onclick="tablaProductos()" class="btn btn-primary" aria-label="Left Align">
                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                  </button>
                  
                </td> 

              </tr>
             <tr>
                <td><input type="text" class="form-control" name="producto[0]"></td>
                <td><input type="text" class="form-control" name="pais[0]"></td>
              </tr>

            </table>
          </div>


          <div class="col-xs-12">
            <h5 class="alert alert-danger">DATOS ULTIMAS ACCIONES</h5>
          </div>
          <div class="col-xs-12">
            <p>ULTIMA ACCIÓN</p>
            <input type="text" class="form-control col-xs-6" name="accion" >
          </div>
          <div class="col-xs-12">
            <p>OBSERVACIONES</p>
            <input type="text" class="form-control col-xs-6" name="observacion" >
          </div>

          <br>
          <div class="col-xs-12" style="margin-top:20px">
            <input class="col-xs-4 btn btn-success" type="submit" value="enviar">   
            <input type="hidden" name="control_procesos" value="10">       
          </div>
        </form>

      </div>


    </div>
  </div> 




<?php 
  if(isset($_POST['agregarCertificado']) && $_POST['agregarCertificado'] == "certificado"){
    $estatus_certificado = $_POST['status_certificado'];
    $vigencia_fin = $_POST['vigencia_fin'];
    $entidad = $_POST['entidad'];
    $idopp = $_POST['idopp'];

    $query = "INSERT INTO certificado(status, vigenciafin, idopp, entidad) VALUES('$estatus_certificado', '$vigencia_fin', '$idopp', '$entidad')";
    $ejecutar = mysql_query($query, $dspp) or die(mysql_error());

    $actualizar = "UPDATE opp SET estado = '$estatus_certificado' WHERE idopp = $idopp";
    $ejecutar = mysql_query($actualizar, $dspp) or die(mysql_error());
  echo "<script>window.location='?idopp=$idopp'</script>"; 
  }


 ?>
<h4>CAMPO DE BUSQUEDA</h4>

<div class="col-xs-12">
  <form action="" name="actualizar" method="POST"  accept-charset="UTF-8" enctype="application/x-www-form-urlencoded">
    <input class="form-control" type="text" name="buscar" placeholder="ingrese la busqueda">
    
    <input type="submit" name="enviar">
    <input type="hidden" name="formularioBusqueda" value="1">
  </form>  
</div>

<div class="col-xs-12">
<?php 
  if(isset($_POST['formularioBusqueda']) && $_POST['formularioBusqueda'] == 1){
    $busqueda = $_POST['buscar'];

    $queryBusqueda = "SELECT * FROM opp WHERE (idf LIKE '%$busqueda%') OR (nombre LIKE '%$busqueda%') OR (abreviacion LIKE '%$busqueda%') OR (pais LIKE '%$busqueda%')";
    $ejecutarOPP = mysql_query($queryBusqueda,$dspp) or die(mysql_error());

  }else if(isset($_GET['idopp'])){
    $busqueda = $_GET['idopp'];
    $queryBusqueda = "SELECT opp.*, certificado.* FROM opp INNER JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.idopp = $busqueda";
    //$queryBusqueda = "SELECT * FROM opp WHERE idopp = $busqueda";
    $ejecutarOPP = mysql_query($queryBusqueda,$dspp) or die(mysql_error());    
  }

 ?>
  <form action="" name="ingresar" method="POST"  accept-charset="UTF-8" enctype="application/x-www-form-urlencoded">
      <table class="table table-bordered">
        <tr>
          <td>idopp</td>
          <td>idf</td>
          <td>nombre</td>
          <td>abreviacion</td>
          <td>pais</td>
          <td>estado certificacion</td>
          <td>Status certificado</td>
          <td>vigencia Fin</td>
          <td>entidad</td>
          <td>ID OPP</td>
        </tr>
        <?php 
          while ($resultado = mysql_fetch_assoc($ejecutarOPP)) {?>
          <tr>
            <td><input type="text" class="form-control" value="<?php echo $resultado['idopp']; ?>"></td>
            <td><input type="text" class="form-control" value="<?php echo $resultado['idf']; ?>"></td>
            <td><textarea type="text" class="form-control"><?php echo $resultado['nombre']; ?></textarea></td>
            <td><input type="text" class="form-control" value="<?php echo $resultado['abreviacion']; ?>"></td>
            <td><input type="text" class="form-control" value="<?php echo $resultado['pais']; ?>"></td>
            <td><input type="text" class="form-control" value="<?php echo $resultado['estado']; ?>" readonly></td>
            <td>
              <select name="status_certificado" id="" class="form-control">
                <option value="Certificado Expirado">Certificado Expirado</option>
                <option value="Certificada">Certificada</option>
              </select>
              <input type="text" class="form-control" value="<?php echo $resultado['status'] ?>" readonly>
            </td>
            <td>
              <input type="date" class="form-control" name="vigencia_fin" value="">
              <input type="date" class="form-control" value="<?php echo $resultado['vigenciafin'] ?>" readonly>
            </td>
            <td>
  
              <select name="entidad" id="" class="form-control">
                <?php 
                  $oc = "SELECT * FROM oc";
                  $ejecutar = mysql_query($oc,$dspp) or die(mysql_error());

                  while($registroOC = mysql_fetch_assoc($ejecutar)){
                    echo "<option value='$registroOC[idoc]'>$registroOC[abreviacion]</option>";
                  }

                 ?>
                 <option value="99">FUNDEPPO</option>
              </select>
              <input type="text" class="form-control" value="<?php echo $resultado['entidad']; ?>" readonly>
            </td>
            <td><input type="text" class="form-control" name="idopp" value="<?php echo $resultado['idopp']; ?>"></td>

          </tr>


          <?php }
         ?>
         <tr>
           <td>
             <input type="hidden" name="agregarCertificado" value="certificado">
           </td>
           <td>
             <input type="submit" name="agregarCertificado2">
           </td>
         </tr>
         
         
      </table>    
  </form>
</div>







<script>
  
  var cont=0;
  function tablaContactos()
  {

  var table = document.getElementById("tablaContactos");
    {
    cont++;

    var row = table.insertRow(1);
    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
    var cell3 = row.insertCell(2);
    var cell4 = row.insertCell(3);
    var cell5 = row.insertCell(4);

    cell1.innerHTML = '<input type="text" class="form-control" name="personaContacto['+cont+']">';    
    cell2.innerHTML = '<input type="text" class="form-control" name="cargoContacto['+cont+']">';    
    cell3.innerHTML = '<input type="text" class="form-control" name="emailContacto1['+cont+']">';    
    cell4.innerHTML = '<input type="text" class="form-control" name="telefonoContacto1['+cont+']">';    
    cell5.innerHTML = '<input type="text" class="form-control" name="telefonoContacto2['+cont+']">';    

    }
  } 


  var cont2=0;
  function tablaProductos()
  {

  var table = document.getElementById("tablaProductos");
    {
    cont2++;

    var row = table.insertRow(1);
    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);

    cell1.innerHTML = '<input type="text" class="form-control" name="producto['+cont2+']">';    
    cell2.innerHTML = '<input type="text" class="form-control" name="pais['+cont2+']">';       

    }
  } 

</script>



  </body>
</html>