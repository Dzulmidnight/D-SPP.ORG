<?php 
require_once('../Connections/dspp.php'); 

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

if(isset($_POST['opp_delete'])){
  $query=sprintf("delete from opp where idopp = %s",GetSQLValueString($_POST['idopp'], "text"));
  $ejecutar=mysql_query($query,$dspp) or die(mysql_error());
}

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_opp = 40;
$pageNum_opp = 0;
if (isset($_GET['pageNum_opp'])) {
  $pageNum_opp = $_GET['pageNum_opp'];
}
$startRow_opp = $pageNum_opp * $maxRows_opp;

mysql_select_db($database_dspp, $dspp);



$timeActual = time();

  if(isset($_POST['archivar']) && $_POST['archivar'] == 1){

    $miVariable =  $_COOKIE["variable"];
    $token = strtok($miVariable, ",");

     while ($token !== false) 
     {
        $query = "UPDATE opp SET estado = 'ARCHIVADO' WHERE idopp = $token";
        $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
        //echo "$token<br>";
        $token = strtok(",");
     }
  
      echo '<script>borrarTodo();</script>';
      echo '<script>location.href="?OPP&select";</script>';
  }
  if(isset($_POST['eliminar']) && $_POST['eliminar'] == 2){
    $miVariable =  $_COOKIE["variable"];
    $token = strtok($miVariable, ",");

     while ($token !== false) 
     {

        $deteleSQL = sprintf("DELETE FROM opp WHERE idopp = %s", 
          GetSQLValueString($token, "int"));
        $eliminar = mysql_query($deteleSQL, $dspp) or die(mysql_error());

        //$query = "DELETE FROM opp WHERE idopp = $token";
        //$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
        //echo "$token<br>";
        $token = strtok(",");
     }
      echo '<script>borrarTodo();</script>';
      echo '<script>location.href="?OPP&select";</script>';

  }

  if(isset($_POST['actualizacion_opp']) && $_POST['actualizacion_opp'] == 1){/* INICIA BOTON ACTUALIZAR LISTA OPP*/

    $row_opp = mysql_query("SELECT * FROM opp",$dspp) or die(mysql_error());
    $cont = 1;
    $fecha = time();

    while($datos_opp = mysql_fetch_assoc($row_opp)){
      //$nombre = "estatusPagina"+$datos_opp['idopp']+"";

      if(isset($_POST['estatus_opp'.$datos_opp['idopp']])){/*********************************** INICIA ESTATUS_OPP(SITUACIÓN) ******************/
        $estatus_opp = $_POST['estatus_opp'.$datos_opp['idopp']];

        if(!empty($estatus_opp)){
          $updateSQL = sprintf("UPDATE opp SET estatus_opp = %s WHERE idopp = %s",
            GetSQLValueString($estatus_opp, "text"),
            GetSQLValueString($datos_opp['idopp'], "int"));
          $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

          //echo "cont: $cont | id($datos_opp[idopp]): $estatusPagina<br>";
        }      
      }/*********************************** TERMINA ESTATUS_OPP(SITUACIÓN) ****************************************************/

      if(isset($_POST['estatusPagina'.$datos_opp['idopp']])){/*********************************** INICIA ESTATUS PAGINA DEL OPP ******************/
        $estatusPagina = $_POST['estatusPagina'.$datos_opp['idopp']];

        if(!empty($estatusPagina)){
          $query = "UPDATE opp SET estatusPagina = $estatusPagina WHERE idopp = $datos_opp[idopp]";
          $ejecutar = mysql_query($query,$dspp) or die(mysql_error());

          //echo "cont: $cont | id($datos_opp[idopp]): $estatusPagina<br>";
        }      
      }/*********************************** TERMINA ESTATUS PAGINA DEL OPP ****************************************************/


      if(isset($_POST['estatus_interno'.$datos_opp['idopp']])){/*********************************** INICIA ESTATUS INTERNO DEL OPP ******************/
        $estatus_interno = $_POST['estatus_interno'.$datos_opp['idopp']];

        if(!empty($estatus_interno)){
          /*
          ESTATUS PAGINA = 
          1.- EN REVISION
          2.- CERTIFICADA
          3.- REGISTRADA
          4.- CANCELADA
          */
          $estatus_publico = "";
          if($estatus_interno == 10){ //ESTATUS PAGINA = CERTIFICADO(REGISTRADO)
            $estatus_publico = 2;
          }else if($estatus_interno == 14 || $estatus_interno == 24){ // ESTATUS PAGINA = CANCELADO
            $estatus_publico = 4;
          }else{ // ESTATUS PAGINA = EN REVISION
            $estatus_publico = 1;
          }

          $updateSQL = sprintf("UPDATE opp SET estatus_interno = %s, estatus_publico = %s WHERE idopp = %s",
            GetSQLValueString($estatus_interno, "int"),
            GetSQLValueString($estatus_publico, "int"),
            GetSQLValueString($datos_opp['idopp'], "int"));
          $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());
          /*$queryPagina = "UPDATE opp SET estatusPagina = $estatusPagina WHERE idopp = $datos_opp[idOPP]";
          $ejecutar = mysql_query($queryPagina,$dspp) or die(mysql_error());
          //echo "cont: $cont | id($datos_opp[idopp]): $estatusInterno<br>";*/
        }      



      }/*********************************** TERMINA ESTATUS INTERNO DEL OPP ****************************************************/



      if(isset($_POST['estatusPublico'.$datos_opp['idopp']])){/*********************************** INICIA ESTATUS PUBLICO DEL OPP ******************/
        $estatusPublico = $_POST['estatusPublico'.$datos_opp['idopp']];

        if(!empty($estatusPublico)){

          $query = "UPDATE opp SET estatusPublico = $estatusPublico, estatusPublico = $estatusPublico WHERE idopp = $datos_opp[idopp]";
          $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
          /*$queryPagina = "UPDATE opp SET estatusPagina = $estatusPagina WHERE idopp = $datos_opp[idOPP]";
          $ejecutar = mysql_query($queryPagina,$dspp) or die(mysql_error());
          //echo "cont: $cont | id($datos_opp[idopp]): $estatusInterno<br>";*/
        }      



      }/*********************************** TERMINA ESTATUS PUBLICO DEL OPP ****************************************************/


      if(isset($_POST['num_socios'.$datos_opp['idopp']])){/*********************************** INICIA NUMERO DE SOCIOS DEL OPP ******************/
        $num_socios = $_POST['num_socios'.$datos_opp['idopp']];


        if(!empty($num_socios)){
          $row_socios = mysql_query("SELECT idopp, numero FROM num_socios WHERE idopp = ".$datos_opp['idopp']."", $dspp) or die(mysql_error());
          $total = mysql_num_rows($row_socios);

          if($total == 0){
            $insertSQL = sprintf("INSERT INTO num_socios(idopp, numero, fecha_registro) VALUES (%s, %s, %s)",
              GetSQLValueString($datos_opp['idopp'], "int"),
              GetSQLValueString($num_socios, "int"),
              GetSQLValueString($fecha, "int"));
            $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

          }else{
            $updateSQL = sprintf("UPDATE num_socios SET numero = %s, fecha_registro = %s WHERE idopp = %s",
              GetSQLValueString($num_socios, "int"),
              GetSQLValueString($fecha, "int"),
              GetSQLValueString($datos_opp['idopp'], "int"));
            $insertar = mysql_query($updateSQL, $dspp) or die(mysql_error());
          }
        }      
      }/*********************************** TERMINA NUMERO DE SOCIOS DEL OPP ****************************************************/


      if(isset($_POST['spp'.$datos_opp['idopp']])){/*********************************** INICIA NUMERO #SPP DEL OPP ******************/
        $spp = $_POST['spp'.$datos_opp['idopp']];

        if(!empty($spp)){
          $updateSQL = sprintf("UPDATE opp SET spp = %s WHERE idopp = %s",
            GetSQLValueString($spp, "text"),
            GetSQLValueString($datos_opp['idopp'], "int"));
          $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

        }      
      }/*********************************** TERMINA NUMERO #SPP DEL OPP ****************************************************/




      if(isset($_POST['vigencia_fin'.$datos_opp['idopp']])){ /****************** INICIA VIGENCIA FIN DEL CERTIFICADO ******************/
        $vigencia_fin = $_POST['vigencia_fin'.$datos_opp['idopp']];
        $timeActual = time();

        $timeVencimiento = strtotime($vigencia_fin);
        $timeRestante = ($timeVencimiento - $timeActual);
        $estatus_certificado = "";
        $plazo = 60 *(24*60*60);
        $plazoDespues = ($timeVencimiento - $plazo);
        $prorroga = ($timeVencimiento + $plazo);
            // Calculamos el número de segundos que tienen 60 días

        if(!empty($vigencia_fin)){ // NO SE INGRESO NINGUNA FECHA

          $row_certificado = mysql_query("SELECT * FROM certificado WHERE idopp = '$datos_opp[idopp]'", $dspp) or die(mysql_error()); // CONSULTO SI EL OPP CUENTA CON ALGUN REGISTRO DE CERTIFICADO
          $totalCertificado = mysql_num_rows($row_certificado);
          
          if(!empty($totalCertificado)){ // SI CUENTA CON UN REGISTRO, ACTUALIZO EL MISMO
            //$query = "UPDATE certificado SET vigenciafin = '$vigenciafin' WHERE idopp = $datos_opp[idopp]";
            //$ejecutar = mysql_query($query,$dspp) or die(mysql_error());

            /*********************************** INICIA, CALCULAMOS FECHAS PARA ASIGNAR EL ESTATUS DEL CERTIFICADO Y DEL OPP ***********************************************/

            if($timeActual <= $timeVencimiento){
              if($timeRestante <= $plazo){
                $estatus_certificado = 14; //estatus_dspp AVISO DE RENOVACIÓN
                $estatus_publico = 2; //certificado
              }else{
                $estatus_certificado = 13; //estatus_dspp CERTIFICADO ACTIVO
                $estatus_publico = 2; //certificado
              }
            }else{
              if($prorroga >= $timeActual){
                $estatus_certificado = 15; //estatus_dspp CERTIFICADO POR EXPIRAR
                $estatus_publico = 2; //certificado
              }else{
                $estatus_certificado = 16; //estatus_dspp CERTIFICADO EXPIRADO
                $estatus_publico = 1; //en revision
              }
            }

            $updateSQL = sprintf("UPDATE opp SET estatus_publico = %s, estatus_dspp = %s WHERE idopp = %s",
              GetSQLValueString($estatus_publico, "int"),
              GetSQLValueString($estatus_certificado, "int"),
              GetSQLValueString($datos_opp['idopp'], "int"));
            $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

            $updateSQL = sprintf("UPDATE certificado SET estatus_certificado = %s, vigencia_fin = %s WHERE idopp = %s",
              GetSQLValueString($estatus_certificado, "int"),
              GetSQLValueString($vigencia_fin, "text"),
              //GetSQLValueString($idoc, "int"),
              GetSQLValueString($datos_opp['idopp'], "int"));
            $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

            /*$updateSQL = sprintf("UPDATE opp SET estatus_opp = %s, estatus_publico = %s, estatus_dspp = %s WHERE idopp = %s",
              GetSQLValueString($estatus_certificado, "int"),
              GetSQLValueString($estatus_publico, "int"),
              GetSQLValueString($estatus_certificado, "int"),
              GetSQLValueString($datos_opp['idopp'], "int"));
            $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

            $updateSQL = sprintf("UPDATE certificado SET estatus_certificado = %s, vigencia_fin = %s WHERE idopp = %s",
              GetSQLValueString($estatus_certificado, "int"),
              GetSQLValueString($vigencia_fin, "text"),
              //GetSQLValueString($idoc, "int"),
              GetSQLValueString($datos_opp['idopp'], "int"));
            $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());*/


              //$actualizar = "UPDATE certificado SET status = '16' WHERE idcertificado = $datos_opp[idcertificado]";
              //$ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());
            
            /*********************************** FIN, CALCULAMOS FECHAS PARA ASIGNAR EL ESTATUS DEL CERTIFICADO Y DEL OPP ***********************************************/

          }else{ // SI NO CUENTA CON REGISTRO PREVIO, ENTONCES INSERTO UN NUEVO REGISTRO
            //$query = "INSERT INTO certificado(vigenciafin,idopp) VALUES('$vigenciafin',$datos_opp[idopp])";
            //$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
            /*********************************** INICIA, CALCULAMOS FECHAS PARA ASIGNAR EL ESTATUS DEL CERTIFICADO Y DEL OPP ***********************************************/

            if($timeActual <= $timeVencimiento){
              if($timeRestante <= $plazo){
                $estatus_certificado = 14; // AVISO DE RENOVACIÓN
                $estatus_publico = 2; //certificado
              }else{
                $estatus_certificado = 13; // CERTIFICADO ACTIVO
                $estatus_publico = 2; //certificado
              }
            }else{
              if($prorroga >= $timeActual){
                $estatus_certificado = 15; // CERTIFICADO POR EXPIRAR
                $estatus_publico = 2; //certificado
              }else{
                $estatus_certificado = 16; // CERTIFICADO EXPIRADO
                $estatus_publico = 1; //en revision
              }
            }
              $updateSQL = sprintf("UPDATE opp SET estatus_publico = %s, estatus_dspp = %s WHERE idopp = %s",
                GetSQLValueString($estatus_publico, "int"),
                GetSQLValueString($estatus_certificado, "int"),
                GetSQLValueString($datos_opp['idopp'], "int"));
              $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());

              $insertSQL = sprintf("INSERT INTO certificado (idopp, entidad, estatus_certificado, vigencia_fin) VALUES (%s, %s, %s, %s)",
                GetSQLValueString($datos_opp['idopp'], "int"),
                GetSQLValueString($idoc, "int"),
                GetSQLValueString($estatus_certificado, "int"),
                GetSQLValueString($vigencia_fin, "text"));
              $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());
              /*$updateSQL = sprintf("UPDATE opp SET estatus_opp = %s, estatus_publico = %s, estatus_dspp = %s WHERE idopp = %s",
                GetSQLValueString($estatus_certificado, "int"),
                GetSQLValueString($estatus_publico, "int"),
                GetSQLValueString($estatus_certificado, "int"),
                GetSQLValueString($datos_opp['idopp'], "int"));
              $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());

              $insertSQL = sprintf("INSERT INTO certificado (idopp, entidad, estatus_certificado, vigencia_fin) VALUES (%s, %s, %s, %s)",
                GetSQLValueString($datos_opp['idopp'], "int"),
                GetSQLValueString($idoc, "int"),
                GetSQLValueString($estatus_certificado, "int"),
                GetSQLValueString($vigencia_fin, "text"));
              $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());*/

              //$actualizar = "UPDATE certificado SET status = '16' WHERE idcertificado = $datos_opp[idcertificado]";
              //$ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());
            
            /*********************************** FIN, CALCULAMOS FECHAS PARA ASIGNAR EL ESTATUS DEL CERTIFICADO Y DEL OPP ***********************************************/


          }

          //echo "cont: $cont | VIGENCIA FIN($datos_opp[idopp]): $vigenciafin :TOTAL Certificado: $totalCertificado<br>";
        }      
      }/************************************ TERMINA VIGENCIA FIN DEL CERTIFICADO*/


      if(isset($_POST['idoc'.$datos_opp['idopp']])){ //********************************** INICIA LA ASIGNACION DE OC ***********************************/
        $idoc = $_POST['idoc'.$datos_opp['idopp']];
        if(!empty($idoc)){
          $update = "UPDATE opp SET idoc = '$idoc' WHERE idopp = '$datos_opp[idopp]'";
          $ejecutar = mysql_query($update,$dspp) or die(mysql_error());
        }
      } //********************************** TERMINA LA ASIGNACION DE OC ***********************************/




      $cont++;
    }
    
   //echo '<script>location.href="?OPP&select";</script>';
      echo "<script>alert('Se han actualizado los datos');</script>";
  } /* TERMINA BOTON ACTUALIZAR LISTA OPP*/





