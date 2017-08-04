<?php
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php');

mysql_select_db($database_dspp, $dspp);

if (!isset($_SESSION)) {
  session_start();
  
  $redireccion = "../index.php?OC";

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
/*********** INICIAN VARIABLES GLOBALES ************/
$idsolicitud_colectiva = $_GET['idsolicitud_colectiva'];
$charset='utf-8';
$administrador = "yasser.midnight@gmail.com";
$spp_global = "cert@spp.coop";
$fecha = time();
/*********** TERMINAN VARIABLES GLOBALES ************/

if(isset($_POST['guardar_cambios']) && $_POST['guardar_cambios'] == "1"){


  if(isset($_POST['op_preg12'])){
    $op_preg12 = $_POST['op_preg12'];
  }else{
    $op_preg12 = "";
  }

  if(isset($_POST['op_preg13'])){
    $op_preg13 = $_POST['op_preg13'];
  }else{
    $op_preg13 = "";
  }


  /*if(!empty($_FILES['op_preg15']['name'])){
      $_FILES["op_preg15"]["name"];
        move_uploaded_file($_FILES["op_preg15"]["tmp_name"], $ruta_croquis.date("Ymd H:i:s")."_".$_FILES["op_preg15"]["name"]);
        $croquis = $ruta_croquis.basename(date("Ymd H:i:s")."_".$_FILES["op_preg15"]["name"]);
  }else{
    $croquis = NULL;
  }*/
  if(!empty($_POST['produccion'])){
    $produccion = $_POST['produccion'];
  }else{
    $produccion = 0;
  }
  if(!empty($_POST['procesamiento'])){
    $procesamiento = $_POST['procesamiento'];
  }else{
    $procesamiento = 0;
  }
  if(!empty($_POST['comercializacion'])){
    $comercializacion = $_POST['comercializacion'];
  }else{
    $comercializacion = 0;
  }

  if(!empty($_FILES['op_preg15']['name'])){
      $_FILES["op_preg15"]["name"];
        move_uploaded_file($_FILES["op_preg15"]["tmp_name"], $ruta_croquis.date("Ymd H:i:s")."_".$_FILES["op_preg15"]["name"]);
        $croquis = $ruta_croquis.basename(date("Ymd H:i:s")."_".$_FILES["op_preg15"]["name"]);
  }else{
    $croquis = NULL;
  }

  // ACTUALIZAMOS LA INFORMACION DE LA SOLICITUD
  $updateSQL = sprintf("UPDATE solicitud_colectiva SET contacto1_nombre = %s, contacto2_nombre = %s, contacto1_cargo = %s, contacto2_cargo = %s, contacto1_email = %s, contacto2_email = %s, contacto1_telefono = %s, contacto2_telefono = %s, adm1_nombre = %s, adm2_nombre = %s, adm1_email = %s, adm2_email = %s, adm1_telefono = %s, adm2_telefono = %s, total_miembros = %s, produccion = %s, procesamiento = %s, comercializacion = %s, preg2 = %s, preg3 = %s, preg4 = %s, preg5 = %s, preg6 = %s, preg8 = %s, preg10 = %s WHERE idsolicitud_colectiva = %s",
         GetSQLValueString($_POST['contacto1_nombre'], "text"),
         GetSQLValueString($_POST['contacto2_nombre'], "text"),
         GetSQLValueString($_POST['contacto1_cargo'], "text"),
         GetSQLValueString($_POST['contacto2_cargo'], "text"),
         GetSQLValueString($_POST['contacto1_email'], "text"),
         GetSQLValueString($_POST['contacto2_email'], "text"),
         GetSQLValueString($_POST['contacto1_telefono'], "text"),
         GetSQLValueString($_POST['contacto2_telefono'], "text"),
         GetSQLValueString($_POST['adm1_nombre'], "text"),
         GetSQLValueString($_POST['adm2_nombre'], "text"),
         GetSQLValueString($_POST['adm1_email'], "text"),
         GetSQLValueString($_POST['adm2_email'], "text"),
         GetSQLValueString($_POST['adm1_telefono'], "text"),
         GetSQLValueString($_POST['adm2_telefono'], "text"),
         GetSQLValueString($_POST['total_miembros'], "text"),

         GetSQLValueString($produccion, "int"),
         GetSQLValueString($procesamiento, "int"),
         GetSQLValueString($comercializacion, "int"),
         GetSQLValueString($_POST['preg2'], "text"),
         GetSQLValueString($_POST['preg3'], "text"),
         GetSQLValueString($_POST['preg4'], "text"),
         GetSQLValueString($_POST['preg5'], "text"),
         GetSQLValueString($_POST['preg6'], "text"),
         GetSQLValueString($_POST['preg8'], "text"),
         //GetSQLValueString($_POST['preg9'], "text"),
         GetSQLValueString($_POST['preg10'], "text"),
         //GetSQLValueString($_POST['preg11'], "text"),
         GetSQLValueString($idsolicitud_colectiva, "int"));
  $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());



  // ACTUALIZAMOS LA INFORMACION DE LA OPP
  $updateSQL = sprintf("UPDATE opp SET nombre = %s, pais = %s, direccion_oficina = %s, email = %s, telefono = %s, sitio_web = %s, razon_social = %s, direccion_fiscal = %s, rfc = %s, ruc = %s, produccion = %s, procesamiento = %s, comercializacion = %s WHERE idopp = %s",
    GetSQLValueString($_POST['nombre_facilitador'], "text"),
    GetSQLValueString($_POST['pais'], "text"),
    GetSQLValueString($_POST['direccion_oficina'], "text"),
    GetSQLValueString($_POST['email'], "text"),
    GetSQLValueString($_POST['telefono'], "text"),
    GetSQLValueString($_POST['sitio_web'], "text"),
    GetSQLValueString($_POST['razon_social'], "text"),
    GetSQLValueString($_POST['direccion_fiscal'], "text"),
    GetSQLValueString($_POST['rfc'], "text"),
    GetSQLValueString($_POST['ruc'], "text"),
    GetSQLValueString($produccion, "int"),
    GetSQLValueString($procesamiento, "int"),
    GetSQLValueString($comercializacion, "int"),
    GetSQLValueString($_POST['idopp'], "int"));
  $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

  //ACTUALIZAMOS EL NUMERO DE SOCIOS
  /*$updateSQL = sprintf("UPDATE num_socios SET numero = %s WHERE fecha_registro = %s AND idopp = %s",
    GetSQLValueString($_POST['resp1'], "int"),
    GetSQLValueString($_POST['fecha_registro'], "int"),
    GetSQLValueString($_POST['idopp'], "int"));
  $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());*/

  ////ACTUALIZAMOS LOS PORCENTAJES DE VENTAS
  if(!empty($_POST['organico']) || !empty($_POST['comercio_justo']) || !empty($_POST['spp']) || !empty($_POST['sin_certificado'])){
    $row_ventas = mysql_query("SELECT * FROM porcentaje_productoVentas WHERE idsolicitud_colectiva = $idsolicitud_colectiva", $dspp) or die(mysql_error());
    $existe_venta = mysql_num_rows($row_ventas);
    if($existe_venta){
      $updateSQL = sprintf("UPDATE porcentaje_productoVentas SET organico = %s, comercio_justo = %s, spp = %s, sin_certificado = %s WHERE idsolicitud_colectiva = %s",
        GetSQLValueString($_POST['organico'], "text"),
        GetSQLValueString($_POST['comercio_justo'], "text"),
        GetSQLValueString($_POST['spp'], "text"),
        GetSQLValueString($_POST['sin_certificado'], "text"),
        GetSQLValueString($idsolicitud_colectiva, "int"));
      $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());
    }else{
      $insertSQL = sprintf("INSERT INTO porcentaje_productoVentas (organico, comercio_justo, spp, sin_certificado, idsolicitud_colectiva, idopp) VALUES (%s, %s, %s, %s, %s, %s)",
        GetSQLValueString($_POST['organico'], "text"),
        GetSQLValueString($_POST['comercio_justo'], "text"),
        GetSQLValueString($_POST['spp'], "text"),
        GetSQLValueString($_POST['sin_certificado'], "text"),
        GetSQLValueString($idsolicitud_colectiva, "int"),
        GetSQLValueString($_POST['idopp'], "int"));
      $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
    }

  }
 

    /*************************** INICIA ACTUALIZAR SUB ORGANIZACIONES ***************************/

        // SE ACTUALIZAN LAS CERTIFICACIONES

    // SE ACTUALIZAN LOS PRODUCTOS
    if(isset($_POST['sub_nombre'])){
      $sub_nombre = $_POST['sub_nombre'];
    }else{
      $sub_nombre = '';
    }
    if(isset($_POST['sub_producto'])){
      $sub_producto = $_POST['sub_producto'];
    }else{
      $sub_producto = '';
    }
    if(isset($_POST['num_productores'])){
      $num_productores = $_POST['num_productores'];
    }else{
      $num_productores = '';
    }
    if(isset($_POST['unidad_produccion'])){
      $unidad_produccion = $_POST['unidad_produccion'];
    }else{
      $unidad_produccion = '';
    }
    if(isset($_POST['sub_incumplimientos'])){
      $sub_incumplimientos = $_POST['sub_incumplimientos'];
    }else{
      $sub_incumplimientos = '';
    }
    if(isset($_POST['sub_certificaciones'])){
      $sub_certificaciones = $_POST['sub_certificaciones'];
    }else{
      $sub_certificaciones = '';
    }
    if(isset($_POST['sub_certificadora'])){
      $sub_certificadora = $_POST['sub_certificadora'];
    }else{
      $sub_certificadora = '';
    }


    if(isset($_POST['sub_anio_certificacion'])){
      $sub_anio_certificacion = $_POST['sub_anio_certificacion'];
    }else{
      $sub_anio_certificacion = '';
    }
    if(isset($_POST['sub_interrumpido'])){
      $sub_interrumpido = $_POST['sub_interrumpido'];
    }else{
      $sub_interrumpido = '';
    }


    if(isset($_POST['idsub_organizacion'])){
      $idsub_organizacion = $_POST['idsub_organizacion'];
    }else{
      $idsub_organizacion = '';
    }

    if(isset($_POST['idsub_organizacion'])){
      for ($i=0;$i<count($sub_nombre);$i++) { 
        if($sub_nombre[$i] != NULL){

        $array9 = "sub_interrumpido".$i; 


        if(isset($_POST[$array9])){
          $sub_interrumpido = $_POST[$array9];
        }else{
          $sub_interrumpido = '';
        }


            //$str = iconv($charset, 'ASCII//TRANSLIT', $producto_actual[$i]);
            //$producto_actual[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

            /*$str = iconv($charset, 'ASCII//TRANSLIT', $destino_actual[$i]);
            $destino_actual[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

            $str = iconv($charset, 'ASCII//TRANSLIT', $materia_actual[$i]);
            $materia_actual[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));*/


        $updateSQL = sprintf("UPDATE sub_organizaciones SET nombre = %s, productos = %s, num_productores = %s, unidad_produccion = %s, incumplimientos = %s, certificaciones = %s, certificadora = %s, anio_inicial = %s, interrumpida = %s WHERE idsub_organizacion = %s",
          GetSQLValueString($sub_nombre[$i], "text"),
          GetSQLValueString($sub_producto[$i], "text"),
          GetSQLValueString($num_productores[$i], "text"),
          GetSQLValueString($unidad_produccion[$i], "text"),
          GetSQLValueString($sub_incumplimientos[$i], "text"),
          GetSQLValueString($sub_certificaciones[$i], "text"),
          GetSQLValueString($sub_certificadora[$i], "text"),
          GetSQLValueString($sub_anio_certificacion[$i], "text"),
          GetSQLValueString($sub_interrumpido, "text"),
          GetSQLValueString($idsub_organizacion[$i], "int"));
        $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());


        }
      }
    }
    /*************************** TERMINA ACTUALIZAR SUB ORGANIZACIONES ***************************/

  /*************************** INICIA INSERTAR SUB ORGANIZACIONES ***************************/

      $pais_facilitador = $_POST['pais_facilitador'];


      if(isset($_POST['unidad_produccion_nuevo'])){
        $unidad_produccion_nuevo = $_POST['unidad_produccion_nuevo'];
      }else{
        $unidad_produccion_nuevo = NULL;
      }
      if(isset($_POST['sub_producto_nuevo'])){
        $sub_producto_nuevo = $_POST['sub_producto_nuevo'];
      }else{
        $sub_producto_nuevo = NULL;
      }
      if(isset($_POST['num_productores_nuevo'])){
        $num_productores_nuevo = $_POST['num_productores_nuevo'];
      }else{
        $num_productores_nuevo = NULL;
      }
      if(isset($_POST['sub_incumplimientos_nuevo'])){
        $incumplimientos_nuevo = $_POST['sub_incumplimientos_nuevo'];
      }else{
        $incumplimientos_nuevo = NULL;
      }
      if(isset($_POST['sub_certificaciones_nuevo'])){
        $certificaciones_nuevo = $_POST['sub_certificaciones_nuevo'];
      }else{
        $certificaciones_nuevo = NULL;
      }
      if(isset($_POST['sub_certificadora_nuevo'])){
        $certificadora_nuevo = $_POST['sub_certificadora_nuevo'];
      }else{
        $certificadora_nuevo = NULL;
      }
      if(isset($_POST['sub_anio_certificacion_nuevo'])){
        $anio_inicial_nuevo = $_POST['sub_anio_certificacion_nuevo'];
      }else{
        $anio_inicial_nuevo = NULL;
      }
      if(isset($_POST['sub_interrumpido_nuevo'])){
        $interrumpida_nuevo = $_POST['sub_interrumpido_nuevo'];
      }else{
        $interrumpida_nuevo = NULL;
      }

      if(isset($_POST['sub_nombre_nuevo'])){
          $nombre_nuevo = $_POST['sub_nombre_nuevo'];

          $contador = 1;

          for($i=0;$i<count($nombre_nuevo);$i++){

            if($nombre_nuevo[$i] != NULL){
                $contador++;
              $row_opp = mysql_query("SELECT idopp, spp, pais FROM opp WHERE pais = '$pais_facilitador'",$dspp) or die(mysql_error());
              //$datos_opp = mysql_fetch_assoc($ejecutar);
              //$fecha = $_POST['fecha_inclusion'];


                $charset='utf-8'; // o 'UTF-8'
                $str = iconv($charset, 'ASCII//TRANSLIT', $pais_facilitador);
                $pais_facilitador = preg_replace("/[^a-zA-Z0-9]/", '', $str);

                $pais_facilitadorDigitos = strtoupper(substr($pais_facilitador, 0, 3));
                $formatoFecha = date("d/m/Y", $fecha);
                $fechaDigitos = substr($formatoFecha, -2);

                $contador = str_pad($contador, 3, "0", STR_PAD_LEFT);
                //$numero =  strlen($contador);

                $spp_suborganizacion = "OPP-".$pais_facilitadorDigitos."-".$fechaDigitos."-".$contador;

                while($datos_opp = mysql_fetch_assoc($row_opp)) {
                  if($datos_opp['spp'] == $spp_suborganizacion){
                    //echo "<b style='color:red'>es igual el OPP con id: $datos_opp[idf]</b><br>";
                    $contador++;
                    $contador = str_pad($contador, 3, "0", STR_PAD_LEFT);
                    $spp_suborganizacion = "OPP-".$pais_facilitadorDigitos."-".$fechaDigitos."-".$contador;
                  }/*else{
                    echo "el id encontrado es: $datos_opp[idf]<br>";
                  }*/
                  
                }


              #for($i=0;$i<count($certificacion);$i++){
              $insertSQL = sprintf("INSERT INTO sub_organizaciones (spp, nombre, pais, productos, unidad_produccion, num_productores, incumplimientos, certificaciones, certificadora, anio_inicial, interrumpida, idsolicitud_colectiva) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                GetSQLValueString($spp_suborganizacion, "text"),
                  GetSQLValueString(strtoupper($nombre_nuevo[$i]), "text"),
                  GetSQLValueString(strtoupper($pais_facilitador), "text"),
                  GetSQLValueString(strtoupper($sub_producto_nuevo[$i]), "text"),
                  GetSQLValueString(strtoupper($unidad_produccion_nuevo[$i]), "text"),
                  GetSQLValueString(strtoupper($num_productores_nuevo[$i]), "text"),
                  GetSQLValueString(strtoupper($incumplimientos_nuevo[$i]), "text"),
                  GetSQLValueString(strtoupper($certificaciones_nuevo[$i]), "text"),
                  GetSQLValueString(strtoupper($certificadora_nuevo[$i]), "text"),
                  GetSQLValueString($anio_inicial_nuevo[$i], "text"),
                  GetSQLValueString(strtoupper($interrumpida_nuevo[$i]), "text"),
                  GetSQLValueString($idsolicitud_colectiva, "int"));
              $Result = mysql_query($insertSQL, $dspp) or die(mysql_error());
              #}
            }
          }

      }


    /*************************** INICIA INSERTAR SUB ORGANIZACIONES ***************************/





  /*************************** INICIA INSERTAR PRODUCTOS ***************************/

  if(isset($_POST['volumen'])){
    $volumen = $_POST['volumen'];
  }
  if(isset($_POST['materia'])){
    $materia = $_POST['materia'];
  }
  if(isset($_POST['destino'])){
    $destino = $_POST['destino'];
  }

  if(isset($_POST['producto'])){
    $producto = $_POST['producto'];


    for ($i=0;$i<count($producto);$i++) { 
      if($producto[$i] != NULL){

          $array1[$i] = "terminado".$i; 
          $array2[$i] = "marca_propia".$i;
          $array3[$i] = "marca_cliente".$i;
          $array4[$i] = "sin_cliente".$i;

          if(isset($_POST[$array1[$i]])){
            $terminado = $_POST[$array1[$i]];
          }else{
            $terminado = null;
          }
          if(isset($_POST[$array2[$i]])){
            $marca_propia = $_POST[$array2[$i]];
          }else{
            $marca_propia = null;
          }
          if(isset($_POST[$array3[$i]])){
            $marca_cliente = $_POST[$array3[$i]];
          }else{
            $marca_cliente = null;
          }
          if(isset($_POST[$array4[$i]])){
            $sin_cliente = $_POST[$array4[$i]];
          }else{
            $sin_cliente = null;
          }

          //$terminado = $_POST[$array1[$i]];
          //$marca_propia = $_POST[$array2[$i]];
          //$marca_cliente = $_POST[$array3[$i]];
          //$sin_cliente = $_POST[$array4[$i]];

          //$str = iconv($charset, 'ASCII//TRANSLIT', $producto[$i]);
          //$producto[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));
          

          $str = iconv($charset, 'ASCII//TRANSLIT', $destino[$i]);
          $destino[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

          $str = iconv($charset, 'ASCII//TRANSLIT', $materia[$i]);
          $materia[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));


            $insertSQL = sprintf("INSERT INTO productos (idopp, idsolicitud_colectiva, producto, volumen, terminado, materia, destino, marca_propia, marca_cliente, sin_cliente) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
              GetSQLValueString($_POST['idopp'], "int"),
                  GetSQLValueString($idsolicitud_colectiva, "int"),
                  GetSQLValueString($producto[$i], "text"),
                  GetSQLValueString($volumen[$i], "text"),
                  GetSQLValueString($terminado[$i], "text"),
                  GetSQLValueString($materia[$i], "text"),
                  GetSQLValueString($destino[$i], "text"),
                  GetSQLValueString($marca_propia[$i], "text"),
                  GetSQLValueString($marca_cliente[$i], "text"),                    
                  GetSQLValueString($sin_cliente[$i], "text"));

          $Result = mysql_query($insertSQL, $dspp) or die(mysql_error());
      }
    }
    /***************************** TERMINA INSERTAR PRODUCTOS ******************************/

  }

  /*$marca_propia = $_POST['marca_propia'];
  $marca_cliente = $_POST['marca_cliente'];
  $sin_cliente = $_POST['sin_cliente'];*/





    // SE ACTUALIZAN LOS PRODUCTOS
    if(isset($_POST['producto_actual'])){
      $producto_actual = $_POST['producto_actual'];
    }else{
      $producto_actual = '';
    }
    if(isset($_POST['volumen_actual'])){
      $volumen_actual = $_POST['volumen_actual'];
    }else{
      $volumen_actual = '';
    }
    if(isset($_POST['materia_actual'])){
      $materia_actual = $_POST['materia_actual'];
    }else{
      $materia_actual = '';
    }
    if(isset($_POST['destino_actual'])){
      $destino_actual = $_POST['destino_actual'];
    }else{
      $destino_actual = '';
    }

    if(isset($_POST['idproducto'])){
      $idproducto = $_POST['idproducto'];
    }else{
      $idproducto = '';
    }

    if(isset($_POST['idproducto'])){
      for ($i=0;$i<count($producto_actual);$i++) { 
        if($producto_actual[$i] != NULL){

        $array1 = "terminado_actual".$i; 
        $array2 = "marca_propia_actual".$i;
        $array3 = "marca_cliente_actual".$i;
        $array4 = "sin_cliente_actual".$i;


        if(isset($_POST[$array1])){
          $terminado = $_POST[$array1];
        }else{
          $terminado = '';
        }
        if(isset($_POST[$array2])){
          $marca_propia = $_POST[$array2];
        }else{
          $marca_propia = '';
        }
        if(isset($_POST[$array3])){
          $marca_cliente = $_POST[$array3];
        }else{
          $marca_cliente = '';
        }
        if(isset($_POST[$array4])){
          $sin_cliente = $_POST[$array4];
        }else{
          $sin_cliente = '';
        }

            //$str = iconv($charset, 'ASCII//TRANSLIT', $producto_actual[$i]);
            //$producto_actual[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

            $str = iconv($charset, 'ASCII//TRANSLIT', $destino_actual[$i]);
            $destino_actual[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));

            $str = iconv($charset, 'ASCII//TRANSLIT', $materia_actual[$i]);
            $materia_actual[$i] =  strtoupper(preg_replace("/[^a-zA-Z0-9\s\.\,]/", '', $str));


        $updateSQL = sprintf("UPDATE productos SET producto = %s, volumen = %s, terminado = %s, materia = %s, destino = %s, marca_propia = %s, marca_cliente = %s, sin_cliente = %s WHERE idproducto = %s",
          GetSQLValueString($producto_actual[$i], "text"),
          GetSQLValueString($volumen_actual[$i], "text"),
          GetSQLValueString($terminado, "text"),
          GetSQLValueString($materia_actual[$i], "text"),
          GetSQLValueString($destino_actual[$i], "text"),
          GetSQLValueString($marca_propia, "text"),
          GetSQLValueString($marca_cliente, "text"),
          GetSQLValueString($sin_cliente, "text"),
          GetSQLValueString($idproducto[$i], "int"));
        $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());


        }
      }
    }




  $mensaje = "Datos Actualizados Correctamente";

}

