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
  
<!-- SECCIÓN DE PRUEBA DE LA INFORMACION OPP -->
<!-- SECCIÓN DE PRUEBA DE LA INFORMACION OPP -->
<!-- SECCIÓN DE PRUEBA DE LA INFORMACION OPP -->
<!-- SECCIÓN DE PRUEBA DE LA INFORMACION OPP -->
<!-- SECCIÓN DE PRUEBA DE LA INFORMACION OPP -->

<?php 
/// EN PROCESO
// SON LAS ORGANIZACIONES QUE:
// NO CUENTAN CON SOLICITUD
// CON SOLICITUD SIN COTIZACIÓN
// CON COTIZACIÓN ENVIADA PERO NO ACEPTADA
// NO SE MUESTRAN LAS CANCELADAS, SUSPENDIDAS Y ARCHIVADAS
?>
<div class="row">
  <div class="col-md-12">
    <h4>EN PROCESO</h4>


  </div>  
  <div class="col-md-2">
    <button class="btn btn-sm btn-danger" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
      <span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span> Filtro Avanzado
    </button>  
  </div>
  <div class="col-md-10">
    <form action="" method="POST">

        <div class="input-group">
          <span class="input-group-btn">
            <button class="btn btn-success" name="busqueda_palabra" value="1" type="submit">Buscar</button>
          </span>
          <input type="text" class="form-control" name="palabra" placeholder="Buscar por: #SPP, Nombre, Abreviacion">
        </div><!-- /input-group -->

    </form>
  </div>  
</div>

<!-- CUADRO DE BUSQUEDA AVANZADA -->
<div class="collapse" id="collapseExample">
  
    <form action="" method="POST">
      <div class="col-md-12 alert alert-info">
        <div class="text-center col-md-12">
          <b style="color:#d35400">Seleccione los parametros de los cuales desea realizar la busqueda</b>
        </div> 
        <div class="row">


          <div class="col-xs-4">
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
          <div class="col-xs-4">
            País
            <select name="buscar_pais" class="form-control">
              <option value=''>Selecciona un país</option>
              <?php 
              while($pais = mysql_fetch_assoc($row_pais)){
                echo "<option value='".$pais['pais']."'>".mayuscula($pais['pais'])."</option>";
              }
               ?>
            </select>
          </div>
          <div class="col-xs-4">
            Producto
            <select class="form-control" name="buscar_producto" id="">
              <option value=''>Seleccione un producto</option>
              <?php 
              while($lista_productos = mysql_fetch_assoc($query_productos)){
                echo "<option value='$lista_productos[producto_general]'>$lista_productos[producto_general]</option>";
              }
               ?>
            </select>
          </div>
          <div class="col-xs-12">
            <button type="submit" class="btn btn-success" name="busqueda_filtros" style="width:100%" value="1">Filtrar Información</button>
          </div>
        </div>
      </div>
    </form>

</div>
<!-- TERMINA CUADRO DE BUSQUEDA AVANZADA -->