if(isset($_GET['query'])){

  $query_opp = "SELECT opp.idopp, opp.idoc, opp.spp AS 'spp_opp', opp.nombre, opp.abreviacion AS 'abreviacion_opp', opp.pais, opp.email, opp.sitio_web, opp.telefono, opp.estatus_opp, opp.estatus_publico, opp.estatus_interno, opp.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', num_socios.numero, certificado.idcertificado, certificado.vigencia_fin FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.idoc = $_GET[query] ORDER BY certificado.vigencia_fin DESC";


  $queryExportar = "SELECT opp.*, contacto.*  FROM opp LEFT JOIN contacto ON opp.idopp = contacto.idopp WHERE idoc = $_GET[query] AND (opp.estado IS NULL OR opp.estado != 'ARCHIVADO') ORDER BY opp.idopp ASC";



}else if(isset($_POST['busqueda_palabra']) && $_POST['busqueda_palabra'] == "1"){
  $palabra = $_POST['palabra'];

  $query_opp = "SELECT opp.idopp, opp.idoc, opp.spp AS 'spp_opp', opp.nombre, opp.abreviacion AS 'abreviacion_opp', opp.pais, opp.email, opp.sitio_web, opp.telefono, opp.estatus_opp, opp.estatus_publico, opp.estatus_interno, opp.estatus_dspp, oc.idoc, estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', num_socios.numero, certificado.idcertificado, certificado.vigencia_fin FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.spp LIKE '%$palabra%' OR opp.nombre LIKE '%$palabra%' OR opp.abreviacion LIKE '%$palabra%' ORDER BY opp.idopp DESC";

  $queryExportar = "SELECT opp.*, contacto.*  FROM opp LEFT JOIN contacto ON opp.idopp = contacto.idopp WHERE (opp.estado != 'ARCHIVADO' OR opp.estado IS NULL) AND ((opp.idf LIKE '%$palabra%') OR (opp.nombre LIKE '%$palabra%') OR (opp.abreviacion LIKE '%$palabra%') OR (sitio_web LIKE '%$palabra%') OR (email LIKE '%$palabra%') OR (pais LIKE '%$palabra%') OR (razon_social LIKE '%$palabra%') OR (direccion_fiscal LIKE '%$palabra%') OR (rfc LIKE '%$palabra%')) ORDER BY opp.idopp ASC";



}else if(isset($_POST['busqueda_filtros']) && $_POST['busqueda_filtros'] == 1){
  $idoc = $_POST['buscar_oc'];
  $pais = $_POST['buscar_pais'];
  $estatus = $_POST['buscar_estatus'];
  $producto = $_POST['buscar_producto'];
  $idopp_producto = '';

  if(!empty($idoc) && !empty($pais) && !empty($producto) && !empty($estatus)){


    $query_productos = mysql_query("SELECT opp.idopp, productos.producto FROM opp LEFT JOIN productos ON opp.idopp = productos.idopp WHERE opp.idoc = $idoc AND opp.pais = '$pais' AND opp.estatus_dspp = $estatus AND producto LIKE '%$producto%' GROUP BY idopp", $dspp) or die(mysql_error());
    $total_idopp = mysql_num_rows($query_productos);
    $cont_idopp = 1;
    while($producto_opp = mysql_fetch_assoc($query_productos)){
      $idopp_producto .= "opp.idopp = '$producto_opp[idopp]'";
      if($cont_idopp < $total_idopp){
        $idopp_producto .= " OR ";
      }
      $cont_idopp++;
    }

    $query_opp = "SELECT opp.idopp, opp.idoc, opp.spp AS 'spp_opp', opp.nombre, opp.abreviacion AS 'abreviacion_opp', opp.pais, opp.email, opp.sitio_web, opp.telefono, opp.estatus_opp, opp.estatus_publico, opp.estatus_interno, opp.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', num_socios.numero, certificado.idcertificado, certificado.vigencia_fin FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE $idopp_producto GROUP BY opp.idopp ORDER BY certificado.vigencia_fin DESC";

  }else if(!empty($idoc) && !empty($pais) && !empty($estatus) && empty($producto)){
    $query_opp = "SELECT opp.idopp, opp.idoc, opp.spp AS 'spp_opp', opp.nombre, opp.abreviacion AS 'abreviacion_opp', opp.pais, opp.email, opp.sitio_web, opp.telefono, opp.estatus_opp, opp.estatus_publico, opp.estatus_interno, opp.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', num_socios.numero, certificado.idcertificado, certificado.vigencia_fin FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.idoc = $idoc AND opp.pais = '$pais' AND opp.estatus_dspp = $estatus GROUP BY opp.idopp ORDER BY certificado.vigencia_fin DESC";

  }else if(!empty($idoc) && !empty($pais) && empty($estatus) && empty($producto)){
    $query_opp = "SELECT opp.idopp, opp.idoc, opp.spp AS 'spp_opp', opp.nombre, opp.abreviacion AS 'abreviacion_opp', opp.pais, opp.email, opp.sitio_web, opp.telefono, opp.estatus_opp, opp.estatus_publico, opp.estatus_interno, opp.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', num_socios.numero, certificado.idcertificado, certificado.vigencia_fin FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.idoc = $idoc AND opp.pais = '$pais' GROUP BY opp.idopp ORDER BY certificado.vigencia_fin DESC"; 

  }else if(!empty($idoc) && empty($pais) && empty($estatus) && empty($producto)){
    $query_opp = "SELECT opp.idopp, opp.idoc, opp.spp AS 'spp_opp', opp.nombre, opp.abreviacion AS 'abreviacion_opp', opp.pais, opp.email, opp.sitio_web, opp.telefono, opp.estatus_opp, opp.estatus_publico, opp.estatus_interno, opp.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', num_socios.numero, certificado.idcertificado, certificado.vigencia_fin FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.idoc = $idoc GROUP BY opp.idopp ORDER BY certificado.vigencia_fin DESC";

  }else if(empty($idoc) && !empty($pais) && empty($estatus) && empty($producto)){
    $query_opp = "SELECT opp.idopp, opp.idoc, opp.spp AS 'spp_opp', opp.nombre, opp.abreviacion AS 'abreviacion_opp', opp.pais, opp.email, opp.sitio_web, opp.telefono, opp.estatus_opp, opp.estatus_publico, opp.estatus_interno, opp.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', num_socios.numero, certificado.idcertificado, certificado.vigencia_fin FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.pais = '$pais' GROUP BY opp.idopp ORDER BY certificado.vigencia_fin DESC";

  }else if(empty($idoc) && empty($pais) && empty($estatus) && !empty($producto)){
    $query_productos = mysql_query("SELECT opp.idopp, productos.producto FROM opp LEFT JOIN productos ON opp.idopp = productos.idopp WHERE producto LIKE '%$producto%' GROUP BY idopp", $dspp) or die(mysql_error());
    $total_idopp = mysql_num_rows($query_productos);
    $cont_idopp = 1;
    while($producto_opp = mysql_fetch_assoc($query_productos)){
      $idopp_producto .= "opp.idopp = '$producto_opp[idopp]'";
      if($cont_idopp < $total_idopp){
        $idopp_producto .= " OR ";
      }
      $cont_idopp++;
    }

    $query_opp = "SELECT opp.idopp, opp.idoc, opp.spp AS 'spp_opp', opp.nombre, opp.abreviacion AS 'abreviacion_opp', opp.pais, opp.email, opp.sitio_web, opp.telefono, opp.estatus_opp, opp.estatus_publico, opp.estatus_interno, opp.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', num_socios.numero, certificado.idcertificado, certificado.vigencia_fin FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE $idopp_producto GROUP BY opp.idopp ORDER BY certificado.vigencia_fin DESC";

  }else if(!empty($idoc) && empty($pais) && empty($estatus) && !empty($producto)){
    $query_productos = mysql_query("SELECT opp.idopp, productos.producto FROM opp LEFT JOIN productos ON opp.idopp = productos.idopp WHERE opp.idoc = '$idoc' AND producto LIKE '%$producto%' GROUP BY idopp", $dspp) or die(mysql_error());
    $total_idopp = mysql_num_rows($query_productos);
    $cont_idopp = 1;
    while($producto_opp = mysql_fetch_assoc($query_productos)){
      $idopp_producto .= "opp.idopp = '$producto_opp[idopp]'";
      if($cont_idopp < $total_idopp){
        $idopp_producto .= " OR ";
      }
      $cont_idopp++;
    }

    $query_opp = "SELECT opp.idopp, opp.idoc, opp.spp AS 'spp_opp', opp.nombre, opp.abreviacion AS 'abreviacion_opp', opp.pais, opp.email, opp.sitio_web, opp.telefono, opp.estatus_opp, opp.estatus_publico, opp.estatus_interno, opp.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', num_socios.numero, certificado.idcertificado, certificado.vigencia_fin FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE $idopp_producto GROUP BY opp.idopp ORDER BY certificado.vigencia_fin DESC";

  }else if(empty($idoc) && !empty($pais) && empty($estatus) && !empty($producto)){
    $query_productos = mysql_query("SELECT opp.idopp, productos.producto FROM opp LEFT JOIN productos ON opp.idopp = productos.idopp WHERE opp.pais = '$pais' AND producto LIKE '%$producto%' GROUP BY idopp", $dspp) or die(mysql_error());
    $total_idopp = mysql_num_rows($query_productos);
    $cont_idopp = 1;
    while($producto_opp = mysql_fetch_assoc($query_productos)){
      $idopp_producto .= "opp.idopp = '$producto_opp[idopp]'";
      if($cont_idopp < $total_idopp){
        $idopp_producto .= " OR ";
      }
      $cont_idopp++;
    }

    $query_opp = "SELECT opp.idopp, opp.idoc, opp.spp AS 'spp_opp', opp.nombre, opp.abreviacion AS 'abreviacion_opp', opp.pais, opp.email, opp.sitio_web, opp.telefono, opp.estatus_opp, opp.estatus_publico, opp.estatus_interno, opp.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', num_socios.numero, certificado.idcertificado, certificado.vigencia_fin FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE $idopp_producto GROUP BY opp.idopp ORDER BY certificado.vigencia_fin DESC"; 
  }else if(empty($idoc) && empty($pais) && !empty($estatus) && empty($producto)){
    $query_opp = "SELECT opp.idopp, opp.idoc, opp.spp AS 'spp_opp', opp.nombre, opp.abreviacion AS 'abreviacion_opp', opp.pais, opp.estatus_opp, opp.email, opp.sitio_web, opp.telefono, opp.estatus_publico, opp.estatus_interno, opp.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', num_socios.numero, certificado.idcertificado, certificado.vigencia_fin FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.estatus_dspp = $estatus GROUP BY opp.idopp ORDER BY certificado.vigencia_fin DESC";

  }else{
    $query_opp = "SELECT opp.idopp, opp.idoc, opp.spp AS 'spp_opp', opp.nombre, opp.abreviacion AS 'abreviacion_opp', opp.pais, opp.estatus_opp, opp.email, opp.sitio_web, opp.telefono, opp.estatus_publico, opp.estatus_interno, opp.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', num_socios.numero, certificado.idcertificado, certificado.vigencia_fin FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN certificado ON opp.idopp = certificado.idopp GROUP BY opp.idopp ORDER BY certificado.vigencia_fin DESC";
  }


}else{
  $query_opp = "SELECT opp.idopp, opp.idoc, opp.spp AS 'spp_opp', opp.nombre, opp.abreviacion AS 'abreviacion_opp', opp.pais, opp.email, opp.sitio_web, opp.telefono, opp.estatus_opp, opp.estatus_publico, opp.estatus_interno, opp.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', num_socios.numero, certificado.idcertificado, certificado.vigencia_fin FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN certificado ON opp.idopp = certificado.idopp GROUP BY opp.idopp ORDER BY certificado.vigencia_fin DESC";


  $queryExportar = "SELECT opp.*, contacto.*  FROM opp LEFT JOIN contacto ON opp.idopp = contacto.idopp WHERE (opp.estado IS NULL) OR (opp.estado != 'ARCHIVADO') ORDER BY opp.idopp ASC";

}

