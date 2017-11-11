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



 ?>
<div class="row">
  <div class="col-md-12">
    <h4>ORGANIZACIONES CANCELADAS</h4>
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
// ORGANIZACIONES CANCELADAS
// SON LAS ORGANIZACIONES QUE TIENEN OPP.ESTATUS_INTERNO = 10 (cancelada) Y OPP.ESTATUS_DSPP != 13(certificada)
/// SELECCIONA LAS ORGANIZACIONES("OPP") QUE TIENEN SOLICITUD "EN RENOVACIÓN", PERO QUE AUN NO SE LES HA ASIGNADO UN DICTAMEN POSITIVO
/// DEBEN DE TENER UN ESTATUS-DSPP DEL 1 al 9, o el 17
///PARA EL NUMERO DE SOCIOS TOMAMOS LA RESP1 QUE ES "NUMERO DE SOCIOS"


  $query = "SELECT opp.idopp, opp.spp, opp.nombre AS 'nombre_opp', opp.abreviacion AS 'abreviacion_opp', opp.pais, opp.estatus_opp, oc.abreviacion AS 'abreviacion_oc', solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.tipo_solicitud, solicitud_certificacion.estatus_dspp, solicitud_certificacion.estatus_interno, solicitud_certificacion.resp1 AS 'num_socios', estatus_dspp.nombre AS 'nombre_estatus_dspp', estatus_interno.nombre AS 'nombre_estatus_interno', certificado.idcertificado, certificado.vigencia_fin FROM opp LEFT JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp INNER JOIN oc ON opp.idoc = oc.idoc LEFT JOIN estatus_dspp ON opp.estatus_dspp = estatus_dspp.idestatus_dspp LEFT JOIN estatus_interno ON opp.estatus_interno = estatus_interno.idestatus_interno LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.estatus_interno = 11 OR solicitud_certificacion.estatus_interno = 11 ORDER BY opp.nombre";
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
      <th>SOLICITUD</th>
      <th>ORGANIZACIÓN</th>
      <th>PAÍS</th>
      <th>OC</th>
      <th>PROCESO CERTIFICACIÓN</th>
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
    <td>
      <?php 
        echo '<p class="alert alert-info" style="padding:7px;margin-bottom:0px;">ID: '.$informacion['idsolicitud_certificacion'].'| '.$informacion['tipo_solicitud'].'</p>';
        echo '<p >'.$informacion['nombre_estatus_dspp'].'</p>';
       ?>
    </td>
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
      <select name="estatus_interno<?php echo $informacion['idopp']; ?>">
        <option>...</option>
        <?php 
        $row_interno = mysql_query("SELECT * FROM estatus_interno", $dspp) or die(mysql_error());
        while($estatus_interno = mysql_fetch_assoc($row_interno)){
        ?>
          <option value="<?php echo $estatus_interno['idestatus_interno'] ?>" <?php if($estatus_interno['idestatus_interno'] == $informacion['estatus_interno']){echo "selected";} ?>><?php echo $estatus_interno['nombre']; ?></option>
        <?php
        }
         ?>
      </select>
      <?php echo "<p class='alert alert-info' style='padding:7px;'>$informacion[nombre_estatus_interno]</p>"; ?>  
    </td>
    <!-- ULTIMA FECHA DE CERTIFICADO -->
    <td>
      <?php 

        $fecha_certificado = strtotime($informacion['vigencia_fin']);
        $vigencia_fin = date('d/m/Y', $fecha_certificado);
      ?>
      <input type="date" name="vigencia_fin<?php echo $informacion['idopp']; ?>" value="<?php echo $informacion['vigencia_fin']; ?>">
      <?php 
      if(isset($informacion['idcertificado'])){
        $estatus_certificado = mysql_query("SELECT idcertificado, estatus_certificado, estatus_dspp.nombre FROM certificado LEFT JOIN estatus_dspp ON certificado.estatus_certificado = estatus_dspp.idestatus_dspp WHERE idcertificado = $informacion[idcertificado]", $dspp) or die(mysql_error());
        $certificado = mysql_fetch_assoc($estatus_certificado);

        switch ($certificado['estatus_certificado']) {
          case '13': //certificado "activo"
            $clase = 'text-center alert alert-success';
            break;
          case '14': //certificado "renovacion"
            $clase = 'text-center alert alert-info';
            break;
          case '15': //certificado "por expirar"
            $clase = 'text-center alert alert-warning';
            break;
          case '16': //certificado "Expirado"
            $clase = 'text-center alert alert-danger';
            break;

          default:
            # code...
            break;
        }
         echo "<p style='padding:5px;' class='".$clase."'><span style='color:#bdc3c7'>ID: ".$informacion['idcertificado']."| </span>".$certificado['nombre']."</p>";
      }else{
        echo "<p style='padding:5px;'>No Disponible</p>";
      }
        //echo $opp['estatus_certificado'];
      ?>

    </td>
    <!-- ESTATUS DE LA OPP -->
    <td>
      <?php 
      if($informacion['estatus_opp'] == 13){ // la organización se ha certificado, desde la tabla de información OPP, pero dentro de la tabla de solicitudes no se ha terminado el proceso
        //estatus_dspp 13 = CERTIFICADA
        echo '<a href="#" style="color:#e67e22" data-toggle="tooltip" title="ESTA ORGANIZACIÓN SE ENCUENTRA CERTIFICADA PERO AUN NO SE HA CARGADO EL CERTIFICADO EN LA SECCIÓN DE SOLICITUDES"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span> CERTIFICADA</a>';
      }else{
        echo 'EN PROCESO DE RENOVACIÓN';
      }
      
      ?>
    </td>
    <!-- PRODUCTOS -->
    <td>
    <?php 
      $query = "SELECT GROUP_CONCAT(producto SEPARATOR ' , ') AS lista_producto_especifico FROM productos WHERE idopp = $informacion[idopp]";
      $consultar_productos = mysql_query($query,$dspp) or die(mysql_error());
      $detalle_productos = mysql_fetch_assoc($consultar_productos);
      echo '<p>'.utf8_encode(mayuscula($detalle_productos['lista_producto_especifico'])).'</p>';
     ?>
    </td>
    <!-- NUMERO DE SOCIOS -->
    <td>
      <?php echo $informacion['num_socios']; ?>
    </td>
    <td></td>

  <?php
    echo '</tr>';
    $contador++;
  }
   ?>
  </tbody>
</table>