<?php 
// EN PROCESO LA PRIMERA VEZ
/// SELECCIONA LAS ORGANIZACIONES("OPP") QUE TIENEN SOLICITUD NUEVA, PERO QUE AUN NO SE LES HA ASIGNADO UN DICMTANE POSITIVO
/// DEBEN DE TENER UN ESTATUS-DSPP DEL 1 al 11, o el 17
///PARA EL NUMERO DE SOCIOS TOMAMOS LA RESP1 QUE ES "NUMERO DE SOCIOS"

  /*$query2 = "SELECT idopp FROM solicitud_certificacion WHERE idopp IS NOT NULL GROUP BY idopp ORDER BY idopp";
  $consultar2 = mysql_query($query2,$dspp) or die(mysql_error());
  $ids = '';
  $total = mysql_num_rows($consultar2);
  $contador = 1;
  while($registros = mysql_fetch_assoc($consultar2)){
    if(empty($registros['idsolicitud_certificacion'])){
      if($total > $contador){
        $ids .= 'opp.idopp != '.$registros['idopp'].' AND ';
      }else{
        $ids .= 'opp.idopp != '.$registros['idopp'];
      }
    }
    $contador++;
  }
  echo '<p>'.$total.'</p>';
  echo '<p>'.$ids.'</p>';*/

  //$query = "SELECT opp.idopp, opp.spp, opp.nombre AS 'nombre_opp', opp.abreviacion AS 'abreviacion_opp', opp.pais, oc.abreviacion AS 'abreviacion_oc', opp.estatus_opp AS 'opp_estatus_opp', opp.estatus_publico AS 'opp_estatus_publico', opp.estatus_interno AS 'opp_estatus_interno', opp.estatus_dspp AS 'opp_estatus_dspp', MAX(solicitud_certificacion.idsolicitud_certificacion) AS 'idsolicitud_certificacion', solicitud_certificacion.tipo_solicitud, solicitud_certificacion.estatus_interno AS 'solicitud_estatus_interno', solicitud_certificacion.estatus_dspp AS 'solicitud_estatus_dspp' FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp LEFT JOIN oc ON solicitud_certificacion.idoc = oc.idoc WHERE opp.idopp = 11 OR opp.idopp = 13 OR opp.idopp = 18 OR opp.idopp = 19 OR opp.idopp = 41 OR opp.idopp = 45 OR opp.idopp = 51 OR opp.idopp = 74 OR opp.idopp = 78 OR opp.idopp = 79 OR opp.idopp = 87 OR opp.idopp = 93 OR opp.idopp = 94 OR opp.idopp = 119 OR opp.idopp = 150 OR opp.idopp = 151 OR opp.idopp = 202 OR opp.idopp = 209 OR opp.idopp = 221 OR opp.idopp = 226 OR opp.idopp = 228 OR opp.idopp = 240 OR  opp.idopp = 242 OR opp.idopp = 95 OR opp.idopp = 115 OR opp.idopp = 156 OR opp.idopp = 171 OR opp.idopp = 208 OR opp.idopp = 218 OR opp.idopp = 225 OR opp.idopp = 229 OR opp.idopp = 213 OR opp.idopp = 227 OR opp.idopp = 244 OR opp.idopp = 245 OR opp.idopp = 248  OR opp.idopp = 10 OR opp.idopp = 17 OR opp.idopp = 25 OR opp.idopp = 26 OR opp.idopp = 38 OR opp.idopp = 48 OR opp.idopp = 50 OR opp.idopp = 54 OR opp.idopp = 55 OR opp.idopp = 56 OR opp.idopp = 61 OR opp.idopp = 70 OR opp.idopp = 71 OR opp.idopp = 72 OR opp.idopp = 77 OR opp.idopp = 82 OR opp.idopp = 90 OR opp.idopp = 102 OR opp.idopp = 107 OR opp.idopp = 118 OR opp.idopp = 125 OR opp.idopp = 128 OR opp.idopp = 129 OR opp.idopp = 131 OR opp.idopp = 136 OR opp.idopp = 152 OR opp.idopp = 184 OR opp.idopp = 193 OR opp.idopp = 194 GROUP BY opp.idopp ORDER BY opp.idopp";

  $query = "SELECT opp.idopp, opp.spp, opp.nombre AS 'nombre_opp', opp.abreviacion AS 'abreviacion_opp', opp.pais, oc.abreviacion AS 'abreviacion_oc', opp.estatus_opp AS 'opp_estatus_opp', opp.estatus_publico AS 'opp_estatus_publico', opp.estatus_interno AS 'opp_estatus_interno', opp.estatus_dspp AS 'opp_estatus_dspp', MAX(solicitud_certificacion.idsolicitud_certificacion) AS 'idsolicitud_certificacion', solicitud_certificacion.tipo_solicitud, solicitud_certificacion.estatus_interno AS 'solicitud_estatus_interno', solicitud_certificacion.estatus_dspp AS 'solicitud_estatus_dspp' FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp LEFT JOIN oc ON solicitud_certificacion.idoc = oc.idoc WHERE (opp.estatus_opp != 'CANCELADO' AND opp.estatus_opp != 'SUSPENDIDO' AND opp.estatus_opp != 'CERTIFICADO' AND opp.estatus_opp != 'ARCHIVADO') AND opp.estatus_opp = 0 OR opp.estatus_opp IS NULL OR opp.estatus_opp = 1 OR opp.estatus_opp = 4 GROUP BY opp.idopp ORDER BY opp.idopp";

  $consultar = mysql_query($query,$dspp) or die(mysql_error());
  $total_organizaciones = mysql_num_rows($consultar);


 ?> 
