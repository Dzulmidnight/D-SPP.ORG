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
        $query = "UPDATE opp SET estatus_opp = 'ARCHIVADO' WHERE idopp = $token";
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
          /*if($estatus_opp == 'CANCELADO'){
            $estatus_interno = 10;
            $estatus_publico = 3;
          }*/
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
          4.- CANCELADO
          */
          $estatus_publico = "";
          if($estatus_interno == 10){ //ESTATUS CANCELADO
            $estatus_publico = 3;

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

  $query_opp = "SELECT opp.idopp, opp.idoc, opp.password, opp.spp AS 'spp_opp', opp.nombre, opp.abreviacion AS 'abreviacion_opp', opp.pais, opp.email, opp.sitio_web, opp.telefono, opp.estatus_opp, opp.estatus_publico, opp.estatus_interno, opp.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', num_socios.numero, MAX(certificado.idcertificado) AS 'idcertificado', MAX(certificado.vigencia_fin) AS 'fecha_fin' FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.idoc = $_GET[query] AND opp.estatus_opp != 'ARCHIVADO' GROUP BY opp.idopp ORDER BY opp.abreviacion ASC";


  $queryExportar = "SELECT opp.*, contacto.*  FROM opp LEFT JOIN contacto ON opp.idopp = contacto.idopp WHERE idoc = $_GET[query] AND (opp.estatus_opp IS NULL OR opp.estatus_opp != 'ARCHIVADO') ORDER BY opp.idopp ASC";



}else if(isset($_POST['busqueda_palabra']) && $_POST['busqueda_palabra'] == "1"){
  $palabra = $_POST['palabra'];
  if(empty($palabra)){
    $query_opp = "SELECT opp.idopp, opp.idoc, opp.password, opp.spp AS 'spp_opp', opp.nombre, opp.abreviacion AS 'abreviacion_opp', opp.pais, opp.email, opp.sitio_web, opp.telefono, opp.estatus_opp, opp.estatus_publico, opp.estatus_interno, opp.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', num_socios.numero, MAX(certificado.idcertificado) AS 'idcertificado', MAX(certificado.vigencia_fin) AS 'fecha_fin' FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE (opp.estatus_opp IS NULL) OR (opp.estatus_opp != 'ARCHIVADO') GROUP BY opp.idopp ORDER BY opp.abreviacion ASC";
  }else{
    $query_opp = "SELECT opp.idopp, opp.idoc, opp.password, opp.spp AS 'spp_opp', opp.nombre, opp.abreviacion AS 'abreviacion_opp', opp.pais, opp.email, opp.sitio_web, opp.telefono, opp.estatus_opp, opp.estatus_publico, opp.estatus_interno, opp.estatus_dspp, oc.idoc, estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', num_socios.numero, MAX(certificado.idcertificado) AS 'idcertificado', MAX(certificado.vigencia_fin) AS 'fecha_fin' FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.spp LIKE '%$palabra%' OR opp.nombre LIKE '%$palabra%' OR opp.abreviacion LIKE '%$palabra%' AND opp.estatus_opp != 'ARCHIVADO' GROUP BY opp.idopp ORDER BY opp.abreviacion ASC";
  }



  $queryExportar = "SELECT opp.*, contacto.*  FROM opp LEFT JOIN contacto ON opp.idopp = contacto.idopp WHERE (opp.estatus_opp != 'ARCHIVADO' OR opp.estatus_opp IS NULL) AND ((opp.idf LIKE '%$palabra%') OR (opp.nombre LIKE '%$palabra%') OR (opp.abreviacion LIKE '%$palabra%') OR (sitio_web LIKE '%$palabra%') OR (email LIKE '%$palabra%') OR (pais LIKE '%$palabra%') OR (razon_social LIKE '%$palabra%') OR (direccion_fiscal LIKE '%$palabra%') OR (rfc LIKE '%$palabra%')) ORDER BY opp.idopp ASC";



}else if(isset($_POST['busqueda_filtros']) && $_POST['busqueda_filtros'] == 1){
  $idoc = $_POST['buscar_oc'];
  $pais = $_POST['buscar_pais'];
  //$estatus = $_POST['buscar_estatus'];
  $producto = $_POST['buscar_producto'];
  $idopp_producto = '';

  if(!empty($idoc) && !empty($pais) && !empty($producto) && !empty($estatus)){

    $query_productos = mysql_query("SELECT opp.idopp, productos.producto FROM opp LEFT JOIN productos ON opp.idopp = productos.idopp WHERE opp.idoc = $idoc AND opp.pais = '$pais' AND opp.estatus_dspp = $estatus AND (opp.estatus_opp != 'ARCHIVADO' OR opp.estatus_opp IS NULL) AND producto_general LIKE '%$producto%' GROUP BY idopp", $dspp) or die(mysql_error());
    $total_idopp = mysql_num_rows($query_productos);
    $cont_idopp = 1;
    while($producto_opp = mysql_fetch_assoc($query_productos)){
      $idopp_producto .= "opp.idopp = '$producto_opp[idopp]'";
      if($cont_idopp < $total_idopp){
        $idopp_producto .= " OR ";
      }
      $cont_idopp++;
    }

    $query_opp = "SELECT opp.idopp, opp.idoc, opp.password, opp.spp AS 'spp_opp', opp.nombre, opp.abreviacion AS 'abreviacion_opp', opp.pais, opp.email, opp.sitio_web, opp.telefono, opp.estatus_opp, opp.estatus_publico, opp.estatus_interno, opp.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', num_socios.numero, MAX(certificado.idcertificado) AS 'idcertificado', MAX(certificado.vigencia_fin) AS 'fecha_fin' FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE $idopp_producto AND (opp.estatus_opp != 'ARCHIVADO' OR opp.estatus_opp IS NULL) GROUP BY opp.idopp ORDER BY opp.abreviacion ASC";

  }else if(!empty($idoc) && !empty($pais) && !empty($estatus) && empty($producto)){
    $query_opp = "SELECT opp.idopp, opp.idoc, opp.password, opp.spp AS 'spp_opp', opp.nombre, opp.abreviacion AS 'abreviacion_opp', opp.pais, opp.email, opp.sitio_web, opp.telefono, opp.estatus_opp, opp.estatus_publico, opp.estatus_interno, opp.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', num_socios.numero, MAX(certificado.idcertificado) AS 'idcertificado', MAX(certificado.vigencia_fin) AS 'fecha_fin' FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.idoc = $idoc AND opp.pais = '$pais' AND opp.estatus_dspp = $estatus AND opp.estatus_opp != 'ARCHIVADO' GROUP BY opp.idopp ORDER BY opp.abreviacion ASC";

  }else if(!empty($idoc) && !empty($pais) && empty($estatus) && empty($producto)){
    $query_opp = "SELECT opp.idopp, opp.idoc, opp.password, opp.spp AS 'spp_opp', opp.nombre, opp.abreviacion AS 'abreviacion_opp', opp.pais, opp.email, opp.sitio_web, opp.telefono, opp.estatus_opp, opp.estatus_publico, opp.estatus_interno, opp.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', num_socios.numero, MAX(certificado.idcertificado) AS 'idcertificado', MAX(certificado.vigencia_fin) AS 'fecha_fin' FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.idoc = $idoc AND opp.pais = '$pais' AND opp.estatus_opp != 'ARCHIVADO' GROUP BY opp.idopp ORDER BY opp.abreviacion ASC"; 

  }else if(!empty($idoc) && empty($pais) && empty($estatus) && empty($producto)){
    $query_opp = "SELECT opp.idopp, opp.idoc, opp.password, opp.spp AS 'spp_opp', opp.nombre, opp.abreviacion AS 'abreviacion_opp', opp.pais, opp.email, opp.sitio_web, opp.telefono, opp.estatus_opp, opp.estatus_publico, opp.estatus_interno, opp.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', num_socios.numero, MAX(certificado.idcertificado) AS 'idcertificado', MAX(certificado.vigencia_fin) AS 'fecha_fin' FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.idoc = $idoc AND opp.estatus_opp != 'ARCHIVADO' GROUP BY opp.idopp ORDER BY opp.abreviacion ASC";

  }else if(empty($idoc) && !empty($pais) && empty($estatus) && empty($producto)){
    $query_opp = "SELECT opp.idopp, opp.idoc, opp.password, opp.spp AS 'spp_opp', opp.nombre, opp.abreviacion AS 'abreviacion_opp', opp.pais, opp.email, opp.sitio_web, opp.telefono, opp.estatus_opp, opp.estatus_publico, opp.estatus_interno, opp.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', num_socios.numero, MAX(certificado.idcertificado) AS 'idcertificado', MAX(certificado.vigencia_fin) AS 'fecha_fin' FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.pais = '$pais' AND (opp.estatus_opp != 'ARCHIVADO' OR opp.estatus_opp IS NULL) GROUP BY opp.idopp ORDER BY opp.abreviacion ASC";

  }else if(empty($idoc) && empty($pais) && empty($estatus) && !empty($producto)){
    $query_productos = mysql_query("SELECT opp.idopp, productos.producto, opp.estatus_opp FROM opp LEFT JOIN productos ON opp.idopp = productos.idopp WHERE producto_general LIKE '%$producto%' AND (opp.estatus_opp != 'ARCHIVADO' OR opp.estatus_opp IS NULL) GROUP BY idopp", $dspp) or die(mysql_error());
    $total_idopp = mysql_num_rows($query_productos);
    $cont_idopp = 1;
    while($producto_opp = mysql_fetch_assoc($query_productos)){
      $idopp_producto .= "opp.idopp = '$producto_opp[idopp]'";
      if($cont_idopp < $total_idopp){
        $idopp_producto .= " OR ";
      }
      $cont_idopp++;
    }

    $query_opp = "SELECT opp.idopp, opp.idoc, opp.password, opp.spp AS 'spp_opp', opp.nombre, opp.abreviacion AS 'abreviacion_opp', opp.pais, opp.email, opp.sitio_web, opp.telefono, opp.estatus_opp, opp.estatus_publico, opp.estatus_interno, opp.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', num_socios.numero, MAX(certificado.idcertificado) AS 'idcertificado', MAX(certificado.vigencia_fin) AS 'fecha_fin' FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE $idopp_producto AND opp.estatus_opp != 'ARCHIVADO' GROUP BY opp.idopp ORDER BY opp.abreviacion ASC";

  }else if(!empty($idoc) && empty($pais) && empty($estatus) && !empty($producto)){
    $query_productos = mysql_query("SELECT opp.idopp, productos.producto, opp.estatus_opp FROM opp LEFT JOIN productos ON opp.idopp = productos.idopp WHERE opp.idoc = '$idoc' AND producto_general LIKE '%$producto%' AND opp.estatus_opp != 'ARCHIVADO' GROUP BY idopp", $dspp) or die(mysql_error());
    $total_idopp = mysql_num_rows($query_productos);
    $cont_idopp = 1;
    while($producto_opp = mysql_fetch_assoc($query_productos)){
      $idopp_producto .= "opp.idopp = '$producto_opp[idopp]'";
      if($cont_idopp < $total_idopp){
        $idopp_producto .= " OR ";
      }
      $cont_idopp++;
    }

    $query_opp = "SELECT opp.idopp, opp.idoc, opp.password, opp.spp AS 'spp_opp', opp.nombre, opp.abreviacion AS 'abreviacion_opp', opp.pais, opp.email, opp.sitio_web, opp.telefono, opp.estatus_opp, opp.estatus_publico, opp.estatus_interno, opp.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', num_socios.numero, MAX(certificado.idcertificado) AS 'idcertificado', MAX(certificado.vigencia_fin) AS 'fecha_fin' FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE $idopp_producto AND opp.estatus_opp != 'ARCHIVADO' GROUP BY opp.idopp ORDER BY opp.abreviacion ASC";

  }else if(empty($idoc) && !empty($pais) && empty($estatus) && !empty($producto)){
    $query_productos = mysql_query("SELECT opp.idopp, productos.producto, opp.estatus_opp FROM opp LEFT JOIN productos ON opp.idopp = productos.idopp WHERE opp.pais = '$pais' AND producto_general LIKE '%$producto%' AND opp.estatus_opp != 'ARCHIVADO' GROUP BY idopp", $dspp) or die(mysql_error());
    $total_idopp = mysql_num_rows($query_productos);
    $cont_idopp = 1;
    while($producto_opp = mysql_fetch_assoc($query_productos)){
      $idopp_producto .= "opp.idopp = '$producto_opp[idopp]'";
      if($cont_idopp < $total_idopp){
        $idopp_producto .= " OR ";
      }
      $cont_idopp++;
    }

    $query_opp = "SELECT opp.idopp, opp.idoc, opp.password, opp.spp AS 'spp_opp', opp.nombre, opp.abreviacion AS 'abreviacion_opp', opp.pais, opp.email, opp.sitio_web, opp.telefono, opp.estatus_opp, opp.estatus_publico, opp.estatus_interno, opp.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', num_socios.numero, MAX(certificado.idcertificado) AS 'idcertificado', MAX(certificado.vigencia_fin) AS 'fecha_fin' FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE $idopp_producto AND opp.estatus_opp != 'ARCHIVADO' GROUP BY opp.idopp ORDER BY opp.abreviacion ASC"; 
  }else if(empty($idoc) && empty($pais) && !empty($estatus) && empty($producto)){
    $query_opp = "SELECT opp.idopp, opp.idoc, opp.password, opp.spp AS 'spp_opp', opp.nombre, opp.abreviacion AS 'abreviacion_opp', opp.pais, opp.estatus_opp, opp.email, opp.sitio_web, opp.telefono, opp.estatus_publico, opp.estatus_interno, opp.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', num_socios.numero, MAX(certificado.idcertificado) AS 'idcertificado', MAX(certificado.vigencia_fin) AS 'fecha_fin' FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.estatus_dspp = $estatus AND opp.estatus_opp != 'ARCHIVADO' GROUP BY opp.idopp ORDER BY opp.abreviacion ASC";

  }else{
    $query_opp = "SELECT opp.idopp, opp.idoc, opp.password, opp.spp AS 'spp_opp', opp.nombre, opp.abreviacion AS 'abreviacion_opp', opp.pais, opp.estatus_opp, opp.email, opp.sitio_web, opp.telefono, opp.estatus_publico, opp.estatus_interno, opp.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', num_socios.numero, MAX(certificado.idcertificado) AS 'idcertificado', MAX(certificado.vigencia_fin) AS 'fecha_fin' FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE (opp.estatus_opp IS NULL) OR (opp.estatus_opp != 'ARCHIVADO') GROUP BY opp.idopp ORDER BY opp.abreviacion ASC";
  }


}else{

  ///CONSULTAMOS LAS ORGANIZACIONES ARCHIVADAS
  $query_opp = "SELECT opp.idopp, opp.idoc, opp.password, opp.spp AS 'spp_opp', opp.nombre, opp.abreviacion AS 'abreviacion_opp', opp.pais, opp.email, opp.sitio_web, opp.telefono, opp.estatus_opp, opp.estatus_publico, opp.estatus_interno, opp.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', num_socios.numero, MAX(certificado.idcertificado) AS 'idcertificado', MAX(certificado.vigencia_fin) AS 'fecha_fin' FROM opp LEFT JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_publico ON opp.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN num_socios ON opp.idopp = num_socios.idopp LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.estatus_opp = 'ARCHIVADO' GROUP BY opp.idopp ORDER BY opp.abreviacion ASC";


  $queryExportar = "SELECT opp.*, contacto.*  FROM opp LEFT JOIN contacto ON opp.idopp = contacto.idopp WHERE (opp.estatus_opp IS NULL) OR (opp.estatus_opp != 'ARCHIVADO') ORDER BY opp.idopp ASC";

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
$row_pais = mysql_query("SELECT pais FROM opp GROUP BY pais", $dspp) or die(mysql_error());
$query_productos = mysql_query("SELECT producto_general FROM productos WHERE productos.idopp IS NOT NULL AND productos.producto_general IS NOT NULL GROUP BY producto_general ORDER BY productos.producto_general ASC",$dspp) or die(mysql_error());

 ?>

<div class="row">
  <div class="col-md-12">
    <div class="btn-group" role="group" aria-label="...">
      <a class="btn btn-sm <?php if(isset($_GET['nuevo_certificado'])){ echo 'btn-warning'; }else{ echo 'btn-default'; } ?>" href="?OPP&certificadas&nuevo_certificado">Nuevas</a>
      <a class="btn btn-sm <?php if(isset($_GET['certificado_renovado'])){ echo 'btn-warning'; }else{ echo 'btn-default'; } ?>" href="?OPP&certificadas&certificado_renovado">Renovación</a>
    </div>
    <hr style="margin-top:0px;">
  </div>
</div>
  
  <?php 
  if(isset($_GET['nuevo_certificado'])){
    include('nuevo_certificado.php');
  }else if(isset($_GET['certificado_renovado'])){
    include('certificado_renovado.php');
  }else{
    echo 'NADA';
  }

   ?>
