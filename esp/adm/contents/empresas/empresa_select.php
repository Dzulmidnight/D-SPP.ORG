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

if(isset($_POST['empresa_delete'])){
  $query=sprintf("delete from empresa where idempresa = %s",GetSQLValueString($_POST['idempresa'], "text"));
  $ejecutar=mysql_query($query,$dspp) or die(mysql_error());
}

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_empresa = 40;
$pageNum_empresa = 0;
if (isset($_GET['pageNum_empresa'])) {
  $pageNum_empresa = $_GET['pageNum_empresa'];
}
$startRow_empresa = $pageNum_empresa * $maxRows_empresa;

mysql_select_db($database_dspp, $dspp);

$timeActual = time();

  if(isset($_POST['archivar']) && $_POST['archivar'] == 1){

    $miVariable =  $_COOKIE["variable"];
    $token = strtok($miVariable, ",");

     while ($token !== false) 
     {
        $query = "UPDATE empresa SET estado = 'ARCHIVADO' WHERE idempresa = $token";
        $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
        //echo "$token<br>";
        $token = strtok(",");
     }
  
      echo '<script>borrarTodo();</script>';
      echo '<script>location.href="?EMPRESAS&select";</script>';
  }
  if(isset($_POST['eliminar']) && $_POST['eliminar'] == 2){
    $miVariable =  $_COOKIE["variable"];
    $token = strtok($miVariable, ",");

     while ($token !== false) 
     {
        $deleteSQL = sprintf("DELETE FROM empresa WHERE idempresa = %s",
          GetSQLValueString($token, "int"));
        $eliminar = mysql_query($deleteSQL, $dspp) or die(mysql_error());

        //$query = "DELETE FROM empresa WHERE idempresa = $token";
        //$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
        //echo "$token<br>";
        $token = strtok(",");
     }
      echo '<script>borrarTodo();</script>';
      echo '<script>location.href="?EMPRESAS&select";</script>';

  }

  if(isset($_POST['actualizacion_empresa']) && $_POST['actualizacion_empresa'] == 1){/* INICIA BOTON ACTUALIZAR LISTA empresa*/

    $row_empresa = mysql_query("SELECT * FROM empresa",$dspp) or die(mysql_error());
    $cont = 1;
    $fecha = time();

    while($datos_empresa = mysql_fetch_assoc($row_empresa)){
      //$nombre = "estatusPagina"+$datos_empresa['idempresa']+"";


      if(isset($_POST['estatus_empresa'.$datos_empresa['idempresa']])){/*********************************** INICIA ESTATUS_EMPRESA(SITUACIÓN) ******************/
        $estatus_empresa = $_POST['estatus_empresa'.$datos_empresa['idempresa']];

        if(!empty($estatus_empresa)){
          $updateSQL = sprintf("UPDATE empresa SET estatus_empresa = %s WHERE idempresa = %s",
            GetSQLValueString($estatus_empresa, "text"),
            GetSQLValueString($datos_empresa['idempresa'], "int"));
          $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

          //echo "cont: $cont | id($datos_empresa[idempresa]): $estatusPagina<br>";
        }      
      }/*********************************** TERMINA ESTATUS_EMPRESA(SITUACIÓN) ****************************************************/


      if(isset($_POST['estatusPagina'.$datos_empresa['idempresa']])){/*********************************** INICIA ESTATUS PAGINA DEL empresa ******************/
        $estatusPagina = $_POST['estatusPagina'.$datos_empresa['idempresa']];

        if(!empty($estatusPagina)){
          $query = "UPDATE empresa SET estatusPagina = $estatusPagina WHERE idempresa = $datos_empresa[idempresa]";
          $ejecutar = mysql_query($query,$dspp) or die(mysql_error());

          //echo "cont: $cont | id($datos_empresa[idempresa]): $estatusPagina<br>";
        }      
      }/*********************************** TERMINA ESTATUS PAGINA DEL empresa ****************************************************/


      if(isset($_POST['estatus_interno'.$datos_empresa['idempresa']])){/*********************************** INICIA ESTATUS INTERNO DEL empresa ******************/
        $estatus_interno = $_POST['estatus_interno'.$datos_empresa['idempresa']];

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

          $updateSQL = sprintf("UPDATE empresa SET estatus_interno = %s, estatus_publico = %s WHERE idempresa = %s",
            GetSQLValueString($estatus_interno, "int"),
            GetSQLValueString($estatus_publico, "int"),
            GetSQLValueString($datos_empresa['idempresa'], "int"));
          $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());


          /*$queryPagina = "UPDATE empresa SET estatusPagina = $estatusPagina WHERE idempresa = $datos_empresa[idempresa]";
          $ejecutar = mysql_query($queryPagina,$dspp) or die(mysql_error());
          //echo "cont: $cont | id($datos_empresa[idempresa]): $estatusInterno<br>";*/
        }      



      }/*********************************** TERMINA ESTATUS INTERNO DEL empresa ****************************************************/



      if(isset($_POST['estatusPublico'.$datos_empresa['idempresa']])){/*********************************** INICIA ESTATUS PUBLICO DEL empresa ******************/
        $estatusPublico = $_POST['estatusPublico'.$datos_empresa['idempresa']];

        if(!empty($estatusPublico)){

          $query = "UPDATE empresa SET estatusPublico = $estatusPublico, estatusPublico = $estatusPublico WHERE idempresa = $datos_empresa[idempresa]";
          $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
          /*$queryPagina = "UPDATE empresa SET estatusPagina = $estatusPagina WHERE idempresa = $datos_empresa[idempresa]";
          $ejecutar = mysql_query($queryPagina,$dspp) or die(mysql_error());
          //echo "cont: $cont | id($datos_empresa[idempresa]): $estatusInterno<br>";*/
        }      



      }/*********************************** TERMINA ESTATUS PUBLICO DEL empresa ****************************************************/




      if(isset($_POST['spp'.$datos_empresa['idempresa']])){/*********************************** INICIA NUMERO #SPP DEL empresa ******************/
        $spp = $_POST['spp'.$datos_empresa['idempresa']];

        if(!empty($spp)){
          $updateSQL = sprintf("UPDATE empresa SET spp = %s WHERE idempresa = %s",
            GetSQLValueString($spp, "text"),
            GetSQLValueString($datos_empresa['idempresa'], "int"));
          $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

        }      
      }/*********************************** TERMINA NUMERO #SPP DEL empresa ****************************************************/


      if(isset($_POST['maquilador'.$datos_empresa['idempresa']]) || isset($_POST['comprador'.$datos_empresa['idempresa']])  ||  isset($_POST['intermediario'.$datos_empresa['idempresa']])    ){ //********************************** INICIA LA ASIGNACION DE OC ***********************************/


        if(!empty($_POST['maquilador'.$datos_empresa['idempresa']])){
          $maquilador = 1;
        }else{
          $maquilador = 0;
        }
        if(!empty($_POST['comprador'.$datos_empresa['idempresa']])){
          $comprador = 1;
        }else{
          $comprador = 0;
        }
        if(!empty($_POST['intermediario'.$datos_empresa['idempresa']])){
          $intermediario = 1;
        }else{
          $intermediario = 0;
        }


        $updateSQL = sprintf("UPDATE empresa SET maquilador = %s, comprador = %s, intermediario = %s WHERE idempresa = %s",
          GetSQLValueString($maquilador, "int"),
          GetSQLValueString($comprador, "int"),
          GetSQLValueString($intermediario, "int"),
          GetSQLValueString($datos_empresa['idempresa'], "int"));
        $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

       
       /* if(!empty($maquilador)){
          $updateSQL = sprintf("UPDATE empresa SET maquilador = %s WHERE idempresa = %s",
            GetSQLValueString($maquilador, "int"),
            GetSQLValueString($datos_empresa['idempresa'], "int"));
          $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());
        }*/

      } //********************************** TERMINA LA ASIGNACION DE OC ***********************************/






      if(isset($_POST['vigencia_fin'.$datos_empresa['idempresa']])){ /****************** INICIA VIGENCIA FIN DEL CERTIFICADO ******************/
        $vigencia_fin = $_POST['vigencia_fin'.$datos_empresa['idempresa']];
        $timeActual = time();

        $timeVencimiento = strtotime($vigencia_fin);
        $timeRestante = ($timeVencimiento - $timeActual);
        $estatus_certificado = "";
        $plazo = 60 *(24*60*60);
        $plazoDespues = ($timeVencimiento - $plazo);
        $prorroga = ($timeVencimiento + $plazo);
            // Calculamos el número de segundos que tienen 60 días

        if(!empty($vigencia_fin)){ // NO SE INGRESO NINGUNA FECHA

          $row_certificado = mysql_query("SELECT * FROM certificado WHERE idempresa = '$datos_empresa[idempresa]'", $dspp) or die(mysql_error()); // CONSULTO SI EL empresa CUENTA CON ALGUN REGISTRO DE CERTIFICADO
          $totalCertificado = mysql_num_rows($row_certificado);
          
          if(!empty($totalCertificado)){ // SI CUENTA CON UN REGISTRO, ACTUALIZO EL MISMO
            //$query = "UPDATE certificado SET vigenciafin = '$vigenciafin' WHERE idempresa = $datos_empresa[idempresa]";
            //$ejecutar = mysql_query($query,$dspp) or die(mysql_error());

            /*********************************** INICIA, CALCULAMOS FECHAS PARA ASIGNAR EL ESTATUS DEL CERTIFICADO Y DEL empresa ***********************************************/

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

            $updateSQL = sprintf("UPDATE empresa SET estatus_publico = %s, estatus_dspp = %s WHERE idempresa = %s",
              GetSQLValueString($estatus_publico, "int"),
              GetSQLValueString($estatus_certificado, "int"),
              GetSQLValueString($datos_empresa['idempresa'], "int"));
            $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());

            $updateSQL = sprintf("UPDATE certificado SET estatus_certificado = %s, vigencia_fin = %s, entidad = %s WHERE idempresa = %s",
              GetSQLValueString($estatus_certificado, "int"),
              GetSQLValueString($vigencia_fin, "text"),
              GetSQLValueString($idoc, "int"),
              GetSQLValueString($datos_empresa['idempresa'], "int"));
            $actualizar = mysql_query($updateSQL, $dspp) or die(mysql_error());


              //$actualizar = "UPDATE certificado SET status = '16' WHERE idcertificado = $datos_empresa[idcertificado]";
              //$ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());
            
            /*********************************** FIN, CALCULAMOS FECHAS PARA ASIGNAR EL ESTATUS DEL CERTIFICADO Y DEL empresa ***********************************************/

          }else{ // SI NO CUENTA CON REGISTRO PREVIO, ENTONCES INSERTO UN NUEVO REGISTRO
            //$query = "INSERT INTO certificado(vigenciafin,idempresa) VALUES('$vigenciafin',$datos_empresa[idempresa])";
            //$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
            /*********************************** INICIA, CALCULAMOS FECHAS PARA ASIGNAR EL ESTATUS DEL CERTIFICADO Y DEL empresa ***********************************************/

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

              $updateSQL = sprintf("UPDATE empresa SET estatus_publico = %s, estatus_dspp = %s WHERE idempresa = %s",
                GetSQLValueString($estatus_publico, "int"),
                GetSQLValueString($estatus_certificado, "int"),
                GetSQLValueString($datos_empresa['idempresa'], "int"));
              $actualizar = mysql_query($updateSQL,$dspp) or die(mysql_error());

              $insertSQL = sprintf("INSERT INTO certificado (idempresa, estatus_certificado, vigencia_fin) VALUES (%s, %s, %s, %s)",
                GetSQLValueString($datos_empresa['idempresa'], "int"),
                GetSQLValueString($estatus_certificado, "int"),
                GetSQLValueString($vigencia_fin, "text"));
              $insertar = mysql_query($insertSQL, $dspp) or die(mysql_error());

              //$actualizar = "UPDATE certificado SET status = '16' WHERE idcertificado = $datos_empresa[idcertificado]";
              //$ejecutar = mysql_query($actualizar,$dspp) or die(mysql_error());
            
            /*********************************** FIN, CALCULAMOS FECHAS PARA ASIGNAR EL ESTATUS DEL CERTIFICADO Y DEL empresa ***********************************************/


          }

          //echo "cont: $cont | VIGENCIA FIN($datos_empresa[idempresa]): $vigenciafin :TOTAL Certificado: $totalCertificado<br>";
        }      
      }/********************* TERMINA VIGENCIA FIN DEL CERTIFICADO ****/


      if(isset($_POST['idoc'.$datos_empresa['idempresa']])){ //********************************** INICIA LA ASIGNACION DE OC ***********************************/
        $idoc = $_POST['idoc'.$datos_empresa['idempresa']];
        if(!empty($idoc)){
          $update = "UPDATE empresa SET idoc = '$idoc' WHERE idempresa = '$datos_empresa[idempresa]'";
          $ejecutar = mysql_query($update,$dspp) or die(mysql_error());
        }
      } //********************************** TERMINA LA ASIGNACION DE OC ***********************************/

      $cont++;
    }
    
    //echo '<script>location.href="?EMPRESAS&select";</script>';
  } /* TERMINA BOTON ACTUALIZAR LISTA empresa*/



