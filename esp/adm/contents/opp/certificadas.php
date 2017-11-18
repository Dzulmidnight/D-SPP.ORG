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



<?php 
// EN PROCESO LA PRIMERA VEZ
/// SELECCIONA LAS ORGANIZACIONES("OPP") QUE TIENEN SOLICITUD NUEVA, PERO QUE AUN NO SE LES HA ASIGNADO UN DICMTANE POSITIVO
/// DEBEN DE TENER UN ESTATUS-DSPP DEL 1 al 11, o el 17
///PARA EL NUMERO DE SOCIOS TOMAMOS LA RESP1 QUE ES "NUMERO DE SOCIOS"



  /// CONSULTAMOS LAS ORGANIZACONES QUE ESTAN EN PROCESO Y POR ESO NO DEBEN ESTAR EN LAS CERTIFICADAS
  $array_opp = '';
  $array_opp2 = '';
  $consultar_numero = mysql_query("SELECT opp.idopp, opp.estatus_interno, opp.abreviacion, solicitud_certificacion.idsolicitud_certificacion, COUNT(idsolicitud_certificacion) AS 'total_solicitudes' FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE (opp.estatus_opp != 'CANCELADO' AND opp.estatus_opp != 'SUSPENDIDO' AND opp.estatus_opp != 'CERTIFICADO' AND opp.estatus_opp != 'ARCHIVADO') AND opp.estatus_opp = 0 OR opp.estatus_opp IS NULL OR opp.estatus_opp = 1 OR opp.estatus_opp = 4 GROUP BY opp.idopp ORDER BY opp.idopp", $dspp) or die(mysql_error());
  $num_registros = mysql_num_rows($consultar_numero);
  $contador = 1;
  while ($detalle_numero = mysql_fetch_assoc($consultar_numero)) {
    if($detalle_numero['total_solicitudes'] <= 1){
      if($contador < $num_registros){
        $array_opp .= 'opp.idopp(<span style="color:red">'.$detalle_numero['abreviacion'].'</span>) = '.$detalle_numero['idopp'].' OR ';
        $array_opp2 .= 'opp.idopp != '.$detalle_numero['idopp'].' AND ';
      }else{
        $array_opp .= 'opp.idopp(<span style="color:red">'.$detalle_numero['abreviacion'].'</span>) = '.$detalle_numero['idopp'];
        $array_opp2 .= 'opp.idopp != '.$detalle_numero['idopp'];
      }
    }
    $contador++;
  }


  //CONSULTAMOS LAS ORGANIZACIONES QUE ESTAN "ARCHIVADAS" Y POR ESO NO DEBEN ESTAR ENTRE LAS CERTIFICADAS
  $array_archivadas = '';
  $array_archivadas2 = '';
  $contador = 1;
  $opp_archivadas = mysql_query("SELECT opp.idopp, opp.estatus_interno, opp.abreviacion, solicitud_certificacion.idsolicitud_certificacion FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE opp.estatus_opp = 'ARCHIVADO' GROUP BY opp.idopp ORDER BY opp.nombre", $dspp) or die(mysql_error());
  $total_archivadas = mysql_num_rows($opp_archivadas);

  while($archivadas = mysql_fetch_assoc($opp_archivadas)){
    if($contador < $total_archivadas){
      $array_archivadas2 .= 'opp.idopp(<span style="color:red">'.$archivadas['abreviacion'].'</span>) = '.$archivadas['idopp'].' OR ';
      $array_archivadas .= 'opp.idopp != '.$archivadas['idopp'].' AND ';
    }else{
      $array_archivadas2 .= 'opp.idopp(<span style="color:red">'.$archivadas['abreviacion'].'</span>) = '.$archivadas['idopp'];
      $array_archivadas .= 'opp.idopp != '.$archivadas['idopp'];
    }
    $contador++;
  }

  //CONSULTAMOS LAS ORGANIZACIONES CANCELADAS
  $array_canceladas = '';
  $contador = 1;
  $query_canceladas = mysql_query("SELECT opp.idopp FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE opp.estatus_interno = 10 OR solicitud_certificacion.estatus_interno = 10 OR opp.estatus_opp = 'CANCELADO' ORDER BY opp.nombre", $dspp) or die(mysql_error());
  $total_canceladas = mysql_num_rows($query_canceladas);
  while($canceladas = mysql_fetch_assoc($query_canceladas)){
    if($contador < $total_canceladas){
      $array_canceladas .= 'opp.idopp != '.$canceladas['idopp'].' AND ';
    }else{
      $array_canceladas .= 'opp.idopp != '.$canceladas['idopp'];
    }
    $contador++;
  }
  $query_tota_opps = mysql_query("SELECT idopp FROM opp GROUP BY idopp",$dspp) or die(mysql_error());
  $total_opps = mysql_num_rows($query_tota_opps);

  

  ///// CONSULTAMOS LOS CUADROS DE BUSQUEDA -//////////////////////////
  if(isset($_POST['palabra'])){
    //// BUSQUEDA POR PALABRAS
    $palabra = $_POST['palabra'];
    $query = "SELECT opp.idopp, opp.spp, opp.email, opp.telefono, opp.password, opp.sitio_web, opp.nombre AS 'nombre_opp', opp.abreviacion AS 'abreviacion_opp', opp.pais, oc.abreviacion AS 'abreviacion_oc', opp.estatus_opp AS 'opp_estatus_opp', opp.estatus_publico AS 'opp_estatus_publico', opp.estatus_interno AS 'opp_estatus_interno', opp.estatus_dspp AS 'opp_estatus_dspp', MAX(solicitud_certificacion.idsolicitud_certificacion) AS 'idsolicitud_certificacion', solicitud_certificacion.tipo_solicitud, solicitud_certificacion.estatus_interno AS 'solicitud_estatus_interno', solicitud_certificacion.estatus_dspp AS 'solicitud_estatus_dspp', certificado.idcertificado, certificado.vigencia_inicio, certificado.vigencia_fin, certificado.archivo AS 'certificado' FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp LEFT JOIN oc ON solicitud_certificacion.idoc = oc.idoc LEFT JOIN certificado ON solicitud_certificacion.idsolicitud_certificacion = certificado.idsolicitud_certificacion WHERE $array_opp2 AND $array_archivadas AND $array_canceladas AND (opp.spp LIKE '%".$palabra."%' OR opp.nombre LIKE '%".$palabra."%' OR opp.abreviacion LIKE '%".$palabra."%') GROUP BY opp.idopp ORDER BY opp.abreviacion";
  }else if(isset($_POST['busqueda_filtros']) && $_POST['busqueda_filtros'] == 1){
    //// BUSQUEDA DE ACUERDO A LOS FILTROS AVANZADOS
    $buscar_oc = $_POST['buscar_oc'];
    $buscar_pais = $_POST['buscar_pais'];
    $buscar_producto = $_POST['buscar_producto'];
    $buscar_estatus = $_POST['buscar_estatus'];
    $productos = '';

    if(empty($buscar_oc)){
      $q_oc = '';
    }else{
      $q_oc = 'AND opp.idoc = "'.$buscar_oc.'"';
    }

    if(empty($buscar_pais)){
      $q_pais = '';
    }else{
      $q_pais = "AND opp.pais = '$buscar_pais'";
    }
    if(empty($buscar_estatus)){
      $q_estatus = '';
    }else{
      switch ($buscar_estatus) {
        case '13':
          $q_estatus = 'AND (opp.estatus_opp = 13 OR opp.estatus_opp = 14 OR opp.estatus_opp = 15)';
          break;
        case '14':
          $q_estatus = 'AND opp.estatus_opp = 14';
          break;
        case '15':
          $q_estatus = 'AND opp.estatus_opp = 15';
          break;
        case '16':
          $q_estatus = 'AND opp.estatus_opp = 16';
          break;
        default:
          $q_estatus = '';
          break;
      }
    }

    if(empty($buscar_producto)){
      $array_productos = '';
      //SELECT productos.idopp FROM productos INNER JOIN opp ON productos.idopp = opp.idopp WHERE producto_general = 'CAFE' AND opp.pais LIKE '%ecuador%' GROUP BY productos.idopp
    }else{
      $array_productos = '';
      $contador = 1;
      $consultar_productos = mysql_query("SELECT productos.idopp FROM productos INNER JOIN opp ON productos.idopp = opp.idopp WHERE producto_general LIKE '%$buscar_producto%' GROUP BY idopp", $dspp) or die(mysql_error());
      $total_productos = mysql_num_rows($consultar_productos);

      while($q_productos = mysql_fetch_assoc($consultar_productos)){
        if($contador < $total_productos){
          $array_productos .= 'opp.idopp = '.$q_productos['idopp'].' OR ';
        }else{
          $array_productos .= 'opp.idopp = '.$q_productos['idopp'];
        }
        $contador++;
      }
      if(empty($array_productos)){
        $productos = '';
      }else{
        $productos = 'AND ('.$array_productos.')';
      }
    }

    /*$estatus_membresia = $_POST['estatus_membresia'];
    if(empty($estatus_membresia)){
      $q_estatus = "";
    }else if($estatus_membresia == 'TODAS'){
      $q_estatus = "";
    }else{
      $q_estatus = "AND membresia.estatus_membresia = '".$estatus_membresia."'";
    }

    $pais_membresia = $_POST['pais_membresia'];
    if(empty($pais_membresia)){
      $q_pais = "";
    }else{
      $q_pais = "AND opp.pais = '".$pais_membresia."'";
    }

    $anio_membresia = $_POST['anio_membresia'];
    if(empty($anio_membresia)){
      $q_anio = "";
    }else if($anio_membresia == 'TODOS'){
      $q_anio = "";
    }else{
      $q_anio = "AND FROM_UNIXTIME(proceso_certificacion.fecha_registro,'%Y') = '".$anio_membresia."'";
    }*/

    $query = "SELECT opp.idopp, opp.spp, opp.email, opp.telefono, opp.password, opp.sitio_web, opp.nombre AS 'nombre_opp', opp.abreviacion AS 'abreviacion_opp', opp.pais, oc.abreviacion AS 'abreviacion_oc', opp.estatus_opp AS 'opp_estatus_opp', opp.estatus_publico AS 'opp_estatus_publico', opp.estatus_interno AS 'opp_estatus_interno', opp.estatus_dspp AS 'opp_estatus_dspp', MAX(solicitud_certificacion.idsolicitud_certificacion) AS 'idsolicitud_certificacion', solicitud_certificacion.tipo_solicitud, solicitud_certificacion.estatus_interno AS 'solicitud_estatus_interno', solicitud_certificacion.estatus_dspp AS 'solicitud_estatus_dspp', certificado.idcertificado, certificado.vigencia_inicio, certificado.vigencia_fin, certificado.archivo AS 'certificado' FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp LEFT JOIN oc ON solicitud_certificacion.idoc = oc.idoc LEFT JOIN certificado ON solicitud_certificacion.idsolicitud_certificacion = certificado.idsolicitud_certificacion WHERE $array_opp2 AND $array_archivadas AND $array_canceladas ".$q_oc." ".$q_pais." ".$q_estatus." ".$productos." GROUP BY opp.idopp ORDER BY opp.abreviacion";
  }else{
    /// CONSULTA POR DEFAULT
    $query = "SELECT opp.idopp, opp.spp, opp.email, opp.telefono, opp.password, opp.sitio_web, opp.nombre AS 'nombre_opp', opp.abreviacion AS 'abreviacion_opp', opp.pais, oc.abreviacion AS 'abreviacion_oc', opp.estatus_opp AS 'opp_estatus_opp', opp.estatus_publico AS 'opp_estatus_publico', opp.estatus_interno AS 'opp_estatus_interno', opp.estatus_dspp AS 'opp_estatus_dspp', MAX(solicitud_certificacion.idsolicitud_certificacion) AS 'idsolicitud_certificacion', solicitud_certificacion.tipo_solicitud, solicitud_certificacion.estatus_interno AS 'solicitud_estatus_interno', solicitud_certificacion.estatus_dspp AS 'solicitud_estatus_dspp', certificado.idcertificado, certificado.vigencia_inicio, certificado.vigencia_fin, certificado.archivo AS 'certificado' FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp LEFT JOIN oc ON solicitud_certificacion.idoc = oc.idoc LEFT JOIN certificado ON solicitud_certificacion.idsolicitud_certificacion = certificado.idsolicitud_certificacion WHERE $array_opp2 AND $array_archivadas AND $array_canceladas GROUP BY opp.idopp ORDER BY opp.abreviacion";
  }
  /*echo $q_estatus.'<br>';
  echo $query;*/
