<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php'); 

?>

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
  mysql_select_db($database_dspp, $dspp);
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}


/****************************** INICIA FORMULARIO INSERTAR OPP **************************************************/
if ((isset($_POST["insertar"])) && ($_POST["insertar"] == "1")) {


  $pais = $_POST['pais'];

  $query = "SELECT idempresa, spp, pais FROM empresa WHERE pais = '$pais'";
  $ejecutar_spp = mysql_query($query) or die(mysql_error());
  //$datos_empresa = mysql_fetch_assoc($ejecutar);
  //$fecha = $_POST['fecha_inclusion'];

  setlocale(LC_ALL, 'en_US.UTF8');

  if(!empty($_POST['spp'])){
    $spp = $_POST['spp'];
  }else{
    $charset='utf-8'; // o 'UTF-8'
    $str = iconv($charset, 'ASCII//TRANSLIT', $pais);
    $pais = preg_replace("/[^a-zA-Z0-9]/", '', $str);

    $paisDigitos = strtoupper(substr($pais, 0, 3));
    $formatoFecha = date("d/m/Y", $fecha);
    $fechaDigitos = substr($formatoFecha, -2);
    $contador = 1;
    $contador = str_pad($contador, 3, "0", STR_PAD_LEFT);
    //$numero =  strlen($contador);

    $spp = "COM-".$paisDigitos."-".$fechaDigitos."-".$contador;

    while ($datos_empresa = mysql_fetch_assoc($ejecutar_spp)) {
      if($datos_empresa['spp'] == $spp){
        //echo "<b style='color:red'>es igual el OPP con id: $datos_empresa[idf]</b><br>";
        $contador++;
        $contador = str_pad($contador, 3, "0", STR_PAD_LEFT);
        $spp = "COM-".$paisDigitos."-".$fechaDigitos."-".$contador;
      }/*else{
        echo "el id encontrado es: $datos_empresa[idf]<br>";
      }*/
      
    }
  }
  //echo "se ha creado un nuevo idf de opp el cual es: <b>$idfcom</b>";

  $logitud = 8;
  if(empty($_POST['password'])){
    $psswd = substr( md5(microtime()), 1, $logitud);
  }else{
    $psswd = $_POST['password'];
  }


  if(isset($_POST['maquilador'])){
    $maquilador = $_POST['maquilador'];
  }else{
    $maquilador = '';
  }
  if(isset($_POST['comprador'])){
    $comprador = $_POST['comprador'];
  }else{
    $comprador = '';
  }
  if(isset($_POST['intermediario'])){
    $intermediario = $_POST['intermediario'];
  }else{
    $intermediario = '';
  }




  $insertSQL = sprintf("INSERT INTO empresa (idoc, spp, maquilador, comprador, intermediario, nombre, abreviacion, password, sitio_web, email, telefono, pais, ciudad, razon_social, direccion_oficina, direccion_fiscal, rfc, ruc, fecha_registro) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
     GetSQLValueString($_POST['idoc'], "int"),
     GetSQLValueString($spp, "text"),
     GetSQLValueString($maquilador, "int"),
     GetSQLValueString($comprador, "int"),
     GetSQLValueString($intermediario, "int"),
     GetSQLValueString($_POST['nombre'], "text"),
     GetSQLValueString($_POST['abreviacion'], "text"),
     GetSQLValueString($psswd, "text"),
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
     GetSQLValueString($_POST['fecha_registro'], "int"));

  $Result1 = mysql_query($insertSQL, $dspp) or die(mysql_error());

  /*$destinatario = $_POST['email'];
        //$asunto = "D-SPP Datos de Usuario / User Data"; 

  $cuerpo = '
      <html>
      <head>
        <meta charset="utf-8">
      </head>
      <body>
      
        <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
          <tbody>
                <tr>
                  <th rowspan="7" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
                  <th scope="col" align="left" width="280"><strong style="color:#27ae60;">Nuevo Registro / New Register</strong></th>
                </tr>
                <tr>
                  <td style="text-align:justify;padding-top:10px;"><i>Felicidades, se han registrado sus datos correctamente. A continuación se muestra su <b>#SPP y su contraseña, necesarios para poder inicia sesión</b>: <a href="http://d-spp.org/?OPP" target="_new">www.d-spp.org/?OPP</a></i>, una vez que haya iniciado sesión se le recomienda cambiar su contraseña en la sección Información OPP, en dicha sección se encuentran sus datos los cuales pueden ser modificados en caso de ser necesario.</td>
                </tr>
                <tr>
                  <td style="text-align:justify;padding-top:10px;"><i>Congratulations , your data have been recorded correctly. Below is your <b>#SPP and password needed to log in </b>: <a href="http://d-spp.org/?OPP" target="_new">www.d-spp.org/?OPP</a></i>, once you have logged you are advised to change your password on the Information OPP section, in that section are data which can be modified if be necessary.</td>
                </tr>
            <tr>
              <td align="left"><br><b>Nombre / Name:</b> <span style="color:#27ae60;">'.$_POST['nombre'].'</span></td>
            </tr>
            <tr>
              <td align="left"><br><b>#SPP:</b> <span style="color:#27ae60;">'.$spp.'</span></td>
            </tr>
            <tr>
              <td align="left"><b>Contraseña / Password:</b> <span style="color:#27ae60;">'.$psswd.'</span></td>
            </tr>
            <tr>
              <td>Cualquier duda escribir a / Any questions write to : <u style="color:#27ae60;">cert@spp.coop</u></td>
            </tr>
          </tbody>
        </table>


      </body>
      </html>
    ';

      $mail->AddAddress($destinatario);
      $mail->AddBCC('yasser.midnight@gmail.com');
      $mail->AddBCC('cert@spp.coop');
      //$mail->Username = "soporte@d-spp.org";
      //$mail->Password = "/aung5l6tZ";
      $mail->Subject = utf8_decode($asunto_usuario);
      $mail->Body = utf8_decode($cuerpo);
      $mail->MsgHTML(utf8_decode($cuerpo));
      $mail->Send();
      $mail->ClearAddresses();
      $mensaje = "<strong>Datos Registrados Correctamente, por favor revisa tu bandeja de correo electronico, si no encuentras tus datos revisa tu bandeja de spam</strong>";*/

}
/****************************** FIN FORMULARIO INSERTAR OPP **************************************************/