if(isset($_GET['query'])){
  $query_empresa = "SELECT empresa.idempresa, empresa.idoc, empresa.spp AS 'spp_empresa', empresa.nombre, empresa.abreviacion AS 'abreviacion_empresa', empresa.pais, empresa.maquilador, empresa.comprador, empresa.intermediario, empresa.email, empresa.telefono, empresa.sitio_web, empresa.estatus_empresa, empresa.estatus_publico, empresa.estatus_interno, empresa.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp' FROM empresa LEFT JOIN oc ON empresa.idoc = oc.idoc LEFT JOIN estatus_publico ON empresa.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON empresa.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON empresa.estatus_dspp = estatus_dspp.idestatus_dspp WHERE empresa.idoc = $_GET[query] ORDER BY empresa.abreviacion ASC";

  $queryExportar = "SELECT empresa.*, contacto.*  FROM empresa LEFT JOIN contacto ON empresa.idempresa = contacto.idempresa WHERE idoc = $_GET[query] AND (empresa.estado IS NULL OR empresa.estado != 'ARCHIVADO') ORDER BY empresa.idempresa ASC";



}else if(isset($_POST['busqueda_palabra']) && $_POST['busqueda_palabra'] == "1"){
  $palabraClave = $_POST['palabra'];

  //$query_empresa = "SELECT *, empresa.idempresa AS 'idempresa' ,empresa.nombre AS 'nombreempresa', empresa.estado AS 'estadoempresa' , empresa.estatusPagina, status.idstatus, status.nombre AS 'nombreStatus', certificado.idcertificado, certificado.vigenciainicio, certificado.vigenciafin, status_pagina.nombre AS 'nombreEstatusPagina', status_publico.nombre AS 'nombreEstatusPublico' FROM empresa LEFT JOIN status ON empresa.estado = status.idstatus LEFT JOIN status_pagina ON empresa.estatusPagina = status_pagina.idEstatusPagina LEFT JOIN status_publico ON empresa.estatusPublico = status_publico.idstatus_publico WHERE (empresa.estado != 'ARCHIVADO' OR empresa.estado IS NULL) AND ((idf LIKE '%$palabraClave%') OR (empresa.nombre LIKE '%$palabraClave%') OR (empresa.abreviacion LIKE '%$palabraClave%') OR (sitio_web LIKE '%$palabraClave%') OR (email LIKE '%$palabraClave%') OR (pais LIKE '%$palabraClave%') OR (razon_social LIKE '%$palabraClave%') OR (direccion_fiscal LIKE '%$palabraClave%') OR (rfc LIKE '%$palabraClave%')) ORDER BY empresa.idempresa ASC";

  $query_empresa = "SELECT empresa.idempresa, empresa.idoc, empresa.spp AS 'spp_empresa', empresa.nombre, empresa.abreviacion AS 'abreviacion_empresa', empresa.pais, empresa.maquilador, empresa.comprador, empresa.intermediario, empresa.email, empresa.telefono, empresa.sitio_web, empresa.estatus_empresa, empresa.estatus_publico, empresa.estatus_interno, empresa.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp' FROM empresa LEFT JOIN oc ON empresa.idoc = oc.idoc LEFT JOIN estatus_publico ON empresa.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON empresa.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON empresa.estatus_dspp = estatus_dspp.idestatus_dspp WHERE empresa.spp LIKE '%$palabraClave%' OR empresa.nombre LIKE '%$palabraClave%' OR empresa.abreviacion LIKE '%$palabraClave%' ORDER BY empresa.abreviacion ASC";


  $queryExportar = "SELECT empresa.*, contacto.*  FROM empresa LEFT JOIN contacto ON empresa.idempresa = contacto.idempresa WHERE (empresa.estado != 'ARCHIVADO' OR empresa.estado IS NULL) AND ((empresa.idf LIKE '%$palabraClave%') OR (empresa.nombre LIKE '%$palabraClave%') OR (empresa.abreviacion LIKE '%$palabraClave%') OR (sitio_web LIKE '%$palabraClave%') OR (email LIKE '%$palabraClave%') OR (pais LIKE '%$palabraClave%') OR (razon_social LIKE '%$palabraClave%') OR (direccion_fiscal LIKE '%$palabraClave%') OR (rfc LIKE '%$palabraClave%')) ORDER BY empresa.idempresa ASC";



}else if(isset($_POST['busqueda_filtros']) && $_POST['busqueda_filtros'] == 1){
  $idoc = $_POST['buscar_oc'];
  $pais = $_POST['buscar_pais'];
  $estatus = $_POST['buscar_estatus'];
  $producto = $_POST['buscar_producto'];
  $idempresa_producto = '';

  if(!empty($idoc) && !empty($pais) && !empty($producto) && !empty($estatus)){


    $query_productos = mysql_query("SELECT empresa.idempresa, productos.producto FROM empresa LEFT JOIN productos ON empresa.idempresa = productos.idempresa WHERE empresa.idoc = $idoc AND empresa.pais = '$pais' AND empresa.estatus_dspp = $estatus AND producto_general LIKE '%$producto%' GROUP BY idempresa", $dspp) or die(mysql_error());
    $total_idempresa = mysql_num_rows($query_productos);
    $cont_idempresa = 1;
    while($producto_empresa = mysql_fetch_assoc($query_productos)){
      $idempresa_producto .= "empresa.idempresa = '$producto_empresa[idempresa]'";
      if($cont_idempresa < $total_idempresa){
        $idempresa_producto .= " OR ";
      }
      $cont_idempresa++;
    }

    $query_empresa = "SELECT empresa.idempresa, empresa.idoc, empresa.spp AS 'spp_empresa', empresa.nombre, empresa.abreviacion AS 'abreviacion_empresa', empresa.pais, empresa.maquilador, empresa.comprador, empresa.intermediario, empresa.email, empresa.telefono, empresa.sitio_web, empresa.estatus_empresa, empresa.estatus_publico, empresa.estatus_interno, empresa.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', MAX(certificado.idcertificado) AS 'idcertificado', MAX(certificado.vigencia_fin) AS 'fecha_fin', MAX(certificado.vigencia_inicio) AS 'fecha_inicio' FROM empresa LEFT JOIN oc ON empresa.idoc = oc.idoc LEFT JOIN estatus_publico ON empresa.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON empresa.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON empresa.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN certificado ON empresa.idempresa = certificado.idempresa WHERE $idempresa_producto GROUP BY empresa.idempresa ORDER BY empresa.abreviacion ASC";


  }else if(!empty($idoc) && !empty($pais) && !empty($estatus) && empty($producto)){

    $query_empresa = "SELECT empresa.idempresa, empresa.idoc, empresa.spp AS 'spp_empresa', empresa.nombre, empresa.abreviacion AS 'abreviacion_empresa', empresa.pais, empresa.maquilador, empresa.comprador, empresa.intermediario, empresa.email, empresa.telefono, empresa.sitio_web, empresa.estatus_empresa, empresa.estatus_publico, empresa.estatus_interno, empresa.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', MAX(certificado.idcertificado) AS 'idcertificado', MAX(certificado.vigencia_fin) AS 'fecha_fin', MAX(certificado.vigencia_inicio) AS 'fecha_inicio' FROM empresa LEFT JOIN oc ON empresa.idoc = oc.idoc LEFT JOIN estatus_publico ON empresa.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON empresa.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON empresa.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN certificado ON empresa.idempresa = certificado.idempresa WHERE empresa.idoc = $idoc AND empresa.pais = '$pais' AND empresa.estatus_dspp = $estatus GROUP BY empresa.idempresa ORDER BY empresa.abreviacion ASC";


  }else if(!empty($idoc) && !empty($pais) && empty($estatus) && empty($producto)){ 

    $query_empresa = "SELECT empresa.idempresa, empresa.idoc, empresa.spp AS 'spp_empresa', empresa.nombre, empresa.abreviacion AS 'abreviacion_empresa', empresa.pais, empresa.maquilador, empresa.comprador, empresa.intermediario, empresa.email, empresa.telefono, empresa.sitio_web, empresa.estatus_empresa, empresa.estatus_publico, empresa.estatus_interno, empresa.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', MAX(certificado.idcertificado) AS 'idcertificado', MAX(certificado.vigencia_fin) AS 'fecha_fin', MAX(certificado.vigencia_inicio) AS 'fecha_inicio' FROM empresa LEFT JOIN oc ON empresa.idoc = oc.idoc LEFT JOIN estatus_publico ON empresa.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON empresa.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON empresa.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN certificado ON empresa.idempresa = certificado.idempresa WHERE empresa.idoc = $idoc AND empresa.pais = '$pais' GROUP BY empresa.idempresa ORDER BY empresa.abreviacion ASC";


  }else if(!empty($idoc) && empty($pais) && empty($estatus) && empty($producto)){

    $query_empresa = "SELECT empresa.idempresa, empresa.idoc, empresa.spp AS 'spp_empresa', empresa.nombre, empresa.abreviacion AS 'abreviacion_empresa', empresa.pais, empresa.maquilador, empresa.comprador, empresa.intermediario, empresa.email, empresa.telefono, empresa.sitio_web, empresa.estatus_empresa, empresa.estatus_publico, empresa.estatus_interno, empresa.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', MAX(certificado.idcertificado) AS 'idcertificado', MAX(certificado.vigencia_fin) AS 'fecha_fin', MAX(certificado.vigencia_inicio) AS 'fecha_inicio' FROM empresa LEFT JOIN oc ON empresa.idoc = oc.idoc LEFT JOIN estatus_publico ON empresa.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON empresa.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON empresa.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN certificado ON empresa.idempresa = certificado.idempresa WHERE empresa.idoc = $idoc GROUP BY empresa.idempresa ORDER BY empresa.abreviacion ASC";


  }else if(empty($idoc) && !empty($pais) && empty($estatus) && empty($producto)){

    $query_empresa = "SELECT empresa.idempresa, empresa.idoc, empresa.spp AS 'spp_empresa', empresa.nombre, empresa.abreviacion AS 'abreviacion_empresa', empresa.pais, empresa.maquilador, empresa.comprador, empresa.intermediario, empresa.email, empresa.telefono, empresa.sitio_web, empresa.estatus_empresa, empresa.estatus_publico, empresa.estatus_interno, empresa.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', MAX(certificado.idcertificado) AS 'idcertificado', MAX(certificado.vigencia_fin) AS 'fecha_fin', MAX(certificado.vigencia_inicio) AS 'fecha_inicio' FROM empresa LEFT JOIN oc ON empresa.idoc = oc.idoc LEFT JOIN estatus_publico ON empresa.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON empresa.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON empresa.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN certificado ON empresa.idempresa = certificado.idempresa WHERE empresa.pais = '$pais' GROUP BY empresa.idempresa ORDER BY empresa.abreviacion ASC";
  }else if(empty($idoc) && empty($pais) && empty($estatus) && !empty($producto)){
    $query_productos = mysql_query("SELECT empresa.idempresa, productos.producto FROM empresa LEFT JOIN productos ON empresa.idempresa = productos.idempresa WHERE producto_general LIKE '%$producto%' GROUP BY idempresa", $dspp) or die(mysql_error());
    $total_idempresa = mysql_num_rows($query_productos);
    $cont_idempresa = 1;
    while($producto_empresa = mysql_fetch_assoc($query_productos)){
      $idempresa_producto .= "empresa.idempresa = '$producto_empresa[idempresa]'";
      if($cont_idempresa < $total_idempresa){
        $idempresa_producto .= " OR ";
      }
      $cont_idempresa++;
    }

    $query_empresa = "SELECT empresa.idempresa, empresa.idoc, empresa.spp AS 'spp_empresa', empresa.nombre, empresa.abreviacion AS 'abreviacion_empresa', empresa.pais, empresa.maquilador, empresa.comprador, empresa.intermediario, empresa.email, empresa.telefono, empresa.sitio_web, empresa.estatus_empresa, empresa.estatus_publico, empresa.estatus_interno, empresa.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', MAX(certificado.idcertificado) AS 'idcertificado', MAX(certificado.vigencia_fin) AS 'fecha_fin', MAX(certificado.vigencia_inicio) AS 'fecha_inicio' FROM empresa LEFT JOIN oc ON empresa.idoc = oc.idoc LEFT JOIN estatus_publico ON empresa.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON empresa.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON empresa.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN certificado ON empresa.idempresa = certificado.idempresa WHERE $idempresa_producto GROUP BY empresa.idempresa ORDER BY empresa.abreviacion ASC";


  }else if(empty($idoc) && !empty($pais) && empty($estatus) && !empty($producto)){
    $query_productos = mysql_query("SELECT empresa.idempresa, productos.producto FROM empresa LEFT JOIN productos ON empresa.idempresa = productos.idempresa WHERE empresa.pais = '$pais' AND producto_general LIKE '%$producto%' GROUP BY idempresa", $dspp) or die(mysql_error());
    $total_idempresa = mysql_num_rows($query_productos);
    $cont_idempresa = 1;
    while($producto_empresa = mysql_fetch_assoc($query_productos)){
      $idempresa_producto .= "empresa.idempresa = '$producto_empresa[idempresa]'";
      if($cont_idempresa < $total_idempresa){
        $idempresa_producto .= " OR ";
      }
      $cont_idempresa++;
    }


    $query_empresa = "SELECT empresa.idempresa, empresa.idoc, empresa.spp AS 'spp_empresa', empresa.nombre, empresa.abreviacion AS 'abreviacion_empresa', empresa.pais, empresa.maquilador, empresa.comprador, empresa.intermediario, empresa.email, empresa.telefono, empresa.sitio_web, empresa.estatus_empresa, empresa.estatus_publico, empresa.estatus_interno, empresa.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', MAX(certificado.idcertificado) AS 'idcertificado', MAX(certificado.vigencia_fin) AS 'fecha_fin', MAX(certificado.vigencia_inicio) AS 'fecha_inicio' FROM empresa LEFT JOIN oc ON empresa.idoc = oc.idoc LEFT JOIN estatus_publico ON empresa.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON empresa.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON empresa.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN certificado ON empresa.idempresa = certificado.idempresa WHERE $idempresa_producto GROUP BY empresa.idempresa ORDER BY empresa.abreviacion ASC";


  }else if(empty($idoc) && empty($pais) && !empty($estatus) && empty($producto)){

    $query_empresa = "SELECT empresa.idempresa, empresa.idoc, empresa.spp AS 'spp_empresa', empresa.nombre, empresa.abreviacion AS 'abreviacion_empresa', empresa.pais, empresa.maquilador, empresa.comprador, empresa.intermediario, empresa.email, empresa.telefono, empresa.sitio_web, empresa.estatus_empresa, empresa.estatus_publico, empresa.estatus_interno, empresa.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', MAX(certificado.idcertificado) AS 'idcertificado', MAX(certificado.vigencia_fin) AS 'fecha_fin', MAX(certificado.vigencia_inicio) AS 'fecha_inicio' FROM empresa LEFT JOIN oc ON empresa.idoc = oc.idoc LEFT JOIN estatus_publico ON empresa.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON empresa.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON empresa.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN certificado ON empresa.idempresa = certificado.idempresa WHERE empresa.estatus_dspp = $estatus GROUP BY empresa.idempresa ORDER BY empresa.abreviacion ASC";


  }else{
    $query_empresa = "SELECT empresa.idempresa, empresa.idoc, empresa.spp AS 'spp_empresa', empresa.nombre, empresa.abreviacion AS 'abreviacion_empresa', empresa.pais, empresa.maquilador, empresa.comprador, empresa.intermediario, empresa.email, empresa.telefono, empresa.sitio_web, empresa.estatus_empresa, empresa.estatus_publico, empresa.estatus_interno, empresa.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', MAX(certificado.idcertificado) AS 'idcertificado', MAX(certificado.vigencia_fin) AS 'fecha_fin', MAX(certificado.vigencia_inicio) AS 'fecha_inicio' FROM empresa LEFT JOIN oc ON empresa.idoc = oc.idoc LEFT JOIN estatus_publico ON empresa.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON empresa.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON empresa.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN certificado ON empresa.idempresa = certificado.idempresa GROUP BY empresa.idempresa ORDER BY empresa.abreviacion ASC";
  }


}else{
  $query_empresa = "SELECT empresa.idempresa, empresa.idoc, empresa.spp AS 'spp_empresa', empresa.nombre, empresa.abreviacion AS 'abreviacion_empresa', empresa.pais, empresa.maquilador, empresa.comprador, empresa.intermediario, empresa.email, empresa.telefono, empresa.sitio_web, empresa.estatus_empresa, empresa.estatus_publico, empresa.estatus_interno, empresa.estatus_dspp, oc.idoc, oc.abreviacion AS 'abreviacion_oc', estatus_publico.nombre AS 'nombre_publico', estatus_interno.nombre 'nombre_interno', estatus_dspp.nombre 'nombre_dspp', MAX(certificado.idcertificado) AS 'idcertificado', MAX(certificado.vigencia_fin) AS 'fecha_fin', MAX(certificado.vigencia_inicio) AS 'fecha_inicio' FROM empresa LEFT JOIN oc ON empresa.idoc = oc.idoc LEFT JOIN estatus_publico ON empresa.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_interno ON empresa.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN estatus_dspp ON empresa.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN certificado ON empresa.idempresa = certificado.idempresa GROUP BY empresa.idempresa ORDER BY empresa.abreviacion ASC";

  //$query_empresa = "SELECT empresa.idempresa, empresa.idoc, empresa.spp AS 'spp_empresa', empresa.nombre, empresa.abreviacion, empresa.pais, empresa.estatus_publico, empresa.estatus_dspp, estatus_publico.nombre AS 'nombre_publico', estatus_dspp.nombre AS 'nombre_dspp' FROM empresa LEFT JOIN oc ON empresa.idoc = oc.idoc LEFT JOIN estatus_publico ON empresa.estatus_publico = estatus_publico.idestatus_publico LEFT JOIN estatus_dspp ON empresa.estatus_dspp = estatus_dspp.idestatus_dspp";


  //$query_empresa = "SELECT *, empresa.idempresa AS 'idempresa', empresa.nombre AS 'nombreempresa', empresa.estado AS 'estadoempresa', empresa.estatusPagina, status.idstatus, status.nombre AS 'nombreStatus', certificado.idcertificado, certificado.vigenciainicio, certificado.vigenciafin, status_pagina.nombre AS 'nombreEstatusPagina', status_publico.nombre AS 'nombreEstatusPublico' FROM empresa LEFT JOIN status ON empresa.estado = status.idstatus LEFT JOIN status_pagina ON empresa.estatusPagina = status_pagina.idEstatusPagina LEFT JOIN status_publico ON empresa.estatusPublico = status_publico.idstatus_publico WHERE (empresa.estado IS NULL) OR (empresa.estado != 'ARCHIVADO') ORDER BY empresa.idempresa ASC";
  $queryExportar = "SELECT empresa.*, contacto.*  FROM empresa LEFT JOIN contacto ON empresa.idempresa = contacto.idempresa WHERE (empresa.estado IS NULL) OR (empresa.estado != 'ARCHIVADO') ORDER BY empresa.idempresa ASC";

}