$query_limit_opp = sprintf("%s LIMIT %d, %d", $query_opp, $startRow_opp, $maxRows_opp);
$opp = mysql_query($query_limit_opp, $dspp) or die(mysql_error());



if (isset($_GET['totalRows_opp'])) {
  $totalRows_opp = $_GET['totalRows_opp'];
} else {
  $all_opp = mysql_query($query_opp);
  $totalRows_opp = mysql_num_rows($all_opp);
}
$totalPages_opp = ceil($totalRows_opp/$maxRows_opp)-1;

$queryString_opp = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_opp") == false && 
        stristr($param, "totalRows_opp") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_opp = "&" . htmlentities(implode("&", $newParams));
  }
}


$queryString_opp = sprintf("&totalRows_opp=%d%s", $totalRows_opp, $queryString_opp);




$rowOPP = mysql_query("SELECT * FROM opp",$dspp) or die(mysql_error());
$estatus_publico = "";



$detalle_opp = mysql_query($query_opp,$dspp) or die(mysql_error());
$totalOPP = mysql_num_rows($detalle_opp);

$row_interno = mysql_query("SELECT * FROM estatus_interno", $dspp) or die(mysql_error());
$row_oc = mysql_query("SELECT * FROM oc", $dspp) or die(mysql_error());
$row_pais = mysql_query("SELECT * FROM paises", $dspp) or die(mysql_error());
$query_productos = mysql_query("SELECT * FROM productos WHERE productos.idopp IS NOT NULL GROUP BY producto",$dspp) or die(mysql_error());

 ?>


    <form action="" method="POST">
      <div class="col-md-12 alert alert-info">
        <div class="text-center col-md-12">
          <b style="color:#d35400">Seleccione los parametros de los cuales desea realizar la busqueda</b>
        </div> 
        <div class="row">
          <div class="col-xs-3">
            Organismo de Certificación
            <select name="buscar_oc" class="form-control">
              <option value=''>Selecciona un organismo de certificación</option>
              <?php 
              while($oc = mysql_fetch_assoc($row_oc)){
                echo "<option value='$oc[idoc]'>$oc[abreviacion]</option>";
              }
               ?>
            </select>
          </div>
          <div class="col-xs-3">
            País
            <select name="buscar_pais" class="form-control">
              <option value=''>Selecciona un país</option>
              <?php 
              while($pais = mysql_fetch_assoc($row_pais)){
                echo "<option value='".utf8_encode($pais['nombre'])."'>".utf8_encode($pais['nombre'])."</option>";
              }
               ?>
            </select>
          </div>
          <div class="col-xs-3">
            Producto
            <select class="form-control" name="buscar_producto" id="">
              <option value=''>Seleccione un producto</option>
              <?php 
              while($lista_productos = mysql_fetch_assoc($query_productos)){
                echo "<option value='$lista_productos[producto]'>$lista_productos[producto]</option>";
              }
               ?>
            </select>
          </div>
          <div class="col-xs-3">
            Estatus Certificado
            <select class="form-control" name="buscar_estatus" id="">
              <option value=''>Estatus Certificado</option>
              <option value="13">CERTIFICADA</option>
              <option value="14">AVISO DE RENOVACIÓN</option>
              <option value="15">CERTIFICADO POR EXPIRAR</option>
              <option value="16">CERTIFICADO EXPIRADO</option>
            </select>
          </div>

          <div class="col-xs-12">
            <button type="submit" class="btn btn-success" name="busqueda_filtros" style="width:100%" value="1">Buscar</button>
          </div>
        </div>
      </div>
    </form>
    <form action="" method="POST">
      <div class="col-md-12">
        <div class="input-group">
          <span class="input-group-btn">
            <button class="btn btn-success" name="busqueda_palabra" value="1" type="submit">Buscar</button>
          </span>
          <input type="text" class="form-control" name="palabra" placeholder="Buscar por: #SPP, Nombre, Abreviacion">
        </div><!-- /input-group -->
      </div>
    </form>

  
  <!--<div class="panel-body">-->
  <table class="table table-condensed table-bordered table-hover" style="font-size:11px;">
    <thead>
      <tr>
        <th colspan="3">
          <!--<a class="btn btn-sm btn-warning" href="?OPP&filed">OPP(s) Archivado(s)</a>-->
          <button class="btn btn-sm btn-info" onclick="guardarDatos()"><span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Guardar Cambios</button><!-- BOTON GUARDAR DATOS -->
        </th>
        <th colspan="4">
          Exportar Lista
          <a href="#" onclick="document.formulario1.submit()"><img src="../../img/pdf.png"></a>
          <a href="#" onclick="document.formulario2.submit()"><img src="../../img/excel.png"></a>

          <form name="formulario1" method="POST" action="../../reportes/lista_opp.php">
            <input type="hidden" name="lista_pdf" value="1">
            <input type="hidden" name="query_pdf" value="<?php echo $query_opp; ?>">
          </form> 
          <form name="formulario2" method="POST" action="../../reportes/lista_opp.php">
            <input type="hidden" name="lista_excel" value="2">
            <input type="hidden" name="query_excel" value="<?php echo $query_opp; ?>">
          </form>

        </th>
        <th colspan="6" class="success text-center">
          <p style="font-size:12px;color:red">Total OPP(s): <?php echo $totalOPP; ?></p>
        </th>
      </tr>
      <tr>
        <th class="text-center">#SPP</th>
        <th class="text-center">Nombre</th>
        <th class="text-center">Abreviación</th>
        <th class="text-center">País</th>
        <th class="text-center">Situación <br>OPP</th>
        <th class="text-center">Estatus Publico</th>
        <!--<th class="text-center">Proceso Certificación</th>
        <th class="text-center">Fecha Final<br>(Certificado)</th>-->
        <th class="text-center"><a href="#" data-toggle="tooltip" title="Proceso de Certificación en el que se encuentra la OPP"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>Proceso certificación</a></th>
        <th class="text-center">
          <a href="#" data-toggle="tooltip" title="Fecha en la que expira el Certificado"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>Fecha Final<br>(Certificado)</a>
        </th>
        <th class="text-center"><a href="#" data-toggle="tooltip" title="Estatus del Certificado definido por la fecha de vigencia final">
          <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>Estatus Certificado</a>
        </th>
        <!--<th class="text-center">Sitio WEB</th>-->
        <!--<th class="text-center">Email OPP</th>-->
        <th class="text-center">Productos</th>
        <th class="text-center">Nº Socios</th>
        <th class="text-center">OC</th>
        <!--<th class="text-center">Razón social</th>
        <th class="text-center">Dirección fiscal</th>
        <th class="text-center">RFC</th>-->
        <!--<th class="text-center">Eliminar</th>-->
        <!--<th class="text-center">Acciones</th>-->
        <th style="width:60px;">
          <form  style="margin: 0;padding: 0;" action="" method="POST" >            
              <button class="btn btn-xs btn-danger" type="subtmit" value="2"  name="eliminar" data-toggle="tooltip" data-placement="top" title="Eliminar" onclick="return confirm('¿Está seguro ?, los datos se eliminaran permanentemente');" >
                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
              </button>        
              <button class="btn btn-xs btn-info" type="subtmit" value="1" name="archivar" data-toggle="tooltip" data-placement="top" title="Archivar">
                <span class="glyphicon glyphicon-folder-open" aria-hidden="true"></span>
              </button> 
          </form>
        </th>
      </tr>      
    </thead>
    <form name="formularioActualizar" id="formularioActualizar" action="" method="POST"><!-- INICIA FORM -->
      <input type="hidden" name="actualizacion_opp" value="1">
      <tbody style="font-size:10px">
      <?php 
      if($totalOPP == 0){
        echo "<tr><td class='text-center info' colspan='12'>No se encontraron Registros</td></tr>";
      }else{
        while($opp = mysql_fetch_assoc($detalle_opp)){
        ?>
          <tr>
            <!--- INICIA CODIGO SPP ---->
            <td>
                <a class="btn btn-primary btn-xs" style="width:100%;font-size:10px;" href="?OPP&amp;detail&amp;idopp=<?php echo $opp['idopp']; ?>&contact">Consultar<br>
                  <!--<?php echo "<br>IDOPP: ".$row_opp['idOPP']; ?>-->
                </a>
                <input type="text" name="spp<?php echo $opp['idopp'];?>" value="<?php echo $opp['spp_opp']; ?>">
            </td>
            <!--- TERMINA CODIGO SPP ---->

            <!--- INICIA NOMBRE ---->
            <td>
              <?php echo $opp['nombre']; ?>
            </td>
            <!--- TERMINA NOMBRE ---->

            <!--- INICIA ABREVIACIÓN ---->
            <td>
              <?php
              echo $opp['abreviacion_opp'];
               ?>
            </td>
            <!--- TERMINA ABREVIACIÓN ---->
            <!--- INICIA PAIS ---->
            <td>
              <?php echo $opp['pais']; ?>
            </td>
            <!--- TERMINA PAIS ---->

            <!--- INICIA SITUACION OPP ---->
            <td>
              <select name="estatus_opp<?php  echo $opp['idopp']; ?>" id="">
                <option value="">...</option>
                <option value="NUEVA" <?php if($opp['estatus_opp'] == 'NUEVA'){ echo 'selected';} ?>>NUEVA</option>
                <option value="RENOVACION" <?php if($opp['estatus_opp'] == 'RENOVACION'){ echo 'selected';} ?>>RENOVACIÓN</option>
                <option value="CANCELADA" <?php if($opp['estatus_opp'] == 'CANCELADA'){ echo 'selected';} ?>>CANCELADA</option>
              </select>
              <?php 
              if($opp['estatus_opp'] == 'NUEVA'){
                echo "<p class='alert alert-success' style='font-size:10px;padding:5px;'>NUEVA</p>";
              }else if($opp['estatus_opp'] == 'RENOVACION'){
                echo "<p class='alert alert-warning' style='font-size:10px;padding:5px;'>RENOVACIÓN</p>";
              }else if($opp['estatus_opp'] == 'CANCELADA'){
                echo "<p class='alert alert-danger' style='font-size:10px;padding:5px;'>CANCELADA</p>";
              }
               ?>
            </td>
            <!--- TERMINA SITUACION OPP ---->

            <!--- INICIA ESTATUS_PUBLICO ---->
            <td>
              <?php 
                echo $opp['nombre_publico']; 
              ?>
            </td>
            <!--- TERMINA ESTATUS_PUBLICO ---->

            <!--- INICIA PROCESO_CERTIFICACIÓN ---->
            <td>
              <select name="estatus_interno<?php echo $opp['idopp']; ?>">
                <option>...</option>
                <?php 
                $row_interno = mysql_query("SELECT * FROM estatus_interno", $dspp) or die(mysql_error());
                while($estatus_interno = mysql_fetch_assoc($row_interno)){
                ?>
                  <option value="<?php echo $estatus_interno['idestatus_interno'] ?>" <?php if($estatus_interno['idestatus_interno'] == $opp['estatus_interno']){echo "selected";} ?>><?php echo $estatus_interno['nombre']; ?></option>
                <?php
                }
                 ?>
              </select>
              <?php echo "<p class='alert alert-info' style='padding:7px;'>$opp[nombre_interno]</p>"; ?>
            </td>
            <!--- TERMINA PROCESO_CERTIFICACIÓN ---->

            <!--- INICIA FECHA_FINAL ---->
            <td>
              <?php 
                $vigenciafin = date('d-m-Y', strtotime($opp['vigencia_fin']));
                $timeVencimiento = strtotime($opp['vigencia_fin']);
              
               ?>
              <input type="date" name="vigencia_fin<?php echo $opp['idopp']; ?>" value="<?php echo $opp['vigencia_fin']; ?>">
            </td>
            <!--- TERMINA FECHA_FINAL ---->

            <!--- INICIA ESTATUS_CERTIFICADO ---->
              <?php 
              if(isset($opp['idcertificado'])){

                $estatus_certificado = mysql_query("SELECT idcertificado, estatus_certificado, estatus_dspp.nombre FROM certificado LEFT JOIN estatus_dspp ON certificado.estatus_certificado = estatus_dspp.idestatus_dspp WHERE idcertificado = $opp[idcertificado]", $dspp) or die(mysql_error());
                $certificado = mysql_fetch_assoc($estatus_certificado);

                switch ($certificado['estatus_certificado']) {
                  case '13': //certificado "activo"
                    $clase = 'success';
                    break;
                  case '14': //certificado "renovacion"
                    $clase = 'info';
                    break;
                  case '15': //certificado "por expirar"
                    $clase = 'warning';
                    break;
                  case '16': //certificado "Expirado"
                    $clase = 'danger';
                    break;

                  default:
                    # code...
                    break;
                }
                 echo "<td class='".$clase."'>".$certificado['nombre']."</td>";
              }else{
                echo "<td>No Disponible</td>";
              }
                //echo $opp['estatus_certificado'];
               ?>
            <!--- TERMINA ESTATUS_CERTIFICADO ---->

            <!--- INICIA PRODUCTOS ---->
            <td>
              <?php 
              $row_productos = mysql_query("SELECT * FROM productos WHERE idopp = $opp[idopp]", $dspp) or die(mysql_error());
              $total_productos = mysql_num_rows($row_productos);
              ?>


              <a style="font-size:14px;" href="../../agregar_producto.php?idopp=<?php echo $opp['idopp']; ?>" target="ventana1" onclick="ventanaNueva ('', 500, 400, 'ventana1');"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span></a>
              <?php
              if($total_productos == 0){
              ?>
               No Disponible
              <?php
              }
             
              while($productos = mysql_fetch_assoc($row_productos)){
                echo $productos['producto']."<br>";
              }
               ?>
            </td>
            <!--- TERMINA PRODUCTOS ---->

            <!--- INICIA NUMERO_SOCIOS ---->
            <td>
              <input type="number" name="num_socios<?php echo $opp['idopp']; ?>" value="<?php echo $opp['numero']; ?>">
              <?php echo $opp['numero']; ?>
            </td>
            <!--- TERMINA NUMERO_SOCIOS ---->

            <!--- INICIA ABREVIACION OC ---->
            <td>
              <?php 
              $row_oc = mysql_query("SELECT * FROM oc", $dspp) or die(mysql_error());
              ?>
              <select name="idoc<?php echo $opp['idopp'];?>" id="">
                <option value="">...</option>
                <?php 
                while($oc = mysql_fetch_assoc($row_oc)){
                ?>
                  <option value="<?php echo $oc['idoc']; ?>" <?php if($oc['idoc'] == $opp['idoc']){echo "selected"; } ?>><?php echo $oc['abreviacion']; ?></option>
                <?php
                }
                 ?>
              </select>
              <?php 
               if(!empty($opp['abreviacion_oc'])){
                echo "<p class='alert alert-info' style='padding:5px;'>".$opp['abreviacion_oc']."</p>";
               }
              ?>
            </td>
            <!--- TERMINA ABREVIACION OC ---->

            <!--- INICIA ACCIONES ---->
              <td class="text-center">

                <div name="formulario">
                  <input type="checkbox" name="idoppCheckbox" id="<?php echo "idopp".$contador; ?>" value="<?php echo $opp['idopp']; ?>" onclick="addCheckbox()">
                </div>
              </td>
            <!--- TERMINA ACCIONES ---->

          </tr>
        <?php
        }
      }
       ?>
      </tbody>
    </form><!-- TERMINA FORM -->
    
  </table>


  <!--</div>-->