$query_empresa = mysql_query("SELECT * FROM empresa", $dspp) or die(mysql_error());

?>
<form action="" method="POST">
  <table class="table table-bordered table-striped table-condensed" style="font-size:11px">

      <tr>
        <th>#SPP</th>
        <th><input type="text" class="form-control" name="spp"></th>
      </tr>
      <tr>
        <th>idoc</th>
        <th><input type="text" class="form-control" name="idoc"></th>
      </tr>
      <tr>
        <th>nombre</th>
        <th><input type="text" class="form-control" name="nombre" required></th>
      </tr>
      <tr>
        <th>abreviacion</th>
        <th><input type="text" class="form-control" name="abreviacion"></th>
      </tr>
      <tr>
        <th colspan="2">
                               <div class="form-group">
                        <p class="col-sm-2 text-right"><strong>TIPO DE EMPRESA</strong></p>
                        <div class="col-sm-10">
                          <div class="checkbox">
                            <label class="col-sm-4">
                              <input type="checkbox"name="maquilador" value="1"> MAQUILADOR
                            </label>
                            <label class="col-sm-4">
                              <input type="checkbox"name="comprador" value="1"> COMPRADOR
                            </label>
                            <label class="col-sm-4">
                              <input type="checkbox"name="intermediario" value="1"> INTERMEDIARIO
                            </label>


                          </div>
                        </div>
                      </div>

        </th>
      </tr>



      <tr>
        <th>password</th>
        <th><input type="text" class="form-control" name="password"></th>
      </tr>
      <tr>
        <th>sitio_web</th>
        <th><input type="text" class="form-control" name="sitio_web"></th>
      </tr>
      <tr>
        <th>email</th>
        <th><input type="text" class="form-control" name="email"></th>
      </tr>
      <tr>
        <th>telefono</th>
        <th><input type="text" class="form-control" name="telefono"></th>
      </tr>
      <tr>
        <th>pais</th>
        <th>
          <select name="pais" id="">
            <option value="">pais</option>
            <?php 
            $row_pais = mysql_query("SELECT * FROM paises", $dspp) or die(mysql_error());
            while($pais = mysql_fetch_assoc($row_pais)){
              echo "<option value='".utf8_encode($pais['nombre'])."'>".utf8_encode($pais['nombre'])."</option>";
            }
             ?>
          </select>
        </th>
      </tr>
      <tr>
        <th>ciudad</th>
        <th><input type="text" class="form-control" name="ciudad"></th>
      </tr>
      <tr>
        <th>razon_social</th>
        <th><input type="text" class="form-control" name="razon_social"></th>
      </tr>
      <tr>
        <td>direccion_oficina</td>
        <td><input type="text" class="form-control" name="direccion_oficina"></td>
      </tr>
      <tr>
        <td>direccion_fiscal</td>
        <td><input type="text" class="form-control" name="direccion_fiscal"></td>
      </tr>
      <tr>
        <td>rfc</td>
        <td><input type="text" class="form-control" name="rfc"></td>
      </tr>
      <tr>
        <td>ruc</td>
        <td><input type="text" class="form-control" name="ruc"></td>
      </tr>
      <tr>
        <td>estatus_opp</td>
        <td><input type="text" class="form-control" name="estatus_opp"></td>
      </tr>
      <tr>
        <td>estatus_interno</td>
        <td><input type="text" class="form-control" name="estatus_interno"></td>
      </tr>
      <tr>
        <td>estatus_dspp</td>
        <td><input type="text" class="form-control" name="estatus_dspp"></td>
      </tr>
      <tr>
        <td>fecha_registr</td>
        <td><input type="text" class="form-control" name="fecha_registro"></td>
      </tr>
      <tr>
        <td><button type="submit" class="btn btn-success" name="insertar" value="1">Insertar registro</button></td>
      </tr>

  </table>  
