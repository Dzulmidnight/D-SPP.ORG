<?php 
require_once('../Connections/dspp.php'); 
require_once('../Connections/mail.php');

mysql_select_db($database_dspp, $dspp);

$fecha = time();

if (!isset($_SESSION)) {
  session_start();
	
	$redireccion = "../index.php?OPP";

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

$idsolicitud_colectiva = $_GET['idsolicitud_colectiva'];
$charset='utf-8'; 


if(isset($_POST['actualizar_solicitud']) && $_POST['actualizar_solicitud'] == 1){
$ruta_croquis = "../../archivos/oppArchivos/croquis/";
	/*
	SE ACTUALIZA LA SOLICITUD
	LA INFORMACION DE OPP
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




  $mensaje = "Correctly Updated Data";
}
 

$query = "SELECT solicitud_colectiva.*, opp.idopp AS 'id_opp', opp.nombre, opp.spp AS 'spp_opp', opp.sitio_web, opp.email, opp.telefono, opp.pais, opp.ciudad, opp.razon_social, opp.direccion_oficina, opp.direccion_fiscal, opp.rfc, opp.ruc, oc.abreviacion AS 'abreviacionOC', porcentaje_productoVentas.* FROM solicitud_colectiva INNER JOIN opp ON solicitud_colectiva.idopp = opp.idopp INNER JOIN oc ON solicitud_colectiva.idoc = oc.idoc LEFT JOIN porcentaje_productoVentas ON solicitud_colectiva.idsolicitud_colectiva = porcentaje_productoVentas.idsolicitud_colectiva WHERE solicitud_colectiva.idsolicitud_colectiva = $idsolicitud_colectiva";
$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
$solicitud = mysql_fetch_assoc($ejecutar);

$row_pais = mysql_query("SELECT * FROM paises", $dspp) or die(mysql_error());
?>
<div class="row" style="font-size:12px;">

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

	<form action="" name="" method="POST" enctype="multipart/form-data">
		<fieldset>
			<div class="col-md-12 alert alert-primary" style="padding:7px;">
				<h3 class="text-center">Application for Collective Certification SPO</h3>
			</div>
			<div class="col-md-12 well text-justify">
				<p class="text-center" style="color:red"><b>Important Information about the Guidelines for the Collective Certification:</b></p>
				<p><b>Scope:</b></p>
				<p>
					1. The Collective Certification Procedures apply to first-level Organizations that are members of a higher level Small Producers’ Organization that applies for Certification, based on the General Standard for the Small Producers’ Symbol, through the Small Producers’ Organization of a higher level.
				</p>  

				<p>
					2. The high-level Small Producers’ Organization does not acquire certification. If the high-level Small Producers’ Organization is the organization that is commercializing products under the Small Producers’ Symbol, it should become registered as an Intermediary (INT) or Collective Trading Company owned by Small Producers’ Organizations (C-OPP).
				</p>      

				<p>
					<b>Requirements:</b><br>
					i. The high-level Small Producers’ Organization (SPO) should work to facilitate and promote the certification process for its members and should provide all the necessary information based on their internal control system.
				</p>
				<p>
					ii.  The high-level SPO should complete the SPP Evaluation Form as a form of self-assessment in line with the information from each of the first-level SPOs involved.
				</p>
				<p>
					iii. The high-level SPO should send the documentation specified in the Evaluation Form as support documentation, as well as the information requested by the Certification Entity (CE).
				</p> 
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
		      <div class="col-md-12 text-center alert alert-success" style="padding:7px;"><b>FACILITATING ORGANIZATION( <a data-toggle="tooltip" title="Facilitating SPO: Second or higher-level Small Producers´ Organization representing its member organizations in their collective certification process" href="#"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span></a> ) GENERAL INFORMATION ( <a data-toggle="tooltip" title="General information corresponding to the Facilitating Small Producers´ Organization submitting the application will be published by SPP Global" href="#"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span></a> )</b></div>
		      <!------ INICIA INFORMACION GENERAL Y DATOS FISCALES ------>
		      <div class="col-lg-12">
		        <div class="col-md-6">
		          <div class="col-md-12 text-center alert alert-warning" style="padding:7px;">GENERAL INFORMATION</div>
		          <label for="fecha_elaboracion">DATE OF ELABORATION</label>
		          <input type="text" class="form-control" id="fecha_elaboracion" name="fecha_elaboracion" value="<?php echo date('Y-m-d', time()); ?>" readonly>  

		          <label for="spp">SPP IDENTIFICATION CODE(#SPP):</label>
		          <input type="text" class="form-control" id="spp" name="spp" value="<?php echo $solicitud['spp_opp']; ?>" readonly>

		          <label for="nombre_facilitador" style="color:red">COMPLETE NAME OF THE FACILITATING ORGANIZATION</label>
		          <textarea name="nombre_facilitador" id="nombre_facilitador" class="form-control"><?php echo $solicitud['nombre']; ?></textarea>


		          <label for="pais">COUNTRY:</label>
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

		          <label for="direccion_oficina">COMPLETE ADDRESS FOR THE FACILITATING ORGANIZATION (STREET, DISTRICT, TOWN/CITY, REGION):</label>
		          <textarea name="direccion_oficina" id="direccion_oficina"  class="form-control"><?php echo $solicitud['direccion_oficina']; ?></textarea>

		          <label for="email">ORGANIZATION´S EMAIL ADDRESS:</label>
		          <input type="text" class="form-control" id="email" name="email" value="<?php echo $solicitud['email']; ?>">

		          <label for="email">ORGANIZATION’S TELEPHONES(COUNTRY CODE+AREA CODE+NUMBER):</label>
		          <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo $solicitud['telefono']; ?>">  

		          <label for="sitio_web">WEBSITE:</label>
		          <input type="text" class="form-control" id="sitio_web" name="sitio_web" value="<?php echo $solicitud['sitio_web']; ?>">

		        </div>

		        <div class="col-md-6">
		          <div class="col-md-12 text-center alert alert-warning" style="padding:7px;">DATA FOR INVOICING</div>

		          <label for="razon_social">BUSINESS NAME</label>
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
		          <input type="text" class="form-control" id="persona1" name="contacto1_nombre" value="<?php echo $solicitud['contacto1_nombre']; ?>" placeholder="* Nombre persona 1" required>
		          <input type="text" class="form-control" id="" name="contacto2_nombre" value="<?php echo $solicitud['contacto2_nombre']; ?>" placeholder="Name Person 2">

		          <label for="cargo">POSITION(S)</label>
		          <input type="text" class="form-control" id="cargo" name="contacto1_cargo" value="<?php echo $solicitud['contacto1_cargo']; ?>" placeholder="* Cargo persona 1" required>
		          <input type="text" class="form-control" id="" name="contacto2_cargo" value="<?php echo $solicitud['contacto2_cargo']; ?>" placeholder="Position Person 2">

		          <label for="email">EMAIL ADDRESS FROM THE CONTACT PERSON(S)</label>
		          <input type="email" class="form-control" id="email" name="contacto1_email" value="<?php echo $solicitud['contacto1_email']; ?>" placeholder="* Email persona 1" required>
		          <input type="email" class="form-control" id="" name="contacto2_email" value="<?php echo $solicitud['contacto2_email']; ?>" placeholder="Email Person 2">

		          <label for="telefono">TELEPHONE(S) FOR CONTACT PERSON(S)</label>
		          <input type="text" class="form-control" id="telefono" name="contacto1_telefono" value="<?php echo $solicitud['contacto1_telefono']; ?>" placeholder="* Teléfono persona 1" required>
		          <input type="text" class="form-control" id="" name="contacto2_telefono" value="<?php echo $solicitud['contacto2_telefono']; ?>" placeholder="Teléfono persona 2">

		        </div>

		        <div class="col-md-6">
		          <div class="col-md-12 text-center alert alert-warning" style="padding:7px;">PERSON(S) OF THE ADMINISTRATIVE AREA</div>

		          <label for="persona_adm">PERSON(S) OF THE ADMINISTRATIVE AREA</label>
		          <input type="text" class="form-control" id="persona_adm" name="adm1_nombre" value="<?php echo $solicitud['adm1_nombre']; ?>" placeholder="Nombre persona 1">
		          <input type="text" class="form-control" id="" name="adm2_nombre" value="<?php echo $solicitud['adm2_nombre']; ?>" placeholder="Nombre persona 2">

		          <label for="email_adm">EMAIL</label>
		          <input type="email" class="form-control" id="email_adm" name="adm1_email" value="<?php echo $solicitud['adm1_email']; ?>" placeholder="Email persona 1">
		          <input type="email" class="form-control" id="" name="adm2_email" value="<?php echo $solicitud['adm2_email']; ?>" placeholder="Email persona 2">

		          <label for="telefono_adm">TELEPHONE(S) PERSON(S) ADMINISTRATIVE AREA</label>
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

		              <th rowspan="2">Complete Name</th>
		              <th rowspan="2">SPP Identification Code</th>
		              <th rowspan="2">Number of Producer Members</th>
		              <th rowspan="2">Name of products to be included in the collective certification</th>
		              <th rowspan="2">Maximum Size of the Unit Production <span style="background:yellow">by Producer</span> of the product(s) to include in the collective certification (unit of measure):</th>
		              <th colspan="4">Fill out the table according your certifications, (example: EU, NOP, JAS, FLO, etc.)</th>

		              <th rowspan="2">According the certifications, in its most recent internal and external evaluations, how many cases of non compliance were identified? Please explain if they have been resolved or what their status is?</th>

		            </tr>
		            <tr>
		              <th>Certification</th>
		              <th>Certification Entity</th>
		              <th>Initial Year of Certification</th>
		              <th>Has been interrupted?</th>
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
		                  <input type="text" class="form-control" style="width:150px;" name="sub_nombre[]" value="<?php echo $row_sub_organizaciones['nombre']; ?>" placeholder="Complete Name">
		                </td>
		                <td>
		                  <?php echo $row_sub_organizaciones['spp']; ?>
		                </td>
		                <td>
		                  <input type="number" class="form-control" style="width:120px;" name="num_productores[]" value="<?php echo $row_sub_organizaciones['num_productores']; ?>" placeholder="Only numbers">
		                </td>
		                <td>
		                  <textarea class="form-control" style="width:200px;" name="sub_producto[]" id="" rows="3" placeholder="Products"><?php echo $row_sub_organizaciones['productos']; ?></textarea>
		                </td>
		                <td>
		                  <input type="text" class="form-control" style="width:150px;" name="unidad_produccion[]" value="<?php echo $row_sub_organizaciones['unidad_produccion']; ?>" placeholder="Unit production">
		                </td>
		                <td>
		                  <textarea class="form-control" style="width:150px;" name="sub_certificaciones[]" placeholder="Certification" rows="3" required><?php echo $row_sub_organizaciones['certificaciones']; ?></textarea>
		                </td>
		                <td>
		                  <textarea class="form-control" style="width:150px;" name="sub_certificadora[]" placeholder="Certification Entity" rows="3" required><?php echo $row_sub_organizaciones['certificadora']; ?></textarea>
		                </td>
		                <td>
		                  <textarea class="form-control" style="width:150px;" name="sub_anio_certificacion[]"  placeholder="Initial year" rows="3" required><?php echo $row_sub_organizaciones['anio_inicial']; ?></textarea>
		                </td>
		                <td>
		                    <?php 
		                      if($row_sub_organizaciones['interrumpida'] == 'SI'){
		                        echo "YES <input type='radio'  name='sub_interrumpido".$contador."' value='SI' checked><br>";
		                      }else{
		                        echo "YES <input type='radio'  name='sub_interrumpido".$contador."' value='SI'><br>";
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
		                <h6>Information provided in this section will be handled with complete confidentiality. Please insert additional lines if necessary.</h6>
		              </td>
		            </tr>
		          </tbody>
		        </table>
		      </div>
		      <!--- TERMINA TABLA SOBRE LA INFORMACION DE LAS ORGANIZACIONES INVOLUCRADAS -->

		      <!------ INICIA INFORMACION DATOS DE OPERACIÓN ------>
		    
		      <div class="col-lg-6" style="background:#2ecc71">
		          <label for="total_miembros">TOTAL MEMBERS INCLUDED IN THE COLLECTIVE CERTIFICATION:</label>
		          <input type="number" class="form-control" id="total_miembros" name="total_miembros" value="<?php echo $solicitud['total_miembros']; ?>" placeholder="just number" required>
		      </div>

		      <div class="col-md-12 text-center alert alert-success" style="margin-top:5em;">INFORMATION ON OPERATION FOR SPP TRANSACTIONS</div>

		      <div class="col-lg-12">
		        <div class="col-md-12">

		          <div >
		            <label for="alcance_opp">
		              1. MARK WITH AN 'X' THE SCOPE OF THE SMALL PRODUCERS` ORGANIZATIONS INCLUDED IN THE COLLECTIVE CERTIFICATION:
		            </label>
		          </div>
		          <div class="col-xs-4">
		            <label>PRODUCTION</label>
		            <?php 
		            if($solicitud['produccion'] == 1){
		              echo '<input type="checkbox" name="produccion" class="form-control" value="1" checked>';
		            }else{
		              echo '<input type="checkbox" name="produccion" class="form-control" value="1">';
		            }
		            ?>
		          </div>
		          <div class="col-xs-4">
		            <label>PROCESSING</label>
		            <?php 
		            if($solicitud['procesamiento'] == 1){
		              echo '<input type="checkbox" name="procesamiento" class="form-control" value="1" checked>';
		            }else{
		              echo '<input type="checkbox" name="procesamiento" class="form-control" value="1">';
		            }
		            ?>
		          </div>
		          <div class="col-xs-4">
		            <label>TRAIDING</label>
		            <?php 
		            if($solicitud['comercializacion'] == 1){
		              echo '<input type="checkbox" name="comercializacion" class="form-control" value="1" checked>';
		            }else{
		              echo '<input type="checkbox" name="comercializacion" class="form-control" value="1">';
		            }
		            ?>

		          </div>


		          <label for="preg2">
		            2. SPECIFY THE NAME OF THE ENTITY THAT CARRY OUT THE IMPORT OR EXPORT FOR SPP TRANSACTIONS.
		          </label>
		          <input type="text" class="form-control" id="preg2" name="preg2" value="<?php echo $solicitud['preg2']; ?>">


		          <label for="preg3">
		            3.  SPECIFY IF YOU SUBCONTRACT THE SERVICES OF PROCESSING PLANTS, TRADING COMPANIES OR COMPANIES THAT CARRY OUT THE IMPORT OR EXPORT, IF THE ANSWER IS AFFIRMATIVE, MENTION THE NAME AND THE SERVICE THAT PERFORMS.
		          </label>
		          <textarea name="preg3" id="preg3" class="form-control"><?php echo $solicitud['preg3']; ?></textarea>

		          <label for="preg4">
		            4.  IN ADDITION TO THE MAIN OFFICES (OF THE FACILITATING SPO), PLEASE SPECIFY HOW MANY COLLECTION CENTERS, PROCESSING AREAS AND ADDITIONAL OFFICES YOU HAVE.
		          </label>
		          <textarea class="form-control" name="preg4" id="" rows="3"><?php echo $solicitud['preg4']; ?></textarea>

		          <label for="preg5">
		            5.  IF YOU SUBCONTRACT THE SERVICES OF PROCESSING PLANTS, TRADING COMPANIES OR COMPANIES THAT CARRY OUT THE IMPORT OR EXPORT, INDICATE WHETHER THESE COMPANIES ARE GOING TO APPLY FOR THE REGISTRATION UNDER SPP CERTIFICATION PROGRAM. <sup><a data-toggle="tooltip" title="Review the General Application Guidelines to the SPP System" href="#">4</a></sup>
		          </label>
		          <textarea name="preg5" id="preg5" class="form-control"><?php echo $solicitud['preg5']; ?></textarea>

		          <label for="preg6">
		            6.  IF THE ORGANIZATION HAS AN INTERNAL CONTROL SYSTEM FOR COMPLYING WITH THE CRITERIA IN THE GENERAL STANDARD OF THE SMALL PRODUCERS´ SYMBOL, PLEASE EXPLAIN HOW IT WORKS.
		          </label>
		          <textarea name="preg6" id="preg6" class="form-control"><?php echo $solicitud['preg6']; ?></textarea>

		          <p for="preg7">
		            <b>7. OF THE APPLICANT’S TOTAL TRADING DURING THE PREVIOUS CYCLE, WHAT PERCENTAGE WAS CONDUCTED UNDER THE SCHEMES OF CERTIFICATION FOR ORGANIC, FAIR TRADE AND/OR THE SMALL PRODUCERS’ SYMBOL?</b>
		            <i>(* Enter only quantity, integer or decimal)</i>
		            <div class="col-lg-12">
		              <div class="row">
		                <div class="col-xs-3">
		                  <label for="organico">% ORGANIC</label>
		                  <input type="number" step="any" class="form-control" id="organico" name="organico" value="<?php echo $solicitud['organico']; ?>" placeholder="Ej: 0.0">
		                </div>
		                <div class="col-xs-3">
		                  <label for="comercio_justo">%  FAIR TRADE</label>
		                  <input type="number" step="any" class="form-control" id="comercio_justo" name="comercio_justo" value="<?php echo $solicitud['comercio_justo']; ?>" placeholder="Ej: 0.0">
		                </div>
		                <div class="col-xs-3">
		                  <label for="spp">SMALL PRODUCERS' SYMBOL</label>
		                  <input type="number" step="any" class="form-control" id="spp" name="spp" value="<?php echo $solicitud['spp']; ?>" placeholder="Ej: 0.0">
		                </div>
		                <div class="col-xs-3">
		                  <label for="otro">WITHOUT CERTIFICATE</label>
		                  <input type="number" step="any" class="form-control" id="otro" name="sin_certificado" value="<?php echo $solicitud['sin_certificado']; ?>" placeholder="Ej: 0.0">
		                </div>
		              </div>
		            </div>
		          </p>

		  

		          <p><b>8. DID YOU HAVE SPP PURCHASES DURING THE PREVIOUS CERTIFICATION CYCLE?</b></p>
		            <div class="col-xs-6">
		              <?php 
		              if($solicitud['preg8'] == 'SI'){
		                echo 'YES <input type="radio" class="form-control" name="preg8" onclick="mostrar_ventas()" id="preg8" value="SI" checked>';
		              }else{
		                echo 'YES <input type="radio" class="form-control" name="preg8" onclick="mostrar_ventas()" id="preg8" value="SI">';
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
		            <b>9. IF YOUR RESPONSE WAS POSITIVE, PLEASE MARK THE RANGE OF THE TOTAL VALUE SPP FROM THE PREVIOUS CYCLE ACCORDING TO THE FOLLOWING TABLE:</b>
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
		                      <p class="alert alert-danger">No response was provided.</p>
		                    <?php 
		                      }else if($solicitud['preg9'] == "HASTA $3,000 USD"){
		                     ?>
		                      <p class="alert alert-info">LESS THAN $3,000 USD</p>
		                    <?php 
		                      }else if($solicitud['preg9'] == "ENTRE $3,000 Y $10,000 USD"){
		                     ?>
		                     <p class="alert alert-info">BETWEENN $3,000 AND $10,000 USD</p>
		                    <?php 
		                      }else if($solicitud['preg9'] == "ENTRE $10,000 A $25,000 USD"){
		                     ?>
		                     <p class="alert alert-info">BEETWENN $10,000 AND $25,000 USD</p>
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
		            10. ESTIMATED DATE FOR BEGINNING TO USE THE SMALL PRODUCERS’ SYMBOL:  		          
              </label>
		          <input type="text" class="form-control" id="preg10" name="preg10" value="<?php echo $solicitud['preg10']; ?>">

		          <label for="preg11">
		            11. PLEASE ATTACH A GENERAL MAP OF THE AREA WHERE YOUR SPO OPERATES, INDICATING THE ZONES WHERE MEMBERS ARE LOCATED.
		          </label>
		          <input type="file" class="form-control" id="preg11" name="preg11">
		        </div>
		      </div>

		      <!------ FIN INFORMACION DATOS DE OPERACIÓN ------>


		      <div class="col-md-12 text-center alert alert-success" style="padding:7px;">INFORMATION ON PRODUCTS FOR WHICH APPLICATION WISHES TO USE SYMBOL<sup>6</sup></div>
		      <div class="col-lg-12">
		        <table class="table table-bordered" id="tablaProductos">
		          <tr>
		            <td>Product</td>
		            <td>Total Estimated Volume to be Traded </td>
		            <td>Finished Product</td>
		            <td>Raw material</td>
		            <td>Destination Countries</td>
		            <td>Own brand</td>
		            <td>Client´s brand</td>
		            <td>Still without client</td>
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
		                <input type="text" class="form-control" name="producto_actual[]" id="exampleInputEmail1" placeholder="Product" value="<?echo $row_producto['producto']?>">
		              </td>
		              <td>
		                <input type="text" class="form-control" name="volumen_actual[]" id="exampleInputEmail1" placeholder="Volumen" value="<?echo $row_producto['volumen']?>">
		              </td>
		              <td>
		                <?php 
		                  if($row_producto['terminado'] == 'SI'){
		                    echo "YES <input type='radio'  name='terminado_actual".$contador."' value='SI' checked><br>";
		                  }else{
		                    echo "YES <input type='radio'  name='terminado_actual".$contador."' value='SI'><br>";
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
		                    echo "YES <input type='radio'  name='marca_propia_actual".$contador."' value='SI' checked><br>";
		                  }else{
		                    echo "YES <input type='radio'  name='marca_propia_actual".$contador."' value='SI'><br>";
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
		                    echo "YES <input type='radio'  name='marca_cliente_actual".$contador."' value='SI' checked><br>";
		                  }else{
		                    echo "YES <input type='radio'  name='marca_cliente_actual".$contador."' value='SI'><br>";
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
		                    echo "YES <input type='radio'  name='sin_cliente_actual".$contador."' value='SI' checked><br>";
		                  }else{
		                    echo "YES <input type='radio'  name='sin_cliente_actual".$contador."' value='SI'><br>";
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
		              <h6><sup>6</sup> Information provided in this section will be handled with complete confidentiality. Please insert additional lines if necessary.</h6>
		            </td>
		          </tr>
		        </table>
		      </div>

			<div class="col-lg-12 text-center alert alert-success" style="padding:7px;">
				<b>COMMITMENTS</b>
			</div>
			<div class="col-lg-12 text-justify">
				<p>1.  By sending in this document, the applicant expresses its interest in receiving a proposal for certification with the Small Producers’ Symbol.</p>
        <p>2.  The certification process will begin when it is confirmed that the payment corresponding to the proposal has been received.</p>
        <p>3.  The fact that this application is delivered and received does not guarantee that the results of the certification process will be positive.</p>
        <p>4.  The applicant will become familiar with and comply with all the applicable requirements in the General Standard of the Small Producers’ Symbol for a Small Producers’ Organization, including both Critical and Minimum Criteria, and independently of the type of evaluation conducted.</p>

			</div>
			<div class="col-lg-12">

				<p style="font-size:14px;">
					<strong>Name of the person who is responsible for the accuracy of the information on this form, and who, on behalf of the Applicant, will follow up on the application:</strong>
				</p>

				<input type="hidden" name="idopp" value="<?php echo $solicitud['id_opp']; ?>">
				<input type="hidden" name="fecha_registro" value="<?php echo $solicitud['fecha_registro']; ?>">
				<input type="text" class="form-control" id="responsable" value="<?php echo $solicitud['responsable']; ?>" readonly>	

				<p>
					<b>Certification Entity that receives the request:</b>
				</p>
				<p class="alert alert-info" style="padding:7px;">
					<?php echo $solicitud['abreviacionOC']; ?>
				</p>	
			</div>


		</fieldset>
	</form>
</div>


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
      cell2.innerHTML = '<input type="text" class="form-control" style="width:150px;" name="sub_nombre_nuevo['+contador+']" id="" placeholder="Complete Name">';
      cell3.innerHTML = 'Generated by the system';
      cell4.innerHTML = '<input type="text" class="form-control" style="width:120px;" name="num_productores_nuevo['+contador+']" id="" placeholder="Just numbers">';
      cell5.innerHTML = '<textarea class="form-control" name="sub_producto_nuevo['+contador+']" id="" rows="3" placeholder="Products"></textarea>';
      cell6.innerHTML = '<input type="text" class="form-control" name="unidad_produccion_nuevo['+contador+']" id="" placeholder=" Unit Productionn">';
      cell7.innerHTML = '<textarea class="form-control" style="width:150px;" name="sub_certificaciones_nuevo['+contador+']" placeholder="Certifications" rows="3" required></textarea>';
      cell8.innerHTML = '<textarea class="form-control" style="width:150px;" name="sub_certificadora_nuevo['+contador+']" placeholder="Certification Entity" rows="3" required></textarea>';
      cell9.innerHTML = '<textarea class="form-control" style="width:150px;" name="sub_anio_certificacion_nuevo['+contador+']" placeholder="Initial Year" rows="3" required></textarea>';
      cell10.innerHTML = 'YES <input type="radio" name="sub_interrumpido_nuevo['+contador+']" id="" value="SI"><br>NO <input type="radio" name="sub_interrumpido_nuevo['+contador+']" id="" value="NO">';
      cell11.innerHTML = '<textarea class="form-control" name="sub_incumplimientos_nuevo['+contador+']" id="" rows="3"></textarea>';
    }
    contador++;
    contador2++;
    
  }


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
      alert("Debes de seleccionar un Tipo de Solicitud");
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
	  var row = table.insertRow(1);
	  var cell1 = row.insertCell(0);
	  var cell2 = row.insertCell(1);
	  var cell3 = row.insertCell(2);
	  var cell4 = row.insertCell(3);

	  cell1.innerHTML = '<input type="text" class="form-control" name="certificacion['+contador+']" id="exampleInputEmail1" placeholder="CERTIFICATION">';
	  cell2.innerHTML = '<input type="text" class="form-control" name="certificadora['+contador+']" id="exampleInputEmail1" placeholder="CERTIFICATION ENTITY">';
	  cell3.innerHTML = '<input type="text" class="form-control" name="ano_inicial['+contador+']" id="exampleInputEmail1" placeholder="INITIAL YEAR">';
	  cell4.innerHTML = '<div class="col-xs-6">YES<input type="radio" class="form-control" name="interrumpida['+contador+']" value="SI"></div><div class="col-xs-6">NO<input type="radio" class="form-control" name="interrumpida['+contador+']" value="NO"></div>';
	  }
		contador++;
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

	  

	  cell1.innerHTML = '<input type="text" class="form-control" name="producto['+cont+']" id="exampleInputEmail1" placeholder="Product">';
	  
	  cell2.innerHTML = '<input type="text" class="form-control" name="volumen['+cont+']" id="exampleInputEmail1" placeholder="Volume">';
	  
	  cell3.innerHTML = 'YES <input type="radio" name="terminado'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="terminado'+cont+'['+cont+']" id="" value="NO">';
	  
	  cell4.innerHTML = '<input type="text" class="form-control" name="materia['+cont+']" id="exampleInputEmail1" placeholder="Material">';
	  
	  cell5.innerHTML = '<input type="text" class="form-control" name="destino['+cont+']" id="exampleInputEmail1" placeholder="Destination">';
	  
	  cell6.innerHTML = 'YES <input type="radio" name="marca_propia'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="marca_propia'+cont+'['+cont+']" id="" value="NO">';
	  
	  cell7.innerHTML = 'YES <input type="radio" name="marca_cliente'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="marca_cliente'+cont+'['+cont+']" id="" value="NO">';
	  
	  cell8.innerHTML = 'YES <input type="radio" name="sin_cliente'+cont+'['+cont+']" id="" value="SI"><br>NO <input type="radio" name="sin_cliente'+cont+'['+cont+']" id="" value="NO">';	 

	  cont++;
	  }

	}	

</script>