<table class="table table-bordered table-condensed" style="font-size:11px;">
  <thead>
    <tr>
      <th colspan="3">
        <button class="btn btn-sm btn-info disabled"><span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Guardar Cambios</button>
      </th>
      <th colspan="3">
        Exportar Lista
        <!--<a href="#" target="_blank" onclick="document.formulario1.submit()"><img src="../../img/pdf.png"></a>-->
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
      <th class="success text-center" colspan="6">
        NUMERO DE ORGANIZACIONES: <?php echo $total_organizaciones; ?>
      </th>
    </tr>
    <tr>
      <th style="width:20px;">#</th>
      <th>#SPP</th>
      <th>ORGANIZACIÓN</th>
      <th>PAÍS</th>
      <th>OC</th>
      <th>PROCESO CERTIFICACIÓN</th>
      <th>ESTATUS OPP</th>
      <th>ESTATUS PUBLICO</th>
      <th>ESTATUS INTERNO</th>
      <th>ESTATUS DSPP</th>
      <th>ULTIMA FECHA DE CERTIFICADO</th>
      <th>ESTATUS DE LA OPP</th>
      <th>PRODUCTOS</th>
      <th>Nº SOCIOS</th>
      <th>
        <form  style="margin: 0;padding: 0;" action="" method="POST" >            
            <button class="btn btn-xs btn-danger disabled" type="subtmit" value="2"  name="eliminar" data-toggle="tooltip" data-placement="top" title="Eliminar" onclick="return confirm('¿Está seguro ?, los datos se eliminaran permanentemente');" >
              <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
            </button>        
            <button class="btn btn-xs btn-info disabled" type="subtmit" value="1" name="archivar" data-toggle="tooltip" data-placement="top" title="Archivar">
              <span class="glyphicon glyphicon-folder-open" aria-hidden="true"></span>
            </button>
        </form>
      </th>
    </tr>
  </thead>
  <tbody>
  <?php 
  $contador = 1;
  while($informacion = mysql_fetch_assoc($consultar)){
    echo '<tr>';
      echo '<td style="width:20px;">'.$contador.'</td>';
  ?>
    <td>
      <a class="btn btn-primary btn-xs" style="width:100%;font-size:10px;" href="?OPP&amp;detail&amp;idopp=<?php echo $informacion['idopp']; ?>">Consultar<br></a>
      <input type="text" name="spp<?php echo $informacion['idopp'];?>" value="<?php echo $informacion['spp']; ?>">
    </td>
    <!-- TIPO DE SOLICITUD -->

    <!-- NOMBRE DE LA OPP -->
    <td>
      <?php echo '<span style="color:#bdc3c7">ID: '.$informacion['idopp'].'| </span>'.mayuscula($informacion['nombre_opp']).' (<span style="color:red">'.mayuscula($informacion['abreviacion_opp']).'</span>)'; ?>
    </td>
    <!-- PAIS DE LA OPP -->
    <td>
      <?php echo mayuscula($informacion['pais']); ?>
    </td>
    <!-- OC -->
    <td>
      <?php echo $informacion['abreviacion_oc']; ?>
    </td>

    <!-- PROCESO DE CERTIFICACION -->
    <td>
      <select name="">
        <option>...</option>
        <?php 
        $row_interno = mysql_query("SELECT * FROM estatus_interno", $dspp) or die(mysql_error());
        while($estatus_interno = mysql_fetch_assoc($row_interno)){
        ?>
          <option value=""><?php echo $estatus_interno['nombre']; ?></option>
        <?php
        }
         ?>
      </select>

    </td>
    <!-- ESTATUS OPP -->
    <td>
      <?php 
      $consultar10 = mysql_query("SELECT nombre FROM estatus_dspp WHERE idestatus_dspp = '$informacion[opp_estatus_opp]'", $dspp) or die(mysql_error());
      $detalle10 = mysql_fetch_assoc($consultar10); 
      echo $informacion['opp_estatus_opp'].' - <span style="color:green">'.$detalle10['nombre'].'</span>'; 
      ?>
    </td>
    <!-- ESTATUS PUBLICO -->
    <td>
      <?php 
        echo '<p>OPP: '.$informacion['opp_estatus_publico'].'</p>';
       ?>
    </td>
    <!-- ESTATUS INTERNO -->
    <td>
      <?php
      $consultar3 = mysql_query("SELECT nombre FROM estatus_interno WHERE idestatus_interno = '$informacion[solicitud_estatus_interno]'", $dspp) or die(mysql_error());
      $detalle3 = mysql_fetch_assoc($consultar3);
        echo '<p>SOLICITUD: '.$informacion['solicitud_estatus_interno'].' - <span style="color:blue">'.$detalle3['nombre'].'</span></p>';
      $consultar4 = mysql_query("SELECT nombre FROM estatus_interno WHERE idestatus_interno = '$informacion[opp_estatus_interno]'", $dspp) or die(mysql_error());
      $detalle4 = mysql_fetch_assoc($consultar4);
        echo '<p>OPP: '.$informacion['opp_estatus_interno'].' - <span style="color:red">'.$detalle4['nombre'].'</span></p>';
       ?>
    </td>
    <!-- ESTATUS DSPP -->
    <td>
      <?php
      $consultar5 = mysql_query("SELECT nombre FROM estatus_dspp WHERE idestatus_dspp = '$informacion[solicitud_estatus_dspp]'", $dspp) or die(mysql_error());
      $detalle5 = mysql_fetch_assoc($consultar5);
        echo '<p>SOLICITUD: '.$informacion['solicitud_estatus_dspp'].' - <span style="color:blue">'.$detalle5['nombre'].'</span></p>';
      $consultar6 = mysql_query("SELECT nombre FROM estatus_dspp WHERE idestatus_dspp = '$informacion[opp_estatus_dspp]'", $dspp) or die(mysql_error());
      $detalle7 = mysql_fetch_assoc($consultar6);
        echo '<p>OPP: '.$informacion['opp_estatus_dspp'].' - <span style="color:red">'.$detalle7['nombre'].'</span></p>';
       ?>
    </td>
    <!-- ULTIMA FECHA DE CERTIFICADO -->
    <td>
      <?php 
       /* echo $informacion['idcertificado'];
        if($informacion['estatus_opp'] == 13){ /// SI LA FECHA DEL CERTIFICADO FUE CARGADA DESDE LA SECCIÓN DE "INFORMACIÓN OPP" REALIZAMOS LA CONSULTA A LA TABLA CERTIFICADOS
          $query = "SELECT idcertificado, vigencia_inicio, vigencia_fin FROM certificado WHERE idopp = $informacion[idopp]";
          $consultar_certificado = mysql_query($query,$dspp) or die(mysql_error());
          $detalle_certificado = mysql_fetch_assoc($consultar_certificado);
          $fecha_certificado = strtotime($detalle_certificado['vigencia_fin']);
          $vigencia_fin = date('d/m/Y', $fecha_certificado);
          echo '<p>'.$vigencia_fin.'</p>';
        }else{
          echo 'EN PROCESO';
        }
        */ 
      ?>
    </td>
    <!-- ESTATUS DE LA OPP -->
    <td>
      <?php 
      /*
      if($informacion['estatus_opp'] == 13){ // la organización se ha certificado, desde la tabla de información OPP, pero dentro de la tabla de solicitudes no se ha terminado el proceso
        //estatus_dspp 13 = CERTIFICADA
        echo '<a href="#" style="color:#e67e22" data-toggle="tooltip" title="ESTA ORGANIZACIÓN SE ENCUENTRA CERTIFICADA PERO AUN NO SE HA CARGADO EL CERTIFICADO EN LA SECCIÓN DE SOLICITUDES"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span> CERTIFICADA</a>';
      }else{
        echo 'EN PROCESO';
      }
      */
      ?>
    </td>
    <!-- PRODUCTOS -->
    <td>
 
    </td>
    <!-- NUMERO DE SOCIOS -->
    <td>

    </td>
    <td></td>

  <?php
    echo '</tr>';
    $contador++;
  }
   ?>
  </tbody>
</table>