</form>

<table class="table table-bordered table-condensed" style="font-size:11px">
  <thead>
    <tr>
      <th>Nº</th>
      <th>#SPP</th>
      <th>idopp</th>
      <th>idoc</th>
      <th>nombre</th>
      <th>abreviación</th>
      <th>password</th>
      <th>Web</th>
      <th>Email</th>
      <th>Telefono</th>
      <th>Pais</th>
      <th>Ciudad</th>
      <th>razon_social</th>
      <th>direccion_oficina</th>
      <th>direccion_fiscal</th>
      <th>rfc</th>
      <th>ruc</th>
      <th>estatus_opp</th>
      <th>estatus_interno</th>
      <th>estatus_dspp</th>
      <th>fecha_registro</th>
    </tr>
  </thead>
  <tbody>
    <?php 
    $contador = 1;
    while($opp = mysql_fetch_array($query_empresa)){
    ?>
      <tr>
        <td><?php echo $contador; ?></td>
        <td><?php echo $opp['spp']; ?></td>
        <td><?php echo $opp['idempresa']; ?></td>
        <td><?php echo $opp['idoc']; ?></td>
        <td><?php echo $opp['nombre']; ?></td>
        <td><?php echo $opp['abreviacion']; ?></td>
        <td><?php echo $opp['password']; ?></td>
        <td><?php echo $opp['sitio_web']; ?></td>
        <td><?php echo $opp['email']; ?></td>
        <td><?php echo $opp['telefono']; ?></td>
        <td><?php echo $opp['pais']; ?></td>
        <td><?php echo $opp['ciudad']; ?></td>
        <td><?php echo $opp['razon_social']; ?></td>
        <td><?php echo $opp['direccion_oficina']; ?></td>
        <td><?php echo $opp['direccion_fiscal']; ?></td>
        <td><?php echo $opp['rfc']; ?></td>
        <td><?php echo $opp['ruc']; ?></td>
        <td><?php echo $opp['estatus_opp']; ?></td>
        <td><?php echo $opp['estatus_interno']; ?></td>
        <td><?php echo $opp['estatus_dspp']; ?></td>
        <td><?php echo $opp['fecha_registro']; ?></td>
      </tr>
    <?php
    $contador++;
    }
     ?>
  </tbody>
</table>