<!--</div>-->




  <input type="hidden" name="prueba2" value="2">
  <table>
    <tr>
    <td width="20"><?php if ($pageNum_opp > 0) { // Show if not first page ?>
    <a href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, 0, $queryString_opp); ?>">
    <span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span>
    </a>
    <?php } // Show if not first page ?></td>
    <td width="20"><?php if ($pageNum_opp > 0) { // Show if not first page ?>
    <a href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, max(0, $pageNum_opp - 1), $queryString_opp); ?>" >
    <span class="glyphicon glyphicon-backward" aria-hidden="true"></span>
    </a>
    <?php } // Show if not first page ?></td>
    <td width="20"><?php if ($pageNum_opp < $totalPages_opp) { // Show if not last page ?>
    <a href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, min($totalPages_opp, $pageNum_opp + 1), $queryString_opp); ?>">
    <span class="glyphicon glyphicon-forward" aria-hidden="true"></span>
    </a>
    <?php } // Show if not last page ?></td>
    <td width="20"><?php if ($pageNum_opp < $totalPages_opp) { // Show if not last page ?>
    <a  href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, $totalPages_opp, $queryString_opp); ?>" >
    <span class="glyphicon glyphicon-fast-forward" aria-hidden="true"></span>
    </a>
    <?php } // Show if not last page ?></td>
    </tr>
  </table>  