//****** INICIA ENVIAR COTIZACION *******///
if(isset($_POST['enviar_cotizacion']) && $_POST['enviar_cotizacion'] == "1"){
  $estatus_dspp = '4'; // COTIZACIÓN ENVIADA
  $estatus_publico = '1';

  $rutaArchivo = "../../archivos/ocArchivos/cotizaciones/";
  $procedimiento = $_POST['procedimiento'];

  if(!empty($_FILES['cotizacion_opp']['name'])){
      $_FILES["cotizacion_opp"]["name"];
        move_uploaded_file($_FILES["cotizacion_opp"]["tmp_name"], $rutaArchivo.$fecha."_".$_FILES["cotizacion_opp"]["name"]);
        $cotizacion_opp = $rutaArchivo.basename($fecha."_".$_FILES["cotizacion_opp"]["name"]);
  }else{
    $cotizacion_opp = NULL;
  }

  //ACTUALIZAMOS LA SOLICITUD DE CERTIFICACION AGREGANDO LA COTIZACIÓN
  $updateSQL = sprintf("UPDATE solicitud_colectiva SET tipo_procedimiento = %s, cotizacion_opp = %s, estatus_dspp = %s WHERE idsolicitud_colectiva = %s",
    GetSQLValueString($procedimiento, "text"),
    GetSQLValueString($cotizacion_opp, "text"),
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($idsolicitud_colectiva, "int"));
  $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());

  // ACTUALIZAMOS EL ESTATUS_DSPP DEL OPP
  $updateSQL = sprintf("UPDATE opp SET estatus_dspp = %s WHERE idopp = %s",
    GetSQLValueString($estatus_dspp, "int"),
    GetSQLValueString($_POST['idopp'], "int"));
  $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());

  //AGREGAMOS EL PROCESO DE CERTIFICACIÓN
  $insertSQL = sprintf("INSERT INTO proceso_certificacion (idsolicitud_colectiva, estatus_publico, estatus_dspp) VALUES (%s, %s, %s)",
    GetSQLValueString($idsolicitud_colectiva, "int"),
    GetSQLValueString($estatus_publico, "int"),
    GetSQLValueString($estatus_dspp, "int"));
  $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

  //ASUNTO DEL CORREO
  //$row_oc = mysql_query("SELECT * FROM oc WHERE idoc = $_POST[idoc]", $dspp) or die(mysql_error());
  //$oc = mysql_fetch_assoc($row_oc);

  $row_opp = mysql_query("SELECT opp.nombre, opp.abreviacion AS 'abreviacion_opp', opp.spp, opp.password, opp.email, oc.email1, oc.email2, oc.abreviacion AS 'abreviacion_oc', oc.pais AS 'pais_oc', solicitud_colectiva.contacto1_email, solicitud_colectiva.contacto2_email, solicitud_colectiva.adm1_email FROM opp INNER JOIN solicitud_colectiva ON opp.idopp = solicitud_colectiva.idopp INNER JOIN oc ON solicitud_colectiva.idoc = oc.idoc WHERE idsolicitud_colectiva = $idsolicitud_colectiva", $dspp) or die(mysql_error());
  $opp_detail = mysql_fetch_assoc($row_opp);

  $asunto = "D-SPP Cotización (Solicitud de Certificación para Organizaciones de Pequeños Productores)";

  $cuerpo_mensaje = '
    <html>
    <head>
      <meta charset="utf-8">
    </head>
    <body>
    
      <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
        <thead>
          <tr>
            <th rowspan="4" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
            <th scope="col" align="left" width="280" ><strong>Notificación de Cotización / Price quote  Notification</strong></th>
          </tr>
        </thead>
        <tbody>
          <tr style="text-align:justify">
            <td colspan="2">
              <p>
                Email Organismo de Certificación / Certification Entity: <span style="color:red">'.$opp_detail['email1'].'</span>
              </p>
            </td>
          </tr>
          <tr style="text-align:justify">
            <td colspan="2">
              <p>
                <b style="color:red">'.$opp_detail['abreviacion_oc'].'</b> ha enviado la cotización correspondiente a la Solicitud de Certificación para Organizaciones de Pequeños Productores.
              </p>
              <p>
                Por favor iniciar sesión en el siguiente enlace <a href="http://d-spp.org/">www.d-spp.org/</a> como OPP, para poder acceder a la cotización.
              </p>
            </td>
          </tr>

          <tr style="text-align:justify">
            <td colspan="2">
              <span style="color:red">¿Qué es lo de debo realizar ahora?. Debes "Aceptar" o "Rechazar" la cotización</span>
              <ol>

                <li>Debes iniciar sesión dentro del sistema <a href="http://d-spp.org/">D-SPP (clic aquí)</a> como Organización de Pequeños Productores(OPP).</li>
                <li>Tu Usuario: <b style="color:red">'.$opp_detail['spp'].'</b> y Contraseña: <b style="color:red">'.$opp_detail['password'].'</b></li>
                <li>Dentro de tu cuenta debes seleccionar Solicitudes > Listado Solicitudes.</li>
                <li>Dentro de la tabla solicitudes debes localizar la columna "Cotización" Y seleccionar el botón Verde (aceptar cotización) ó el botón Rojo (rechazar cotización)</li>
                <li>En caso de aceptar la cotización debes esperar a que finalice el "Periodo de Objeción"(en caso de que sea la primera vez que solicitas la certificación SPP)</li>
              </ol>
            </td>
          </tr> 
  
          <tr>
            <td colspan="2" style="padding-bottom:10px;">
              <hr>
              <p><b>English below</b></p>
            </td>
          </tr>
          
          <tr style="font-style: italic; text-align:justify">
            <td colspan="2">
              <p>
                <b style="color:red">'.$opp_detail['abreviacion_oc'].'</b> has sent the price quote corresponding to the Certification Application for Small Producers’ Organizations (SPOs)
              </p>
              <p>
                Please open a session as an SPO at the following link: <a href="http://d-spp.org/">www.d-spp.org/</a> in order to access the price quote.
              </p>
            </td>
          </tr>
          <tr style="font-style: italic; text-align:justify">
            <td colspan="2">
              <span style="color:red">What should I do now? You should “Accept” or “Reject” the price quote.</span>
              <ol>

                <li>
                  You should open a session in the <a href="http://d-spp.org/">D-SPP (clic aquí)</a> system as a Small Producers’ Organization (SPO).
                </li>
                <li>Your User (SPP#): <b style="color:red">'.$opp_detail['spp'].'</b> and your Password is:  <b style="color:red">'.$opp_detail['password'].'</b></li>
                <li>Within your account you should select Applications  >  Applications List.</li>
                <li>In the Applications table, you should locate the column entitled “Price Quote” and select the Green button (accept price quote) or the Red button (reject price quote).</li>
                <li>If you accept the price quote, you will need to wait until the “Objection Period” is over (if this is the first time you are applying for SPP certification).</li>
              </ol>
            </td>
          </tr> 

          <tr>
            <td colspan="2">
              <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
                <tbody>
                  <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
                    <td width="130px">Nombre de la organización/Organization name</td>
                    <td width="130px">País / Country</td>
                    <td width="130px">Organismo de Certificación / Certification Entity</td>
                    <td width="130px">Fecha de envío / Shipping Date</td>
                 
                    
                  </tr>
                  <tr style="font-size: 12px; text-align:justify">
                    <td style="padding:10px;">
                      '.$_POST['nombre'].' - ('.$opp_detail['abreviacion_opp'].')
                    </td>
                    <td style="padding:10px;">
                      '.$_POST['pais'].'
                    </td>
                    <td style="padding:10px;">
                      '.$opp_detail['abreviacion_oc'].'
                    </td>
                    <td style="padding:10px;">
                    '.date('d/m/Y', $fecha).'
                    </td>
                  </tr>

                </tbody>
              </table>        
            </td>
          </tr>


          <tr>
            <td coslpan="2">Para cualquier duda o aclaración por favor contactar a: soporte@d-spp.org</td>
          </tr>
        </tbody>
      </table>

    </body>
    </html>
  ';

  $mail->AddAddress($_POST['email']);
  $mail->AddAddress($_POST['contacto1_email']);
  $mail->AddBCC($spp_global);
  if(!empty($opp['email1'])){
    //$mail->AddCC($oc['email1']);
      $token = strtok($opp['email1'], "\/\,\;");
      while ($token !== false)
      {
        $mail->AddCC($token);
        $token = strtok('\/\,\;');
      }

  }
  if(!empty($opp['email2'])){
    //$mail->AddCC($oc['email2']);
      $token = strtok($opp['email2'], "\/\,\;");
      while ($token !== false)
      {
        $mail->AddCC($token);
        $token = strtok('\/\,\;');
      }

  }
  //se adjunta la cotización
  $mail->AddAttachment($cotizacion_opp);

  //$mail->Username = "soporte@d-spp.org";
  //$mail->Password = "/aung5l6tZ";
  $mail->Subject = utf8_decode($asunto);
  $mail->Body = utf8_decode($cuerpo_mensaje);
  $mail->MsgHTML(utf8_decode($cuerpo_mensaje));
  $mail->Send();
  $mail->ClearAddresses();


  $mensaje = "Se ha enviado la cotizacion al OPP";
}
//****** TERMINA ENVIAR COTIZACION *******///

  ////INICIA INGRESAR LAS OBSERVACIONES REALIZADAS