$query_limit_empresa = sprintf("%s LIMIT %d, %d", $query_empresa, $startRow_empresa, $maxRows_empresa);
$empresa = mysql_query($query_limit_empresa, $dspp) or die(mysql_error());



if (isset($_GET['totalRows_empresa'])) {
  $totalRows_empresa = $_GET['totalRows_empresa'];
} else {
  $all_empresa = mysql_query($query_empresa);
  $totalRows_empresa = mysql_num_rows($all_empresa);
}
$totalPages_empresa = ceil($totalRows_empresa/$maxRows_empresa)-1;

$queryString_empresa = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_empresa") == false && 
        stristr($param, "totalRows_empresa") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_empresa = "&" . htmlentities(implode("&", $newParams));
  }
}


$queryString_empresa = sprintf("&totalRows_empresa=%d%s", $totalRows_empresa, $queryString_empresa);




  $rowempresa = mysql_query("SELECT * FROM empresa",$dspp) or die(mysql_error());
    $estatus_publico = "";


  /*while ($actualizarempresa = mysql_fetch_assoc($rowempresa)) {

    if($actualizarempresa['estatus_interno'] == 10){ //ESTATUS PAGINA = CERTIFICADO(REGISTRADO)
      $estatus_publico = 2;
    }else if($actualizarempresa['estatus_interno'] == 14 || $actualizarempresa['estatus_interno'] == 24){ // ESTATUS PAGINA = CANCELADO
      $estatus_publico = 3;
    }else{ // ESTATUS PAGINA = EN REVISION
      $estatus_publico = 1;
    }
      
    $query = "UPDATE empresa SET estatus_publico = $estatus_publico WHERE idempresa = $actualizarempresa[idempresa]";
    $ejecutar = mysql_query($query,$dspp) or die(mysql_error());

  }*/

  $detalle_empresa = mysql_query($query_empresa,$dspp) or die(mysql_error());
  $total_empresa = mysql_num_rows($detalle_empresa);

  $row_interno = mysql_query("SELECT * FROM estatus_interno", $dspp) or die(mysql_error());

  $row_oc = mysql_query("SELECT * FROM oc", $dspp) or die(mysql_error());
  $row_pais = mysql_query("SELECT * FROM paises", $dspp) or die(mysql_error());


  $query_productos = mysql_query("SELECT producto_general FROM productos WHERE productos.idempresa IS NOT NULL AND productos.producto_general IS NOT NULL GROUP BY producto_general ORDER BY productos.producto_general ASC",$dspp) or die(mysql_error());
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
                echo "<option value='$lista_productos[producto_general]'>$lista_productos[producto_general]</option>";
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
          <!--<a class="btn btn-sm btn-warning" href="?EMPRESA&filed">OPP(s) Archivado(s)</a>-->
          <button class="btn btn-sm btn-info" onclick="guardarDatos()"><span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Guardar Cambios</button><!-- BOTON GUARDAR DATOS -->
        </th>
        <th colspan="4">
          Exportar Lista
          <a href="#" target="_blank" onclick="document.formulario1.submit()"><img src="../../img/pdf.png"></a>
          <a href="#" onclick="document.formulario2.submit()"><img src="../../img/excel.png"></a>

          <form name="formulario1" method="POST" action="../../reportes/lista_empresas.php">
            <input type="hidden" name="lista_pdf" value="1">
            <input type="hidden" name="query_pdf" value="<?php echo $query_empresa; ?>">
          </form> 
          <form name="formulario2" method="POST" action="../../reportes/lista_empresas.php">
            <input type="hidden" name="lista_excel" value="2">
            <input type="hidden" name="query_excel" value="<?php echo $query_empresa; ?>">
          </form>

        </th>
        <th colspan="6" class="success text-center">
          <p style="font-size:12px;color:red">Total OPP(s): <?php echo $total_empresa; ?></p>
        </th>
      </tr>

      <tr>
        <th class="text-center">#SPP</th>
        <th class="text-center">Nombre</th>
        <th class="text-center">Abreviación</th>
        <th class="text-center">País</th>
        <th class="text-center">Situación Empresa</th>
        <th class="text-center">Tipo Empresa</th>
        <th class="text-center"><a href="#" data-toggle="tooltip" title="Puede ser definido por la fecha de certificado ó El Proceso de Certificación"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>Estatus Publico</a></th>
        <th class="text-center"><a href="#" data-toggle="tooltip" title="Proceso de Certificación en el que se encuentra la empresa"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>Proceso certificación</a></th>
        <th class="text-center">
          <a href="#" data-toggle="tooltip" title="Fecha en la que expira el Certificado"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>Fecha Final<br>(Certificado)</a>
        </th>
        <th class="text-center"><a href="#" data-toggle="tooltip" title="Estatus del Certificado definido por la fecha de vigencia final">
          <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>Estatus Certificado</a>
        </th>
        <th class="text-center">
          Productos
          <br>
          <a href="../../traducir_producto.php?empresa" target="ventana1" onclick="ventanaNueva ('', 500, 400, 'ventana1');"><span class="glyphicon glyphicon-book glyphicon" aria-hidden="true"></span> Traducir</a>
        </th>
        <th class="text-center">OC</th>
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
      <input type="hidden" name="actualizacion_empresa" value="1">
      <tbody style="font-size:10px">
      <?php 
      if($total_empresa == 0){
        echo "<tr><td class='text-center info' colspan='12'>No se encontraron Registros</td></tr>";
      }else{
        while($empresa = mysql_fetch_assoc($detalle_empresa)){
        ?>
          <tr>
            <td>
              <a class="btn btn-primary btn-xs" style="width:100%;font-size:10px;" href="?EMPRESAS&amp;detail&amp;idempresa=<?php echo $empresa['idempresa']; ?>&contact">Consultar<br>
              </a>
              <input type="text" name="spp<?php echo $empresa['idempresa']; ?>" value="<?php echo $empresa['spp_empresa']; ?>">
            </td>
            <td>
              <?php echo $empresa['nombre']; ?>
            </td>
            <td>
              <?php
              echo $empresa['abreviacion_empresa'];
               ?>
            </td>
            <td>
              <b style="color:#e74c3c"><?php echo $empresa['pais']; ?></b>
            </td>
            <!--- INICIA SITUACION EMPRESA ---->
            <td>
              <select name="estatus_empresa<?php  echo $empresa['idempresa']; ?>" id="">
                <option value="">...</option>
                <option value="NUEVA" <?php if($empresa['estatus_empresa'] == 'NUEVA'){ echo 'selected';} ?>>NUEVA</option>
                <option value="RENOVACION" <?php if($empresa['estatus_empresa'] == 'RENOVACION'){ echo 'selected';} ?>>RENOVACIÓN</option>
                <option value="CANCELADA" <?php if($empresa['estatus_empresa'] == 'CANCELADA'){ echo 'selected';} ?>>CANCELADA</option>
              </select>
              <?php 
              if($empresa['estatus_empresa'] == 'NUEVA'){
                echo "<p class='alert alert-success' style='font-size:10px;padding:5px;'>NUEVA</p>";
              }else if($empresa['estatus_empresa'] == 'RENOVACION'){
                echo "<p class='alert alert-warning' style='font-size:10px;padding:5px;'>RENOVACIÓN</p>";
              }else if($empresa['estatus_empresa'] == 'CANCELADA'){
                echo "<p class='alert alert-danger' style='font-size:10px;padding:5px;'>CANCELADA</p>";
              }
               ?>
            </td>
            <!--- TERMINA SITUACION EMPRESA ---->
            
            <td>
              <div class="checkbox">
                  <label>
                    <input type="checkbox" name="maquilador<?php echo $empresa['idempresa']; ?>" value="1" <?php if($empresa['maquilador']){echo "checked"; } ?>> MAQUILADOR
                  </label>

                  <label>
                    <input type="checkbox" name="comprador<?php echo $empresa['idempresa']; ?>" value="1" <?php if($empresa['comprador']){echo "checked"; } ?>> COMPRADOR
                  </label>


                  <label>
                    <input type="checkbox" name="intermediario<?php echo $empresa['idempresa']; ?>" value="1" <?php if($empresa['intermediario']){echo "checked"; } ?>> INTERMEDIARIO
                  </label>

                </div>
            </td>

            <td>
              <?php 
                echo $empresa['nombre_publico']; 
              ?>
            </td>
            <td>
              <select name="estatus_interno<?php echo $empresa['idempresa']; ?>">
                <option>...</option>
                <?php 
                $row_interno = mysql_query("SELECT * FROM estatus_interno", $dspp) or die(mysql_error());
                while($estatus_interno = mysql_fetch_assoc($row_interno)){
                ?>
                  <option value="<?php echo $estatus_interno['idestatus_interno'] ?>" <?php if($estatus_interno['idestatus_interno'] == $empresa['estatus_interno']){echo "selected";} ?>><?php echo $estatus_interno['nombre']; ?></option>
                <?php
                }
                 ?>
              </select>
              <?php echo "<p class='alert alert-info' style='padding:7px;'>$empresa[nombre_interno]</p>"; ?>
            </td>

            <!-- INICIA SECCION VIGENCIA DEL CERTIFICADO -->
            <?php 
           // $row_certificado = mysql_query("SELECT * FROM certificado WHERE idempresa = $empresa[idempresa] ORDER BY certificado.vigencia_fin DESC LIMIT 1", $dspp) or die(mysql_error());
            //$certificado = mysql_fetch_assoc($row_certificado);

             $queryCertificado = mysql_query("SELECT idcertificado, vigencia_inicio, vigencia_fin, archivo FROM certificado WHERE idcertificado = (SELECT MAX(idcertificado) FROM certificado WHERE idempresa = '$empresa[idempresa]')", $dspp) or die(mysql_error());
              $certificado = mysql_fetch_assoc($queryCertificado);
            ?>
            <td>
              <?php 
            $vigenciainicio = '';
            $vigenciafin = '';
            if(!empty($certificado['vigencia_inicio'])){
              $vigenciainicio = date('d-m-Y', strtotime($certificado['vigencia_inicio']));
            }
            if(!empty($certificado['vigencia_fin'])){
              $vigenciafin = date('d-m-Y', strtotime($certificado['vigencia_fin']));
            }

            $timeVencimiento = strtotime($certificado['vigencia_fin']);
          
            if(isset($vigenciainicio)){
              echo $vigenciainicio;
            }

               ?>
              <input type="date" name="vigencia_fin<?php echo $empresa['idempresa']; ?>" value="<?php echo $certificado['vigencia_fin']; ?>">
            </td>
            <!-- TERMINA SECCION VIGENCIA DEL CERTIFICADO -->

            <?php 
            if(isset($certificado['idcertificado'])){

              $estatus_certificado = mysql_query("SELECT idcertificado, estatus_certificado, estatus_dspp.nombre FROM certificado LEFT JOIN estatus_dspp ON certificado.estatus_certificado = estatus_dspp.idestatus_dspp WHERE idcertificado = $certificado[idcertificado]", $dspp) or die(mysql_error());
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
              //echo $empresa['estatus_certificado'];
             ?>
            <td>
              <?php 
              $row_productos = mysql_query("SELECT * FROM productos WHERE idempresa = $empresa[idempresa] GROUP BY productos.producto", $dspp) or die(mysql_error());
              $total_productos = mysql_num_rows($row_productos);
              ?>
              <a style="font-size:14px;" href="../../agregar_producto.php?idempresa=<?php echo $empresa['idempresa']; ?>" target="ventana1" onclick="ventanaNueva ('', 500, 400, 'ventana1');"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span></a>
              <?php
              if($total_productos == 0){
              ?>
               No Disponible
              <?php
              }
              $contador = 1;
              $total = mysql_num_rows($row_productos);
              while($productos = mysql_fetch_assoc($row_productos)){
                if($contador < $total){
                  echo strtoupper($productos['producto']) .", ";
                }else{
                  echo strtoupper($productos['producto']);
                }
                $contador++;
              }
               ?>
            </td>

            <td>
              <?php 
              $row_oc = mysql_query("SELECT * FROM oc", $dspp) or die(mysql_error());
              ?>
              <select name="idoc<?php echo $empresa['idempresa']; ?>" id="">
                <option value="">...</option>
                <?php 
                while($oc = mysql_fetch_assoc($row_oc)){
                ?>
                  <option value="<?php echo $oc['idoc']; ?>" <?php if($oc['idoc'] == $empresa['idoc']){echo "selected"; } ?>><?php echo $oc['abreviacion']; ?></option>
                <?php
                }
                 ?>
              </select>
              <?php 
               if(!empty($empresa['abreviacion_oc'])){
                echo "<p class='alert alert-info' style='padding:5px;'>".$empresa['abreviacion_oc']."</p>";
               }
              ?>
            </td>
              <!--02/06</td>-->
              <td class="text-center">

                <div name="formulario">
                  <input type="checkbox" name="idempresaCheckbox" id="<?php echo "idempresa".$contador; ?>" value="<?php echo $empresa['idempresa']; ?>" onclick="addCheckbox()">
                </div>
              </td>
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
    <td width="20"><?php if ($pageNum_empresa > 0) { // Show if not first page ?>
    <a href="<?php printf("%s?pageNum_empresa=%d%s", $currentPage, 0, $queryString_empresa); ?>">
    <span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span>
    </a>
    <?php } // Show if not first page ?></td>
    <td width="20"><?php if ($pageNum_empresa > 0) { // Show if not first page ?>
    <a href="<?php printf("%s?pageNum_empresa=%d%s", $currentPage, max(0, $pageNum_empresa - 1), $queryString_empresa); ?>" >
    <span class="glyphicon glyphicon-backward" aria-hidden="true"></span>
    </a>
    <?php } // Show if not first page ?></td>
    <td width="20"><?php if ($pageNum_empresa < $totalPages_empresa) { // Show if not last page ?>
    <a href="<?php printf("%s?pageNum_empresa=%d%s", $currentPage, min($totalPages_empresa, $pageNum_empresa + 1), $queryString_empresa); ?>">
    <span class="glyphicon glyphicon-forward" aria-hidden="true"></span>
    </a>
    <?php } // Show if not last page ?></td>
    <td width="20"><?php if ($pageNum_empresa < $totalPages_empresa) { // Show if not last page ?>
    <a  href="<?php printf("%s?pageNum_empresa=%d%s", $currentPage, $totalPages_empresa, $queryString_empresa); ?>" >
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

<script language="JavaScript"> 

var contadorPHP = 'qwerty';
var miVariable = [];
var idempresa = '';


function addCheckbox(){
  var cont = 0;
  var checkboxidempresa = document.getElementsByName("idempresaCheckbox");
//var precio=document.getElementById('precio').value;

  for (var i=0; i<checkboxidempresa.length; i++) {
    if (checkboxidempresa[i].checked == 1) { 
      //alert("EL VALOR ES: "+checkboxidempresa[i].value); 
      //cont = cont + 1; 
      idempresa = checkboxidempresa[i].value; 
      sessionStorage[idempresa] = idempresa; 

    }

  }

  for(var i=0;i<sessionStorage.length;i++){
    var idempresa=sessionStorage.key(i);
    miVariable[i] = idempresa;
    document.cookie = 'variable='+miVariable;
  }
}



function mostrarDatos(){
  var datosDisponibles=document.getElementById('datosDisponibles');
  datosDisponibles.innerHTML='';
  for(var i=0;i<sessionStorage.length;i++){
    var idempresa=sessionStorage.key(i);
    var variablePHP = "<?php $otraVariable = 6; ?>";
    datosDisponibles.innerHTML += '<div>'+idempresa+'</div>';
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