<script type="text/javascript">
<!--
function ventanaNueva(documento,ancho,alto,nombreVentana){
    window.open(documento, nombreVentana,'width=' + ancho + ', height=' + alto);
}
     
//-->
</script>

<script>
var contador=0;

  function tabla_productos()
  {
    contador++;
  var table = document.getElementById("tabla_productos");
    {
    var row = table.insertRow(2);
    var cell1 = row.insertCell(0);

    cell1.innerHTML = '<input type="text" class="form-control" name="nombre_producto['+contador+']" id="exampleInputEmail1" placeholder="Nombre">';

    }
  } 


</script>

<script language="JavaScript"> 

var contadorPHP = 'qwerty';
var miVariable = [];
var idopp = '';


function addCheckbox(){
  var cont = 0;
  var checkboxIdopp = document.getElementsByName("idoppCheckbox");
//var precio=document.getElementById('precio').value;

  for (var i=0; i<checkboxIdopp.length; i++) {
    if (checkboxIdopp[i].checked == 1) { 
      //alert("EL VALOR ES: "+checkboxIdopp[i].value); 
      //cont = cont + 1; 
      idopp = checkboxIdopp[i].value; 
      sessionStorage[idopp] = idopp; 

    }

  }

  for(var i=0;i<sessionStorage.length;i++){
    var idopp=sessionStorage.key(i);
    miVariable[i] = idopp;
    document.cookie = 'variable='+miVariable;
  }
}



function mostrarDatos(){
  var datosDisponibles=document.getElementById('datosDisponibles');
  datosDisponibles.innerHTML='';
  for(var i=0;i<sessionStorage.length;i++){
    var idopp=sessionStorage.key(i);
    var variablePHP = "<?php $otraVariable = 6; ?>";
    datosDisponibles.innerHTML += '<div>'+idopp+'</div>';
  }
 
}

function limpiarVista() {
var datosDisponibles=document.getElementById('datosDisponibles');
datosDisponibles.innerHTML='Limpiada vista. Los datos permanecen.';
}
 
function borrarTodo() {
  var cookies = document.cookie.split(";");

  for (var i = 0; i < cookies.length; i++) {
    var cookie = cookies[i];
    var eqPos = cookie.indexOf("=");
    var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
    document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
  }
  sessionStorage.clear();  

}


function preguntar(){ 
    if(!confirm('¿Estas seguro de eliminar el registro?, los datos se eliminaran permanentemen')){ 
       return false; } 
} 

function guardarDatos(){
  document.getElementById("formularioActualizar").submit();
}


</script>