if(isset($_POST['agregar_observaciones']) && $_POST['agregar_observaciones'] == 1){
  $idsolicitud_colectiva = $_POST['idsolicitud_colectiva'];


  $updateSQL = sprintf("UPDATE solicitud_colectiva SET observaciones = %s WHERE idsolicitud_colectiva = %s",
    GetSQLValueString($_POST['observaciones_solicitud'], "text"),
    GetSQLValueString($idsolicitud_colectiva, "int"));
  $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());

  $row_informacion = mysql_query("SELECT opp.nombre, opp.abreviacion AS 'abreviacion_opp', opp.spp, opp.password, opp.email, oc.email1, oc.email2, oc.abreviacion AS 'abreviacion_oc', solicitud_colectiva.contacto1_email, solicitud_colectiva.contacto2_email, solicitud_colectiva.adm1_email FROM opp INNER JOIN solicitud_colectiva ON opp.idopp = solicitud_colectiva.idopp INNER JOIN oc ON solicitud_colectiva.idoc = oc.idoc WHERE idsolicitud_colectiva = $idsolicitud_colectiva", $dspp) or die(mysql_error());
  $informacion = mysql_fetch_assoc($row_informacion);
  
  $asunto = "D-SPP | Observaciones Solicitud Certficación SPP";

  $cuerpo_mensaje = '
    <html>
    <head>
      <meta charset="utf-8">
    </head>
    <body>
    
      <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
        <tbody>
          <tr>
            <th rowspan="4" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
            <th scope="col" align="left" width="280" ><strong>Observaciones Realizadas a la Solicitud de Certificación SPP / Observations SPP Certification Application</strong></th>
          </tr>
          <tr>
            <td align="left" style="color:#ff738a;">
              <p>Organismo de Certificación: '.$informacion['abreviacion_oc'].'</p>
              <p>Email Organismo de Certificación / Certification Entity: '.$informacion['email1'].'</p>
            </td>
          </tr>

          <tr>
            <td aling="left" style="text-align:justify">
            A continuación se listan las siguientes observaciones realizadas a su Solicitud de Certificación SPP. Por favor proceda a corregir y/o complementar su solicitud, para poder continuar con el proceso de certificación.
            </td>
          </tr>
          <tr>
            <td aling="left" style="text-align:justify">
            Following is a list of observations regarding your SPP Certification Application. Please correct and/or complement your application, in order for the certification process to continue.
            </td>
          </tr>


          <tr>
            <td colspan="2" style="padding-top:20px;">
            <hr>
              '.$_POST['observaciones_solicitud'].'   
            <hr>  
            </td>
          </tr>
                <tr>
                  <td colspan="2">
                    <span style="color:red">¿Qué es lo de debo realizar ahora?</span>
                    <ol>
                      <li>Debes iniciar sesión dentro del sistema <a href="http://d-spp.org/">D-SPP (clic aquí)</a> como Organización de Pequeños Productores(OPP).</li>
                      <li>Usuario(#SPP): <span style="color:red">'.$informacion['spp'].'</span> y contraseña: <span style="color:red">'.$informacion['password'].'</span> de su cuenta.</li>
                      <li>Dentro de tu cuenta debes seleccionar <span style="color:red">"Solicitudes"</span> > <span style="color:red">Listado Solicitudes</span>.</li>
                      <li>Dentro de la tabla solicitudes debes localizar la columna <span style="color:red">"Acciones"</span> Y seleccionar el botón <span style="color:red">"CONSULTAR"</span>.</li>
                      <li>Al dar clic en "Consultar" podra visualizar su Solicitud de Certificación" la cual puede ser modificada.</li>
                      <li>Una vez realizados los cambios correspondientes debe dar clic en el boton <span style="color:red">"Actualizar Solicitud" al inicio de su Solicitud</span>.</li>
                    </ol>
                  </td>
                </tr> 

                <tr>
                  <td colspan="2">
                    <span style="color:red">What should I do now?</span>
                    <ol>
                      <li>
                        You should open a session in the <a href="http://d-spp.org/">D-SPP (click here)</a> system as a Small Producers’ Organization (SPO).
                      </li>
                      <li>
                        Your User (#SPP) is: <span style="color:red">'.$informacion['spp'].'</span> and your password is: <span style="color:red">'.$informacion['password'].'</span> de su cuenta.
                      </li>
                      <li>
                        Within your account, you should select “Applications”  >  Applications List.
                      </li>
                      <li>
                        In the Applications table, you should locate the column entitled “<span style="color:red">Actions”</span> and select the <span style="color:red">“CONSULT”</span> button.
                      </li>
                      <li>
                        If you click on “Consult,” you will be able to see your Certification Application, which may be modified.
                      </li>
                      <li>
                        After making the corresponding changes, you should click on the <span style="color:red">“Update Application”</span> button at the top of your Application.
                      </li>
                    </ol>
                  </td>
                </tr>

                <tr>
                  <td colspan="2">Para cualquier duda o aclaración por favor contactar a: soporte@d-spp.org</td>
                </tr>
        </tbody>
      </table>

    </body>
    </html>
  ';
  if(isset($informacion['contacto1_email'])){
    $mail->AddAddress($informacion['contacto1_email']);
  }
  if(isset($informacion['contacto2_email'])){
    $mail->AddAddress($informacion['contacto2_email']);
  }
  if(isset($informacion['adm1_email'])){
    $mail->AddAddress($informacion['adm1_email']);
  }
  //$mail->Username = "soporte@d-spp.org";
  //$mail->Password = "/aung5l6tZ";
  $mail->Subject = utf8_decode($asunto);
  $mail->Body = utf8_decode($cuerpo_mensaje);
  $mail->MsgHTML(utf8_decode($cuerpo_mensaje));

  if($mail->Send()){
    $mail->ClearAddresses();
    echo "<script>alert('Correo enviado Exitosamente.');location.href ='javascript:history.back()';</script>";
  }else{
    $mail->ClearAddresses();
    echo "<script>alert('Error, no se pudo enviar el correo, por favor contacte al administrador: soporte@d-spp.org');location.href ='javascript:history.back()';</script>";
  }

}
  //// TERMINA INGRESAR OBSERVACIONES

$query = "SELECT solicitud_colectiva.*, opp.nombre, opp.spp AS 'spp_opp', opp.sitio_web, opp.email, opp.telefono, opp.pais, opp.ciudad, opp.razon_social, opp.direccion_oficina, opp.direccion_fiscal, opp.rfc, opp.ruc, oc.abreviacion AS 'abreviacionOC', porcentaje_productoVentas.* FROM solicitud_colectiva INNER JOIN opp ON solicitud_colectiva.idopp = opp.idopp INNER JOIN oc ON solicitud_colectiva.idoc = oc.idoc LEFT JOIN porcentaje_productoVentas ON solicitud_colectiva.idsolicitud_colectiva = porcentaje_productoVentas.idsolicitud_colectiva WHERE solicitud_colectiva.idsolicitud_colectiva = $idsolicitud_colectiva";
$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
$solicitud = mysql_fetch_assoc($ejecutar);

?>

<div class="row" style="font-size:12px;">

  <?php 
  if(isset($mensaje)){
  ?>
  <div class="col-md-12 alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <p class="text-center"><strong><?php echo $mensaje; ?></strong></p>
  </div>
  <?php
  }
  ?>

  <form action="" name="" method="POST" enctype="multipart/form-data">
    <fieldset>
      <div class="col-md-12 alert alert-primary" style="padding:7px;">
        <h3 class="text-center">Solicitud de Certificación Colectiva para Organizaciones de Pequeños Productores</h3>
      </div>


      <div class="col-md-12 text-center alert alert-success" style="padding:7px;"><b>DATOS GENERALES</b></div>

      <div class="col-lg-12 alert alert-info" style="padding:7px;">
        <div class="col-md-12">
          <!--<div class="col-xs-4">
            <b>ENVAR AL OC (selecciona el OC al que deseas enviar la solicitud):</b>
            <input type="text" class="form-control" value="<?php echo $solicitud['abreviacionOC']; ?>" readonly>
          </div>-->
          <div class="col-md-4">
            <b>AGREGAR OBSERVACIONES</b>
            <button type="button" class="btn btn-primary" style="width:100%" data-toggle="modal" data-target="<?php echo "#observaciones".$_GET['IDsolicitud']; ?>">Agregar Observaciones</button>
          </div>

          <div class="col-xs-4">
            <b>TIPO DE SOLICITUD</b>
            <input type="text" class="form-control" value="<?php echo $solicitud['tipo_solicitud']; ?>"readonly>
            <button type="submit" class="btn btn-warning form-control" style="color:white" name="guardar_cambios" value="1">
              <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>Actualizar Solicitud
            </button>
            <!--<input type="submit" style="color:white" class="btn btn-warning form-control" value="Actualizar Solicitud">
            <input type="hidden" name="guarda_cambios" value="1">-->

          </div>
          <div class="col-md-4">
            <?php 
            if(empty($solicitud['cotizacion_opp'])){
            ?>
              <b>CARGAR COTIZACIÓN</b>
              <input type="file" class="form-control" id="cotizacion_opp" name="cotizacion_opp"> 
              <input type="hidden" name="idoc" value="<?php echo $solicitud['idoc']; ?>"> 
              <button class="btn btn-sm btn-success form-control" style="color:white" id="enviar_cotizacion" name="enviar_cotizacion" type="submit" value="1" onclick="return validar()">
                <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> Enviar Cotización
              </button>
              <!--<button type="submit" class="btn btn-success form-control" style="color:white" name="enviar_cotizacion" value="Enviar"><span class="glyphicon glyphicon-envelope" aria-hidden="true" onclick="return validar()"></span> Enviar Cotización</button>-->

            <?php 
            }else{
              echo "<b style='font-size:14px;'>Ya se ha enviado la cotización</b>";
            }
             ?>
          </div>

        </div>
      </div>
      <div class="col-xs-12 text-center">
        <div class="row">
      <h4>Procedimiento de Certificación <br><small>(realizado por OC)</small></h4>
        </div>
      </div>
      <div class="col-xs-3 text-center">
        <div class="row">
          <div class="col-xs-12">
            <p style="font-size:10px;"><b>DOCUMENTAL "ACORTADO"</b></p> 
          </div>       
          <div class="col-xs-12">
            <input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='DOCUMENTAL "ACORTADO"' <?php if($solicitud['tipo_procedimiento'] == 'DOCUMENTAL "ACORTADO"'){ echo "checked"; } ?>>

          </div>                        
        </div>
      </div>
      <div class="col-xs-3 text-center">
        <div class="row">
          <div class="col-xs-12">
            <p style="font-size:10px;"><b>DOCUMENTAL "NORMAL"</b></p> 
          </div>
          <div class="col-xs-12">
            <input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='DOCUMENTAL "NORMAL"' <?php if($solicitud['tipo_procedimiento'] == 'DOCUMENTAL "NORMAL"'){ echo "checked"; } ?>>

          </div>                
        </div>
      </div>
      <div class="col-xs-3 text-center">
        <div class="row">
          <div class="col-xs-12">
            <p style="font-size:10px;"><b>COMPLETO "IN SITU"</b></p>  
          </div>
          <div class="col-xs-12">
            <input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='COMPLETO "IN SITU"' <?php if($solicitud['tipo_procedimiento'] == 'COMPLETO "IN SITU"'){ echo "checked"; } ?>>

          </div>                
        </div>
      </div>
      <div class="col-xs-3 text-center">
        <div class="row">
          <div class="col-xs-12">
            <p style="font-size:10px;"><b>COMPLETO "A DISTANCIA"</b></p>  
          </div>
          <div class="col-xs-12">
            <input type="radio" data-on-color="success" data-off-color="danger" data-size="small" name="procedimiento" value='COMPLETO "A DISTANCIA"' <?php if($solicitud['tipo_procedimiento'] == 'COMPLETO "A DISTANCIA"'){ echo "checked"; } ?>>

          </div>                
        </div>
      </div> 

      <!------ INICIA INFORMACION GENERAL Y DATOS FISCALES ------>
      <div class="col-md-12 text-center alert alert-success" style="padding:7px;"><b>ORGANIZACIÓN FACILITADORA( <a data-toggle="tooltip" title="Organización de Pequeños Productores de segundo o más alto nivel que representa a sus organizaciones miembros en su proceso de certificación colectiva" href="#"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span></a> ) INFORMACIÓN GENERAL ( <a data-toggle="tooltip" title="Los datos generales de la Organización de Pequeños Productores solicitante serán publicados por SPP Global." href="#"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span></a> )</b></div>
      <!------ INICIA INFORMACION GENERAL Y DATOS FISCALES ------>
      <div class="col-lg-12">
        <div class="col-md-6">
          <div class="col-md-12 text-center alert alert-warning" style="padding:7px;">INFORMACIÓN GENERAL</div>
          <label for="fecha_elaboracion">FECHA DE ELABORACIÓN</label>
          <input type="text" class="form-control" id="fecha_elaboracion" name="fecha_elaboracion" value="<?php echo date('d-m-Y', $solicitud['fecha_registro']); ?>" readonly>  

          <label for="spp">CODIGO DE IDENTIFICACIÓN SPP(#SPP): </label>
          <input type="text" class="form-control" id="spp" name="spp" value="<?php echo $solicitud['spp_opp']; ?>" readonly>

          <label for="nombre_facilitador" style="color:red">NOMBRE COMPLETO DE LA ORGANIZACIÓN FACILITADORA DE LA QUE FORMAN PARTE LAS ORGANIZACIONES DE BASE A INCLUIR EN LA CERTIFICACIÓN COLECTIVA:</label>
          <textarea name="nombre_facilitador" id="nombre_facilitador" class="form-control"><?php echo $solicitud['nombre']; ?></textarea>


          <label for="pais">PAÍS:</label>
          <?php 
          $row_pais = mysql_query("SELECT * FROM paises",$dspp) or die(mysql_error());
           ?>
           <select name="pais" id="pais" class="form-control">
            <option value="">Selecciona un pais</option>
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
           <input type="hidden" name="pais_facilitador" value="<?php echo $solicitud['pais']; ?>">

          <label for="direccion_oficina">DIRECCIÓN COMPLETA DE SUS OFICINAS CENTRALES DE LA ORGANIZACIÓN FACILITADORA (CALLE, BARRIO, LUGAR, REGIÓN):</label>
          <textarea name="direccion_oficina" id="direccion_oficina"  class="form-control"><?php echo $solicitud['direccion_oficina']; ?></textarea>

          <label for="email">CORREO ELECTRÓNICO:</label>
          <input type="text" class="form-control" id="email" name="email" value="<?php echo $solicitud['email']; ?>">

          <label for="email">TELÉFONOS (CÓDIGO DE PAÍS+ CÓDIGO DE ÁREA + NÚMERO):</label>
          <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo $solicitud['telefono']; ?>">  

          <label for="sitio_web">SITIO WEB:</label>
          <input type="text" class="form-control" id="sitio_web" name="sitio_web" value="<?php echo $solicitud['sitio_web']; ?>">

        </div>

        <div class="col-md-6">
          <div class="col-md-12 text-center alert alert-warning" style="padding:7px;">INFORMACIÓN FISCAL</div>

          <label for="razon_social">NOMBRE COMERCIAL</label>
          <input type="text" class="form-control" id="razon_social" name="razon_social" value="<?php echo $solicitud['razon_social']; ?>">

          <label for="direccion_fiscal">DIRECCIÓN FISCAL</label>
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
          <div class="col-md-12 text-center alert alert-warning" style="padding:7px;">PERSONAS DE CONTACTO DE LA SOLICITUD</div>

          <label for="persona1">PERSONAS DE CONTACTO</label>
          <input type="text" class="form-control" id="persona1" name="contacto1_nombre" value="<?php echo $solicitud['contacto1_nombre']; ?>" placeholder="* Nombre persona 1" required>
          <input type="text" class="form-control" id="" name="contacto2_nombre" value="<?php echo $solicitud['contacto2_nombre']; ?>" placeholder="Name Person 2">

          <label for="cargo">CARGO(S)</label>
          <input type="text" class="form-control" id="cargo" name="contacto1_cargo" value="<?php echo $solicitud['contacto1_cargo']; ?>" placeholder="* Cargo persona 1" required>
          <input type="text" class="form-control" id="" name="contacto2_cargo" value="<?php echo $solicitud['contacto2_cargo']; ?>" placeholder="Position Person 2">

          <label for="email">CORREO ELECTRÓNICO PERSONA(S) DE CONTACTO</label>
          <input type="email" class="form-control" id="email" name="contacto1_email" value="<?php echo $solicitud['contacto1_email']; ?>" placeholder="* Email persona 1" required>
          <input type="email" class="form-control" id="" name="contacto2_email" value="<?php echo $solicitud['contacto2_email']; ?>" placeholder="Email Person 2">

          <label for="telefono">TELÉFONO PERSONA(S) DE CONTACTO:</label>
          <input type="text" class="form-control" id="telefono" name="contacto1_telefono" value="<?php echo $solicitud['contacto1_telefono']; ?>" placeholder="* Teléfono persona 1" required>
          <input type="text" class="form-control" id="" name="contacto2_telefono" value="<?php echo $solicitud['contacto2_telefono']; ?>" placeholder="Teléfono persona 2">

        </div>

        <div class="col-md-6">
          <div class="col-md-12 text-center alert alert-warning" style="padding:7px;">PERSONAL DEL ÁREA ADMINISTRATIVA</div>

          <label for="persona_adm">PERSONA(S) DEL ÁREA ADMINISTRATIVA</label>
          <input type="text" class="form-control" id="persona_adm" name="adm1_nombre" value="<?php echo $solicitud['adm1_nombre']; ?>" placeholder="Nombre persona 1">
          <input type="text" class="form-control" id="" name="adm2_nombre" value="<?php echo $solicitud['adm2_nombre']; ?>" placeholder="Nombre persona 2">

          <label for="email_adm">CORREO ELECTRÓNICO</label>
          <input type="email" class="form-control" id="email_adm" name="adm1_email" value="<?php echo $solicitud['adm1_email']; ?>" placeholder="Email persona 1">
          <input type="email" class="form-control" id="" name="adm2_email" value="<?php echo $solicitud['adm2_email']; ?>" placeholder="Email persona 2">

          <label for="telefono_adm">TELÉFONO(S) PERSONA(S) DEL ÁREA ADMINISTRATIVA:</label>
          <input type="text" class="form-control" id="telefono_adm" name="adm1_telefono" value="<?php echo $solicitud['adm1_telefono']; ?>" placeholder="Teléfono persona 1">
          <input type="text" class="form-control" id="" name="adm2_telefono" value="<?php echo $solicitud['adm2_telefono']; ?>" placeholder="Teléfono persona 2">
        </div>
      </div>
      <!------ FIN INFORMACION CONTACTOS Y AREA ADMINISTRATIVA ------>
      <!--- INICIA TABLA SOBRE INFORMACION DE LAS ORGANIZACIONES INVOLUCRADAS -->
      <div class="col-lg-12">
        <table class="table table-bordered" id="tabla_organizaciones">
          <thead>
            <tr class="success">
              <th rowspan="2" style="margin:0px;padding:0px;">
                <button type="button" onclick="tabla_organizaciones()" class="btn btn-primary" aria-label="Left Align">
                  <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                </button>
              </th>

              <th rowspan="2">Nombre Completo</th>
              <th rowspan="2">Código de Identificación SPP Identification Code</th>
              <th rowspan="2">Número de Socios</th>
              <th rowspan="2">Productos a incluir en la certificación Colectiva</th>
              <th rowspan="2">Tamaño máximo de la unidad Producción <span style="background:yellow">por Productor</span> del producto para incluir en la certificación colectiva (unidad de medida)</th>
              <th colspan="4">Llenar la tabla de acuerdo a las certificaciones que tiene (Ejemplo: EU, NOP, JASS, FLO, etc)</th>

              <th rowspan="2">De las certificaciones con las que cuenta, en su más reciente evaluación externa, ¿cuántos incumplimientos se identificaron. ¿Están resueltos o cuál es su estado?</th>

            </tr>
            <tr>
              <th>Certificación</th>
              <th>Certificadora</th>
              <th>Año inicial</th>
              <th>¿Ha sido Interrumpida?</th>
            </tr>

          </thead>
          <tbody>
            <?php 
            $query_sub_organizaciones = "SELECT * FROM sub_organizaciones WHERE idsolicitud_colectiva = $idsolicitud_colectiva";
            $detalle_sub_organizacioens = mysql_query($query_sub_organizaciones, $dspp) or die(mysql_error());
            $contador = 0;
            $contador2 = 1;

            while($row_sub_organizaciones = mysql_fetch_assoc($detalle_sub_organizacioens)){
            ?>
              <tr>  
                <td>
                  <?php echo $contador2; ?>
                </td>
                <td>
                  <input type="text" class="form-control" style="width:150px;" name="sub_nombre[]" value="<?php echo $row_sub_organizaciones['nombre']; ?>" placeholder="Nombre completo">
                </td>
                <td>
                  <?php echo $row_sub_organizaciones['spp']; ?>
                </td>
                <td>
                  <input type="number" class="form-control" style="width:120px;" name="num_productores[]" value="<?php echo $row_sub_organizaciones['num_productores']; ?>" placeholder="Solo numero">
                </td>
                <td>
                  <textarea class="form-control" style="width:200px;" name="sub_producto[]" id="" rows="3" placeholder="Productos"><?php echo $row_sub_organizaciones['productos']; ?></textarea>
                </td>
                <td>
                  <input type="text" class="form-control" style="width:150px;" name="unidad_produccion[]" value="<?php echo $row_sub_organizaciones['unidad_produccion']; ?>" placeholder="Unidad producción">
                </td>
                <td>
                                  <textarea class="form-control" style="width:150px;" name="sub_certificaciones[]" placeholder="Certificación" rows="3" required><?php echo $row_sub_organizaciones['certificaciones']; ?></textarea>
                </td>
                <td>
                                  <textarea class="form-control" style="width:150px;" name="sub_certificadora[]" placeholder="Certificadora" rows="3" required><?php echo $row_sub_organizaciones['certificadora']; ?></textarea>
                </td>
                <td>
                                  <textarea class="form-control" style="width:150px;" name="sub_anio_certificacion[]"  placeholder="Año inicial" rows="3" required><?php echo $row_sub_organizaciones['anio_inicial']; ?></textarea>
                </td>
                <td>
                    <?php 
                      if($row_sub_organizaciones['interrumpida'] == 'SI'){
                        echo "SI <input type='radio'  name='sub_interrumpido".$contador."' value='SI' checked><br>";
                      }else{
                        echo "SI <input type='radio'  name='sub_interrumpido".$contador."' value='SI'><br>";
                      }
                      if($row_sub_organizaciones['interrumpida'] == 'NO'){
                        echo "NO <input type='radio'  name='sub_interrumpido".$contador."' value='NO' checked>";
                      }else{
                        echo "NO <input type='radio'  name='sub_interrumpido".$contador."' value='NO'>";
                      }
                     ?> 
                </td>
                <td>
                  <textarea class="form-control" style="width:200px;" name="sub_incumplimientos[]" id="" rows="3"><?php echo $row_sub_organizaciones['incumplimientos']; ?></textarea>
                </td>
                
              </tr>
              <input type="hidden" name="idsub_organizacion[]" value="<?echo $row_sub_organizaciones['idsub_organizacion']?>">
            <?php

            $contador++;
            $contador2++;
            }
             ?>

            <tr>
              <td colspan="11">
                <h6>La información proporcionada en esta sección será manejada con total confidencialidad. Por favor, inserte líneas adicionales si es necesario.</h6>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <!--- TERMINA TABLA SOBRE LA INFORMACION DE LAS ORGANIZACIONES INVOLUCRADAS -->

      <!------ INICIA INFORMACION DATOS DE OPERACIÓN ------>
    
      <div class="col-lg-6" style="background:#2ecc71">
          <label for="total_miembros">TOTAL DE MIEMBROS INCLUIDOS EN LA CERTIFICACIÓN COLECTIVA:</label>
          <input type="number" class="form-control" id="total_miembros" name="total_miembros" value="<?php echo $solicitud['total_miembros']; ?>" placeholder="just number" required>
      </div>

      <div class="col-md-12 text-center alert alert-success" style="margin-top:5em;">DATOS DE OPERACIÓN</div>

      <div class="col-lg-12">
        <div class="col-md-12">

          <div >
            <label for="alcance_opp">
              1.  INDIQUE CON UNA  ‘X’ EL ALCANCE QUE TIENE LA ORGANIZACIÓN DE PEQUEÑOS PRODUCTORES:
            </label>
          </div>
          <div class="col-xs-4">
            <label>PRODUCCIÓN</label>
            <?php 
            if($solicitud['produccion'] == 1){
              echo '<input type="checkbox" name="produccion" class="form-control" value="1" checked>';
            }else{
              echo '<input type="checkbox" name="produccion" class="form-control" value="1">';
            }
            ?>
          </div>
          <div class="col-xs-4">
            <label>PROCESAMIENTO</label>
            <?php 
            if($solicitud['procesamiento'] == 1){
              echo '<input type="checkbox" name="procesamiento" class="form-control" value="1" checked>';
            }else{
              echo '<input type="checkbox" name="procesamiento" class="form-control" value="1">';
            }
            ?>
          </div>
          <div class="col-xs-4">
            <label>COMERCIALIZACIÓN</label>
            <?php 
            if($solicitud['comercializacion'] == 1){
              echo '<input type="checkbox" name="comercializacion" class="form-control" value="1" checked>';
            }else{
              echo '<input type="checkbox" name="comercializacion" class="form-control" value="1">';
            }
            ?>

          </div>


          <label for="preg2">
            2.  ESPECIFIQUE EL NOMBRE DE LA INSTANCIA QUE LLEVA A CABO LA COMERCIALIZACIÓN, IMPORTACIÓN O EXPORTACIÓN DE LAS TRANSACCIONES SPP.
          </label>
          <input type="text" class="form-control" id="preg2" name="preg2" value="<?php echo $solicitud['preg2']; ?>">


          <label for="preg3">
            3.  ESPECIFIQUE SI SUBCONTRATA LOS SERVICIOS DE PLANTAS DE PROCESAMIENTO, EMPRESAS DE COMERCIALIZACIÓN O EMPRESAS QUE REALICEN LA IMPORTACIÓN O EXPORTACIÓN, SI LA RESPUESTA ES AFIRMATIVA, MENCIONE EL NOMBRE Y EL SERVICIO QUE REALIZA.
          </label>
          <textarea name="preg3" id="preg3" class="form-control"><?php echo $solicitud['preg3']; ?></textarea>

          <label for="preg4">
            4.  ADICIONAL A SUS OFICINAS CENTRALES (DE LA ORGANIZACIÓN FACILITADORA), ESPECIFIQUE CUÁNTOS CENTROS DE ACOPIO, ÁREAS DE PROCESAMIENTO U OFICINAS ADICIONALES TIENE.
          </label>
          <textarea class="form-control" name="preg4" id="" rows="3"><?php echo $solicitud['preg4']; ?></textarea>

          <label for="preg5">
            5.  SI SUBCONTRATA LOS SERVICIOS DE PLANTAS DE PROCESAMIENTO, EMPRESAS DE COMERCIALIZACIÓN O EMPRESAS QUE REALICEN LA IMPORTACIÓN O EXPORTACIÓN, INDIQUE SI ESTAS EMPRESAS VAN A REALIZAR EL REGISTRO BAJO EL PROGRAMA DEL SPP O SERÁN CONTROLADAS A TRAVÉS DE LA ORGANIZACIÓN DE PEQUEÑOS PRODUCTORES. <sup><a data-toggle="tooltip" title="Revisar las Directrices Generales de Sistema SPP" href="#">4</a></sup>
          </label>
          <textarea name="preg5" id="preg5" class="form-control"><?php echo $solicitud['preg5']; ?></textarea>

          <label for="preg6">
            6.  ¿CUENTA CON UN SISTEMA DE CONTROL INTERNO PARA DAR CUMPLIMIENTO A LOS CRITERIOS DE LA NORMA GENERAL DEL SÍMBOLO DE PEQUEÑOS PRODUCTORES?, EN SU CASO, EXPLIQUE.WORKS.
          </label>
          <textarea name="preg6" id="preg6" class="form-control"><?php echo $solicitud['preg6']; ?></textarea>

          <p for="preg7">
            <b>7. DEL TOTAL DE SUS VENTAS ¿QUÉ PORCENTAJE DEL PRODUCTO CUENTA CON LA CERTIFICACIÓN DE ORGÁNICO, COMERCIO JUSTO Y/O SÍMBOLO DE PEQUEÑOS PRODUCTORES?  </b>
            <i>(* Enter only quantity, integer or decimal)</i>
            <div class="col-lg-12">
              <div class="row">
                <div class="col-xs-3">
                  <label for="organico">% ORGANICO</label>
                  <input type="number" step="any" class="form-control" id="organico" name="organico" value="<?php echo $solicitud['organico']; ?>" placeholder="Ej: 0.0">
                </div>
                <div class="col-xs-3">
                  <label for="comercio_justo">% COMERCIO JUSTO</label>
                  <input type="number" step="any" class="form-control" id="comercio_justo" name="comercio_justo" value="<?php echo $solicitud['comercio_justo']; ?>" placeholder="Ej: 0.0">
                </div>
                <div class="col-xs-3">
                  <label for="spp">SPP</label>
                  <input type="number" step="any" class="form-control" id="spp" name="spp" value="<?php echo $solicitud['spp']; ?>" placeholder="Ej: 0.0">
                </div>
                <div class="col-xs-3">
                  <label for="otro">SIN CERTIFICADO</label>
                  <input type="number" step="any" class="form-control" id="otro" name="sin_certificado" value="<?php echo $solicitud['sin_certificado']; ?>" placeholder="Ej: 0.0">
                </div>
              </div>
            </div>
          </p>

  

          <p><b>8. ¿TUVO VENTAS SPP DURANTE EL CICLO DE CERTIFICACIÓN ANTERIOR?</b></p>
            <div class="col-xs-6">
              <?php 
              if($solicitud['preg8'] == 'SI'){
                echo 'SI <input type="radio" class="form-control" name="preg8" onclick="mostrar_ventas()" id="preg8" value="SI" checked>';
              }else{
                echo 'SI <input type="radio" class="form-control" name="preg8" onclick="mostrar_ventas()" id="preg8" value="SI">';
              }
               ?>
            </div>
            <div class="col-xs-6">
              <?php 
              if($solicitud['preg8'] == 'NO'){
                echo 'NO <input type="radio" class="form-control" name="preg8" onclick="mostrar_ventas()" id="preg8" value="NO" checked>';
              }else{
                echo 'NO <input type="radio" class="form-control" name="preg8" onclick="mostrar_ventas()" id="preg8" value="NO">';
              }
               ?>
            </div>      

          <p>
            <b>9. SI SU RESPUESTA FUE POSITIVA, FAVOR DE INIDICAR CON UNA ‘X ‘EL RANGO DEL VALOR TOTAL DE SUS VENTAS SPP  DEL CICLO ANTERIOR DE ACUERDO A LA SIGUIENTE TABLA:</b>
          </p>

          <div class="col-xs-12 ">
                <?php
                  if($solicitud['preg8'] == 'SI'){
                ?>
                  <div class="col-xs-6">
                    <p class='text-center alert alert-success'><span class='glyphicon glyphicon-ok' aria-hidden='true'></span> SI</p>
                  </div>
                  <div class="col-xs-6">
                    <?php 
                      if(empty($solicitud['preg9'])){
                     ?>
                      <p class="alert alert-danger">No se proporciono ninguna respuesta.</p>
                    <?php 
                      }else if($solicitud['preg9'] == "HASTA $3,000 USD"){
                     ?>
                      <p class="alert alert-info">HASTA $3,000 USD</p>
                    <?php 
                      }else if($solicitud['preg9'] == "ENTRE $3,000 Y $10,000 USD"){
                     ?>
                     <p class="alert alert-info">ENTRE $3,000 Y $10,000 USD</p>
                    <?php 
                      }else if($solicitud['preg9'] == "ENTRE $10,000 A $25,000 USD"){
                     ?>
                     <p class="alert alert-info">ENTRE $10,000 A $25,000 USD</p>
                    <?php 
                      }else if($solicitud['preg9'] != "HASTA $3,000 USD" && $solicitud['preg9'] != "ENTRE $3,000 Y $10,000 USD" && $solicitud['preg9'] != "ENTRE $10,000 A $25,000 USD"){
                     ?>
                     <p class="alert alert-info"><?php echo $solicitud['preg9']; ?></p>
                     
                    <?php 
                      }
                     ?>
                  </div>
                <?php
                  }else if($solicitud['preg8'] == 'NO'){
                ?>
                  <div class="col-xs-12">
                    <p class='text-center alert alert-danger'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span> NO</p>
                  </div>
                
                <?php         
                  }
                ?>
          </div>

              
          <label for="preg10">
            10. FECHA ESTIMADA PARA COMENZAR A USAR EL SÍMBOLO DE PEQUEÑOS PRODUCTORES.
          </label>
          <input type="text" class="form-control" id="preg10" name="preg10" value="<?php echo $solicitud['preg10']; ?>">

          <label for="preg11">
            11. ANEXAR EL CROQUIS GENERAL DE SU OPP, INDICANDO LAS ZONAS EN DONDE CUENTA CON SOCIOS.
          </label>
          <input type="file" class="form-control" id="preg11" name="preg11">
        </div>
      </div>

      <!------ FIN INFORMACION DATOS DE OPERACIÓN ------>


      <div class="col-md-12 text-center alert alert-success" style="padding:7px;">DATOS DE PRODUCTOS PARA LOS CUALES QUIERE UTILIZAR EL SÍMBOLO<sup>6</sup></div>
      <div class="col-lg-12">
        <table class="table table-bordered" id="tablaProductos">
          <tr>
            <td>Producto</td>
            <td>Volumen Total Estimado a Comercializar</td>
            <td>Producto Terminado</td>
            <td>Materia Prima</td>
            <td>País(es) de Destino</td>
            <td>Marca Propia</td>
            <td>Marca de un Cliente</td>
            <td>Sin cliente aún</td>
            <td>
              <button type="button" onclick="tablaProductos()" class="btn btn-primary" aria-label="Left Align">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
              </button>
              
            </td>   
          </tr>
          <?php 
          $query_producto_detalle = "SELECT * FROM productos WHERE idsolicitud_colectiva = $idsolicitud_colectiva";
          $producto_detalle = mysql_query($query_producto_detalle, $dspp) or die(mysql_error());
          $contador = 0;
          while($row_producto = mysql_fetch_assoc($producto_detalle)){
          ?>
            <tr>
              <td>
                <input type="text" class="form-control" name="producto_actual[]" id="exampleInputEmail1" placeholder="Producto" value="<?echo $row_producto['producto']?>">
              </td>
              <td>
                <input type="text" class="form-control" name="volumen_actual[]" id="exampleInputEmail1" placeholder="Volumen" value="<?echo $row_producto['volumen']?>">
              </td>
              <td>
                <?php 
                  if($row_producto['terminado'] == 'SI'){
                    echo "SI <input type='radio'  name='terminado_actual".$contador."' value='SI' checked><br>";
                  }else{
                    echo "SI <input type='radio'  name='terminado_actual".$contador."' value='SI'><br>";
                  } 
                  if($row_producto['terminado'] == 'NO'){
                    echo "NO <input type='radio'  name='terminado_actual".$contador."' value='NO' checked>";
                  }else{
                    echo "NO <input type='radio'  name='terminado_actual".$contador."' value='NO'>";
                  }
                 ?>
              </td>          
              <td>
                <input type="text" class="form-control" name="materia_actual[]" id="exampleInputEmail1" placeholder="Materia" value="<?echo $row_producto['materia']?>">
              </td>
              <td>
                <input type="text" class="form-control" name="destino_actual[]" id="exampleInputEmail1" placeholder="Destino" value="<?echo $row_producto['destino']?>">
              </td>
              <td>
                <?php 
                  if($row_producto['marca_propia'] == 'SI'){
                    echo "SI <input type='radio'  name='marca_propia_actual".$contador."' value='SI' checked><br>";
                  }else{
                    echo "SI <input type='radio'  name='marca_propia_actual".$contador."' value='SI'><br>";
                  } 
                  if($row_producto['marca_propia'] == 'NO'){
                    echo "NO <input type='radio'  name='marca_propia_actual".$contador."' value='NO' checked>";
                  }else{
                    echo "NO <input type='radio'  name='marca_propia_actual".$contador."' value='NO'>";
                  }
                 ?>
              </td>
              <td>
                <?php 
                  if($row_producto['marca_cliente'] == 'SI'){
                    echo "SI <input type='radio'  name='marca_cliente_actual".$contador."' value='SI' checked><br>";
                  }else{
                    echo "SI <input type='radio'  name='marca_cliente_actual".$contador."' value='SI'><br>";
                  } 
                  if($row_producto['marca_cliente'] == 'NO'){
                    echo "NO <input type='radio'  name='marca_cliente_actual".$contador."' value='NO' checked>";
                  }else{
                    echo "NO <input type='radio'  name='marca_cliente_actual".$contador."' value='NO'>";                  
                  }
                 ?>              
              </td>
              <td>
                <?php 
                  if($row_producto['sin_cliente'] == 'SI'){
                    echo "SI <input type='radio'  name='sin_cliente_actual".$contador."' value='SI' checked><br>";
                  }else{
                    echo "SI <input type='radio'  name='sin_cliente_actual".$contador."' value='SI'><br>";
                  }
                  if($row_producto['sin_cliente'] == 'NO'){
                    echo "NO <input type='radio'  name='sin_cliente_actual".$contador."' value='NO' checked>";
                  }else{
                    echo "NO <input type='radio'  name='sin_cliente_actual".$contador."' value='NO'>";
                  }
                 ?> 
              </td>
                <input type="hidden" name="idproducto[]" value="<?echo $row_producto['idproducto']?>">                     
            </tr>
          <?php 
          $contador++;
          }
          ?>        
          <tr>
            <td colspan="8">
              <h6><sup>6</sup> La información proporcionada en esta sección será tratada con plena confidencialidad. Favor de insertar filas adicionales de ser necesario.</h6>
            </td>
          </tr>
        </table>
      </div>
      <div class="col-lg-12 text-center alert alert-success" style="padding:7px;">
        <b>COMPROMISOS</b>
      </div>
      <div class="col-lg-12 text-justify">
        <p>1. Con el envío de esta solicitud se manifiesta el interés de recibir una propuesta de Certificación.</p> 
        <p>2. El proceso de Certificación comenzará en el momento que se confirme la recepción del pago correspondiente.</p>
        <p>3. La entrega y recepción de esta solicitud no garantiza que el proceso de Certificación será positivo.</p>
        <p>4. Conocer y dar cumplimiento a todos los requisitos de la Norma General del Símbolo de Pequeños Productores que le apliquen como Organización de Pequeños Productores, tanto Críticos como Mínimos, independientemente del tipo de evaluación que se realice.</p>
      </div>
      <div class="col-lg-12">
        <label for="responsable">
          <p style="font-size:14px;"><strong>Nombre de la persona que se responsabiliza de la veracidad de la información del formato y que le dará seguimiento a la solicitud de parte del solicitante:</strong></p>
        </label>
        <input type="text" class="form-control" id="responsable" value="<?php echo $solicitud['responsable']; ?>" > 
        <input type="hidden" name="fecha_registro" value="<?php echo $solicitud['fecha_registro'] ?>">
        <input type="hidden" name="idopp" value="<?php echo $solicitud['idopp']; ?>">

        <p>
          <b>OC que recibe la solicitud:</b>
        </p>
        <p class="alert alert-info" style="padding:7px;">
          <strong><?php echo $solicitud['abreviacionOC']; ?></strong>
        </p>  
      </div>
      <!--<div class="col-xs-12">
        <hr>
        <input type="text" name="insertar_solicitud" value="1">
        <input type="submit" class="btn btn-primary form-control" value="Enviar Solicitud" onclick="return validar()">
      </div>-->

    </fieldset>
  </form>
</div>
<!-- inicia modal estatus_Certificado -->

<div id="<?php echo "observaciones".$_GET['IDsolicitud']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form action="" method="POST">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title" id="myModalLabel">Agregar Observaciones sobre la Solicitud</h4>
        </div>
        <div class="modal-body">
          <textarea name="observaciones_solicitud" id="" class="textareaMensaje" cols="30" rows="10"></textarea>
        </div>

        <div class="modal-footer">
          <input type="hidden" name="tipo_solicitud" value="<?php echo $solicitud['tipo_solicitud']; ?>">
          <input type="hidden" name="idsolicitud_colectiva" value="<?php echo $_GET['IDsolicitud']; ?>">
          <input type="hidden" name="agregar_observaciones" value="1">
          <button type="submit" class="btn btn-success">Enviar Observaciones</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- termina modal estatus_Certificado -->

<script>

var contador=0;
var contador2=0;
  function tabla_organizaciones()
  {


    var table = document.getElementById("tabla_organizaciones");
    {
      var row = table.insertRow(2);
      var cell1 = row.insertCell(0);
      var cell2 = row.insertCell(1);
      var cell3 = row.insertCell(2);
      var cell4 = row.insertCell(3);
      var cell5 = row.insertCell(4);
      var cell6 = row.insertCell(5);
      var cell7 = row.insertCell(6);
      var cell8 = row.insertCell(7);
      var cell9 = row.insertCell(8);
      var cell10 = row.insertCell(9);
      var cell11 = row.insertCell(10);

      cell1.innerHTML = ''+contador2+'';
      cell2.innerHTML = '<input type="text" class="form-control" style="width:150px;" name="sub_nombre_nuevo['+contador+']" id="" placeholder="Nombre Completo">';
      cell3.innerHTML = 'Generated by the system';
      cell4.innerHTML = '<input type="text" class="form-control" style="width:120px;" name="num_productores_nuevo['+contador+']" id="" placeholder="Solo numeros">';
      cell5.innerHTML = '<textarea class="form-control" name="sub_producto_nuevo['+contador+']" id="" rows="3" placeholder="Productos"></textarea>';
      cell6.innerHTML = '<input type="text" class="form-control" name="unidad_produccion_nuevo['+contador+']" id="" placeholder="Unidad de producción">';
      cell7.innerHTML = '<textarea class="form-control" style="width:150px;" name="sub_certificaciones_nuevo['+contador+']" placeholder="Certificacion(es)" rows="3" required></textarea>';
      cell8.innerHTML = '<textarea class="form-control" style="width:150px;" name="sub_certificadora_nuevo['+contador+']" placeholder="Certificadora" rows="3" required></textarea>';
      cell9.innerHTML = '<textarea class="form-control" style="width:150px;" name="sub_anio_certificacion_nuevo['+contador+']" placeholder="Año inicial" rows="3" required></textarea>';
      cell10.innerHTML = 'SI <input type="radio" name="sub_interrumpido_nuevo['+contador+']" id="" value="SI"><br>NO <input type="radio" name="sub_interrumpido_nuevo['+contador+']" id="" value="NO">';
      cell11.innerHTML = '<textarea class="form-control" name="sub_incumplimientos_nuevo['+contador+']" id="" rows="3"></textarea>';
    }
    contador++;
    contador2++;
    
  }


  function validar(){
    valor = document.getElementById("cotizacion_opp").value;
    if( valor == null || valor.length == 0 ) {
      alert("No se ha cargado la cotización de el OPP");
      return false;
    }
    
    Procedimiento = document.getElementsByName("procedimiento");
     
    var seleccionado = false;
    for(var i=0; i<Procedimiento.length; i++) {    
      if(Procedimiento[i].checked) {
        seleccionado = true;
        break;
      }
    }
     
    if(!seleccionado) {
      alert("Debes de seleecionar un Procedimiento de Certificación");
      return false;
    }

    return true
  }

</script>

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
    cell3.innerHTML = '<input type="text" class="form-control" name="ano_inicial['+contador+']" id="exampleInputEmail1" placeholder="AÑO INICIAL">';
    cell4.innerHTML = '<div class="col-xs-6">SI<input type="radio" class="form-control" name="interrumpida['+contador+']" value="SI"></div><div class="col-xs-6">NO<input type="radio" class="form-control" name="interrumpida['+contador+']" value="NO"></div>';
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
    var cell7 = row.insertCell(6); 
    var cell8 = row.insertCell(7);        

    

    cell1.innerHTML = '<input type="text" class="form-control" name="producto['+cont+']" id="exampleInputEmail1" placeholder="Producto">';
    
    cell2.innerHTML = '<input type="text" class="form-control" name="volumen['+cont+']" id="exampleInputEmail1" placeholder="Volumen">';
    
    cell3.innerHTML = 'SI <input type="radio" name="terminado'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="terminado'+cont+'['+cont+']" id="" value="NO">';
    
    cell4.innerHTML = '<input type="text" class="form-control" name="materia['+cont+']" id="exampleInputEmail1" placeholder="Materia">';
    
    cell5.innerHTML = '<input type="text" class="form-control" name="destino['+cont+']" id="exampleInputEmail1" placeholder="Destino">';
    
    cell6.innerHTML = 'SI <input type="radio" name="marca_propia'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="marca_propia'+cont+'['+cont+']" id="" value="NO">';
    
    cell7.innerHTML = 'SI <input type="radio" name="marca_cliente'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="marca_cliente'+cont+'['+cont+']" id="" value="NO">';
    
    cell8.innerHTML = 'SI <input type="radio" name="sin_cliente'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="sin_cliente'+cont+'['+cont+']" id="" value="NO">';  

    cont++;
    }

  } 

</script>