/*14_11_2017  echo $productos;
  echo '<hr>';
  echo $query;
  14_11_2017*/
  $consultar = mysql_query($query,$dspp) or die(mysql_error());
  $total_organizaciones = mysql_num_rows($consultar);

 ?>

<div class="row">

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


          <div class="col-xs-3">
            Organismo de Certificación
            <select name="buscar_oc" class="form-control">
              <option value=''>Selecciona un organismo de certificación</option>
              <?php 
              $row_oc = mysql_query("SELECT idoc, abreviacion FROM oc WHERE idoc != 18", $dspp) or die(mysql_error());
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
              $row_pais = mysql_query("SELECT pais FROM opp GROUP BY pais",$dspp) or die(mysql_error());
              while($pais = mysql_fetch_assoc($row_pais)){
                echo "<option value='".$pais['pais']."'>".mayuscula($pais['pais'])."</option>";
              }
               ?>
            </select>
          </div>
          <div class="col-xs-3">
            Estatus del Certificado
            <select name="buscar_estatus" class="form-control">
              <option value=''>Selecciona un estatus</option>
              <option value="13">Certificado</option>
              <option value="14">Aviso de Renovación</option>
              <option value="15">Certificado por Expirar</option>
              <option value="16">Certificado Expirado</option>
            </select>
          </div>
          <div class="col-xs-3">
            Producto
            <select class="form-control" name="buscar_producto" id="">
              <option value=''>Seleccione un producto</option>
              <?php
              $row_productos = mysql_query("SELECT producto_general FROM productos WHERE producto_general IS NOT NULL GROUP BY producto_general", $dspp) or die(mysql_error());
              while($lista_productos = mysql_fetch_assoc($row_productos)){
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
          <input type="hidden" name="query_pdf" value="<?php echo $query; ?>">
        </form> 
        <form name="formulario2" method="POST" action="../../reportes/lista_opp.php">
          <input type="hidden" name="lista_excel" value="2">
          <input type="hidden" name="query_excel" value="<?php echo $query; ?>">
        </form>
      </th>
      <th class="success text-center" colspan="6">
        NUMERO DE ORGANIZACIONES: <?php echo $total_organizaciones; ?>
      </th>
    </tr>
    <tr>
      <th style="width:20px;">#</th>
      <th>#SPP</th>
      <th style="width:200px;">ORGANIZACIÓN</th>
      <th style="width:100px;">PAÍS</th>
      <th>OC</th>

      <th>ULTIMA FECHA DE CERTIFICADO</th>
      <!--14_11_2017<th>ESTATUS OPP</th>-->
      <th>ESTATUS CERTIFICADO</th>

      
     <!--<th>ESTATUS PUBLICO</th>-->
      <!--<th>ESTATUS INTERNO</th>-->
      <!--<th>ESTATUS DSPP</th>-->
      <!--17_11_2017<th><a href="#" data-toggle="tooltip" title="Ultimo tipo de solicitud registrado"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span> TIPO SOLICITUD</a></th>-->
      <th>ESTATUS ORGANIZACIÓN</th>
      <th>ID SOLICITUD</th>
      <th>PROCESO SOLICITUD</th>

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

    <!-- CODIGO #SPP -->
    <td>
      <a class="btn btn-primary btn-xs" style="width:100%;font-size:10px;" href="?OPP&amp;detail&amp;idopp=<?php echo $informacion['idopp']; ?>">Consultar<br></a>
      <input type="text" name="spp<?php echo $informacion['idopp'];?>" value="<?php echo $informacion['spp']; ?>">
    </td>

    <!-- NOMBRE DE LA ORGANIZACION -->
    <td>
      <?php echo '<span style="color:#bdc3c7">ID: '.$informacion['idopp'].'| </span>'.mayuscula($informacion['nombre_opp']).' (<span style="color:red">'.mayuscula($informacion['abreviacion_opp']).'</span>)'; ?>
    </td>
    <!-- PAIS DE LA ORGANIZACION -->
    <td>
      <?php echo mayuscula($informacion['pais']); ?>
    </td>
    <!-- ORGANISMO DE CERTIFICACION -->
    <td>
      <?php 
      if(isset($informacion['abreviacion_oc'])){
        echo mayuscula($informacion['abreviacion_oc']); 
      }else{
        echo '<span style="color:red">NO DISPONIBLE</span>';
      }
      ?>
    </td>

    <!-- ULTIMA FECHA DE CERTIFICADO -->
    <td>
      <?php

      if(isset($informacion['vigencia_fin'])){
        echo '<p>I: '.$informacion['vigencia_inicio'].'</p>';
        echo '<p class="bg-success">F: '.$informacion['vigencia_fin'].'</p>';
      }else{
        $consulta_certificado = mysql_query("SELECT idcertificado, vigencia_inicio, vigencia_fin FROM certificado WHERE idopp = '$informacion[idopp]'", $dspp) or die(mysql_error());
        $detalle_certificado = mysql_fetch_assoc($consulta_certificado);
        if(isset($detalle_certificado['vigencia_fin'])){
          echo '<p>I: '.$detalle_certificado['vigencia_inicio'].'</p>';
          echo '<p class="bg-success">F: '.$detalle_certificado['vigencia_fin'].'</p>';
        }else{
          echo '<span style="color:red">No disponible</span>';
        }
      }
      ?>
    </td>
    <!-- ESTATUS OPP (posiblemente cambiarlo por estatus certificado) -->
    <td>
      <?php
      if($informacion['opp_estatus_opp'] != 'CERTIFICADO' && $informacion['opp_estatus_opp'] != 'CANCELADO'){
        $consultar10 = mysql_query("SELECT nombre FROM estatus_dspp WHERE idestatus_dspp = $informacion[opp_estatus_opp]", $dspp) or die(mysql_error());
        $detalle10 = mysql_fetch_assoc($consultar10);
        switch ($informacion['opp_estatus_opp']) {
          case '13': // CERTIFICADA
            //13_11_2017echo '<p class="bg-success">('.$informacion['opp_estatus_opp'].')'.mayuscula($detalle10['nombre']).'</p>';
            echo '<p class="bg-success">'.mayuscula($detalle10['nombre']).'</p>';
            break;
          case '14': // AVISO DE RENOVACIÓN DEL CERTIFICADO
            //13_11_2017echo '<p class="bg-info">('.$informacion['opp_estatus_opp'].')'.mayuscula($detalle10['nombre']).'</p>';
            echo '<p class="bg-info">'.mayuscula($detalle10['nombre']).'</p>';
            break;
          case '15': // CERTIFICADO POR EXPIRAR
            //13_11_2017echo '<p class="bg-warning">('.$informacion['opp_estatus_opp'].')'.mayuscula($detalle10['nombre']).'</p>';
            echo '<p class="bg-warning">'.mayuscula($detalle10['nombre']).'</p>';
            break;
          case '16': // CERTIFICADO EXPIRADO
            //13_11_2017echo '<p class="bg-danger">('.$informacion['opp_estatus_opp'].')'.mayuscula($detalle10['nombre']).'</p>';
            echo '<p class="bg-danger">'.mayuscula($detalle10['nombre']).'</p>';
            break;
        }
      }else{
        echo '<span style="color:red">'.mayuscula($informacion['opp_estatus_opp']).'</span>';
      }
      ?>
    </td>
    <!-- ESTATUS PUBLICO -->
    <!--13_11_2017<td>
      <?php 
        echo '<p>OPP: '.$informacion['opp_estatus_publico'].'</p>';
       ?>
    </td>
    <!-- ESTATUS INTERNO -->
    <!--<td>
      <?php
      $consultar3 = mysql_query("SELECT nombre FROM estatus_interno WHERE idestatus_interno = '$informacion[solicitud_estatus_interno]'", $dspp) or die(mysql_error());
      $detalle3 = mysql_fetch_assoc($consultar3);
        echo '<p>SOLICITUD: '.$informacion['solicitud_estatus_interno'].' - <span style="color:blue">'.$detalle3['nombre'].'</span></p>';
      $consultar4 = mysql_query("SELECT nombre FROM estatus_interno WHERE idestatus_interno = '$informacion[opp_estatus_interno]'", $dspp) or die(mysql_error());
      $detalle4 = mysql_fetch_assoc($consultar4);
        echo '<p>OPP: '.$informacion['opp_estatus_interno'].' - <span style="color:red">'.$detalle4['nombre'].'</span></p>';
       ?>
    </td>-->
    <!-- ESTATUS DSPP -->
    <!--<td>
      <?php
      $consultar5 = mysql_query("SELECT nombre FROM estatus_dspp WHERE idestatus_dspp = '$informacion[solicitud_estatus_dspp]'", $dspp) or die(mysql_error());
      $detalle5 = mysql_fetch_assoc($consultar5);
        echo '<p>SOLICITUD: '.$informacion['solicitud_estatus_dspp'].' - <span style="color:blue">'.$detalle5['nombre'].'</span></p>';
      $consultar6 = mysql_query("SELECT nombre FROM estatus_dspp WHERE idestatus_dspp = '$informacion[opp_estatus_dspp]'", $dspp) or die(mysql_error());
      $detalle7 = mysql_fetch_assoc($consultar6);
        echo '<p>OPP: '.$informacion['opp_estatus_dspp'].' - <span style="color:red">'.$detalle7['nombre'].'</span></p>';
       ?>
    </td>
    <!-- TIPO SOLICITUD -->
    <td>
      <?php
      /*$estado = mysql_query("SELECT estatus_interno FROM estatus_interno WHERE idestatus_interno = '$informacion[solicitud_estatus_interno]'", $dspp) or die(mysql_error());
      $detalle_estado = mysql_fetch_assoc($estado);*/
      if($informacion['opp_estatus_interno'] == 11){
        echo '<span style="color:red">SUSPENDIDA</span>';
      }else if($informacion['opp_estatus_interno'] == 12){
        echo '<span style="color:red">INACTIVA</span>';
      }else{
        $query_tipo = mysql_query("SELECT solicitud_certificacion.tipo_solicitud FROM solicitud_certificacion WHERE idsolicitud_certificacion = '$informacion[idsolicitud_certificacion]'", $dspp) or die(mysql_error());
        $tipo_solicitud = mysql_fetch_assoc($query_tipo);
        if($tipo_solicitud['tipo_solicitud'] == 'NUEVA'){
          echo '<p class="bg-success">'.$tipo_solicitud['tipo_solicitud'].'</p>';
        }else if($tipo_solicitud['tipo_solicitud'] == 'RENOVACION'){
          echo '<p class="bg-warning">'.$tipo_solicitud['tipo_solicitud'].'</p>';
        }else{
          echo '<p style="color:red">NO DISPONIBLE</p>';
        }
      }
       ?>
    </td>
    <!-- ID DE LA SOLICITUD -->
    <td>
      <?php
      if(isset($informacion['idsolicitud_certificacion'])){
        echo '<a href="?SOLICITUD&idsolicitud='.$informacion['idsolicitud_certificacion'].'">CONSULTAR</a>';
      }else{
        echo '<p style="color:red">NO DISPONIBLE</p>';
      }
      ?>
    </td>
    <!-- PROCESO SOLICITUD -->
    <td>
      <?php
      if(isset($informacion['idsolicitud_certificacion'])){
        $proceso = mysql_query("SELECT idestatus_dspp, nombre FROM estatus_dspp WHERE idestatus_dspp = '$informacion[solicitud_estatus_dspp]'", $dspp) or die(mysql_error());
        $info_proceso = mysql_fetch_assoc($proceso);

        if($informacion['solicitud_estatus_dspp'] == 9){
          $proceso_interno = mysql_query("SELECT nombre FROM estatus_interno WHERE idestatus_interno = '$informacion[solicitud_estatus_interno]'", $dspp) or die(mysql_error());
          $info_proceso_interno = mysql_fetch_assoc($proceso_interno);
          //13_11_2018echo '('.$informacion['solicitud_estatus_dspp'].')'.$info_proceso['nombre'].': <span style="color:green">'.$info_proceso_interno['nombre'].'</span>';
          echo mayuscula($info_proceso['nombre']).': <span style="color:green">'.mayuscula($info_proceso_interno['nombre']).'</span>';
        }else{
          if($info_proceso['idestatus_dspp'] == 12){
            if(file_exists($informacion['certificado'])){
              echo '<a href="'.$informacion['certificado'].'" target="_new"><span class="glyphicon glyphicon-bookmark" aria-hidden="true"></span> '.mayuscula($info_proceso['nombre']).'</a>';
            }else{
              echo mayuscula($info_proceso['nombre']);
            }
            //13_11_2017echo '('.$info_proceso['idestatus_dspp'].')'.$info_proceso['nombre'];
          }else{
            //13_11_2018echo '('.$info_proceso['idestatus_dspp'].')'.$info_proceso['nombre'];
            echo mayuscula($info_proceso['nombre']);
          }
        }
      }else{
        echo '<span style="color:red">SIN SOLICITUD</span>';
      }
       ?>
    </td>

    <!-- PRODUCTOS DE LA ORGANIZACIÓN -->
    <td>
    <?php 
      $query_productos = mysql_query("SELECT GROUP_CONCAT(producto SEPARATOR ', ') AS 'lista_productos' FROM productos WHERE idsolicitud_certificacion = '$informacion[idsolicitud_certificacion]'", $dspp) or die(mysql_error());
      $productos = mysql_fetch_assoc($query_productos);
      if(empty($productos['lista_productos'])){
        $query_productos = mysql_query("SELECT GROUP_CONCAT(producto SEPARATOR ', ') AS 'lista_productos' FROM productos WHERE idopp = '$informacion[idopp]'", $dspp) or die(mysql_error());
        $productos = mysql_fetch_assoc($query_productos);
        echo $productos['lista_productos'];
      }else{
        echo '<p style="color:green">'.$productos['lista_productos'].'</p>';
      }
     ?>
    </td>
    <!-- NUMERO DE SOCIOS DE LA ORGANIZACIÓN -->
    <td>
    <?php 
    if(isset($informacion['idsolicitud_certificacion'])){
      $consultar_socios = mysql_query("SELECT resp1 FROM solicitud_certificacion WHERE idsolicitud_certificacion = $informacion[idsolicitud_certificacion]", $dspp) or die(mysql_error());
      $socios = mysql_fetch_assoc($consultar_socios);
      echo $socios['resp1'];
    }else{
      $consultar_socios = mysql_query("SELECT numero FROM num_socios WHERE idopp = $informacion[idopp]", $dspp) or die(mysql_error());
      $socios = mysql_fetch_assoc($consultar_socios);
      echo '<span style="color:green">'.$socios['numero'].'</span>';
    }
    ?>
    </td>
    <td></td>

  <?php
    echo '</tr>';
    $contador++;
  }
   ?>
  </tbody>
</table>
