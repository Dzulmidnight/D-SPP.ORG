<?php require_once('../Connections/dspp.php'); 

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

if(isset($_GET['query'])){
  $query_opp = "SELECT *, opp.idopp AS 'idOPP', opp.nombre AS 'nombreOPP', opp.estado AS 'estadoOPP', status.idstatus, status.nombre AS 'nombreStatus', certificado.idcertificado, certificado.vigenciainicio, certificado.vigenciafin FROM opp LEFT JOIN status ON opp.estado = status.idstatus LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE idoc = $_GET[query] AND opp.estado = 'ARCHIVADO' ORDER BY opp.idopp ASC";

  $queryExportar = "SELECT opp.*, contacto.*  FROM opp LEFT JOIN contacto ON opp.idopp = contacto.idopp WHERE idoc = $_GET[query] AND opp.estado = 'ARCHIVADO' ORDER BY opp.idopp ASC";



}else if(isset($_POST['filtroPalabra']) && $_POST['filtroPalabra'] == "1"){
  $palabraClave = $_POST['palabraClave'];

  $query_opp = "SELECT *, opp.idopp AS 'idOPP' ,opp.nombre AS 'nombreOPP', opp.estado AS 'estadoOPP' ,status.idstatus, status.nombre AS 'nombreStatus', certificado.idcertificado, certificado.vigenciainicio, certificado.vigenciafin FROM opp LEFT JOIN status ON opp.estado = status.idstatus LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE (idf LIKE '%$palabraClave%') OR (opp.nombre LIKE '%$palabraClave%') OR (opp.abreviacion LIKE '%$palabraClave%') OR (sitio_web LIKE '%$palabraClave%') OR (email LIKE '%$palabraClave%') OR (pais LIKE '%$palabraClave%') OR (razon_social LIKE '%$palabraClave%') OR (direccion_fiscal LIKE '%$palabraClave%') OR (rfc LIKE '%$palabraClave%') AND opp.estado = 'ARCHIVADO' ORDER BY opp.idopp ASC";

  $queryExportar = "SELECT opp.*, contacto.*  FROM opp LEFT JOIN contacto ON opp.idopp = contacto.idopp WHERE (opp.idf LIKE '%$palabraClave%') OR (opp.nombre LIKE '%$palabraClave%') OR (opp.abreviacion LIKE '%$palabraClave%') OR (sitio_web LIKE '%$palabraClave%') OR (email LIKE '%$palabraClave%') OR (pais LIKE '%$palabraClave%') OR (razon_social LIKE '%$palabraClave%') OR (direccion_fiscal LIKE '%$palabraClave%') OR (rfc LIKE '%$palabraClave%') AND opp.estado = 'ARCHIVADO' ORDER BY opp.idopp ASC";



}/*else if(isset($_POST['filtroPais']) && $_POST['filtroPais'] == "2" && $_POST['busquedaPais'] != NULL){
  $pais = $_POST['busquedaPais'];
  $query_opp = "SELECT * FROM opp WHERE pais LIKE '%$pais%'";
}*/else{
	$query_opp = "SELECT *, opp.idopp AS 'idOPP', opp.nombre AS 'nombreOPP', opp.estado AS 'estadoOPP', status.idstatus, status.nombre AS 'nombreStatus', certificado.idcertificado, certificado.vigenciainicio, certificado.vigenciafin FROM opp LEFT JOIN status ON opp.estado = status.idstatus LEFT JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.estado = 'ARCHIVADO' ORDER BY opp.idopp ASC";
  $queryExportar = "SELECT opp.*, contacto.*  FROM opp LEFT JOIN contacto ON opp.idopp = contacto.idopp WHERE opp.estado = 'ARCHIVADO' ORDER BY opp.idopp ASC";

}

$query_limit_opp = sprintf("%s LIMIT %d, %d", $query_opp, $startRow_opp, $maxRows_opp);
$opp = mysql_query($query_limit_opp, $dspp) or die(mysql_error());

//$row_opp = mysql_fetch_assoc($opp);

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


$timeActual = time();


?>

<script language="JavaScript"> 

var contadorPHP = 'qwerty';
var miVariable = [];
var idopp = '';


function addCheckbox(){

var cont = 0;


var checkboxIdopp = document.getElementsByName("idopp");
  for (var i=0; i<checkboxIdopp.length; i++) {
    if (checkboxIdopp[i].checked == true) { 
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
    if(!confirm('¿Estas seguro de eliminar el registro?')){ 
       return false; } 
} 
</script>


<?php 
  if(isset($_POST['activar']) && $_POST['activar'] == 1){

    $miVariable =  $_COOKIE["variable"];
    $token = strtok($miVariable, ",");

     while ($token !== false)
     {
        $query = "UPDATE opp SET estado = '' WHERE idopp = $token";
        $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
        echo "$token<br>";
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
        $query = "DELETE FROM opp WHERE idopp = $token";
        $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
        echo "$token<br>";
        $token = strtok(",");
     }
      echo '<script>borrarTodo();</script>';
      echo '<script>location.href="?OPP&filed";</script>';
  }


 ?>

  <hr>
  <div class="row">
    <div class="col-xs-6">
      <h5 class="alert alert-info" >Busqueda extendida(idf, nombre, abreviacion, sitio web, email, país, etc...). Sensible a acentos.</h5>

      <form method="post" name="filtro" action="" enctype="application/x-www-form-urlencoded">
        <div class="input-group">
          <input type="text" class="form-control" name="palabraClave" placeholder="Palabra clave...">
          <span class="input-group-btn">
            <input type="hidden" name="filtroPalabra" value="1">
            <button class="btn btn-default" type="submit">Buscar !</button>
          </span>
        </div><!-- /input-group -->        
      </form>
    </div><!-- /.col-lg-6 -->
    <div class="col-xs-6">
      <h4 class="well">
        Consulta OPP  | 
        <span> 
          <?php 
            $ejecutar = mysql_query($query_opp,$dspp) or die(mysql_error());
            $totalOPP = mysql_num_rows($ejecutar);
          ?>
            Total OPP(s): <strong style="color:red"><?php echo $totalOPP; ?></strong>

        </span>
      </h4>
    </div>


  </div>


  <hr>
<a class="btn btn-sm btn-warning" href="?OPP&filed">OPP(s) Archivado(s)</a>


<div class="panel panel-default">
  <div class="panel-heading">
    <div style="display:inline;margin-right:10em;">
      Exportar Contactos
      <a href="#" onclick="document.formulario1.submit()"><img src="../../img/pdf.png"></a>
      <a href="#" onclick="document.formulario2.submit()"><img src="../../img/excel.png"></a>
    </div>


      <form style="display:inline" action="" method="POST">
          Acción 
          <button class="btn btn-xs btn-danger" type="subtmit" value="Eliminar"  data-toggle="tooltip" data-placement="top" title="Eliminar">
            <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
          </button>        
          <button class="btn btn-xs btn-info" type="subtmit" value="1" name="archivar" data-toggle="tooltip" data-placement="top" title="Archivar">
            <span class="glyphicon glyphicon-folder-open" aria-hidden="true"></span>
          </button> 
      </form>     
  </div>

  <form name="formulario1" method="POST" action="../../reporte.php">
    <input type="hidden" name="contactoPDF" value="1">
    <input type="hidden" name="queryPDF" value="<?php echo $queryExportar; ?>">
  </form>
  <form name="formulario2" method="POST" action="../../reporte.php">
    <input type="hidden" name="contactoExcel" value="2">
    <input type="hidden" name="queryExcel" value="<?php echo $queryExportar; ?>">
  </form>
  
  <div class="panel-body">
  <table class="table table-condensed table-bordered table-hover">
    <thead style="font-size:12px;">
      <tr>
        <th class="text-center">IDF</th>
        <th class="text-center">Estatus Certificado</th>
        <th class="text-center" style="width:90px;">Vigencia Fin</th>
        <th class="text-center" style="width:200px;">Nombre</th>
        <th class="text-center">Abreviación</th>
        <!--<th class="text-center">Sitio WEB</th>-->
        <!--<th class="text-center">Email OPP</th>-->
        <th class="text-center">País</th>
        <th class="text-center">OC</th>
        <!--<th class="text-center">Razón social</th>
        <th class="text-center">Dirección fiscal</th>
        <th class="text-center">RFC</th>-->
        <!--<th class="text-center">Eliminar</th>-->
        <th class="text-center">Acciones</th>
        <th style="width:70px;">
          <form  style="margin: 0;padding: 0;" action="" method="POST">            
              <button class="btn btn-xs btn-danger" type="subtmit" value="2" name="eliminar" data-toggle="tooltip" data-placement="top" title="Eliminar" onclick="return confirm('¿Está seguro ?, los datos se eliminaran permanentemente');">
                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
              </button>        
              <button class="btn btn-xs btn-success" type="subtmit" value="1" name="activar" data-toggle="tooltip" data-placement="top" title="Activar">
                <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
              </button> 
          </form>
        </th>

      </tr>      
    </thead>
    <tbody style="font-size:11px">
      <?php $contador=0; while ($row_opp = mysql_fetch_assoc($opp)) {$contador++; ?>
        <tr>
          <td>

            <a class="btn btn-warning btn-sm" style="width:100%" href="?OPP&amp;detail&amp;idopp=<?php echo $row_opp['idOPP']; ?>&contact">Consultar<br>
              <?php echo $row_opp['idf']; ?>
            </a>
          </td>

          <td style="width:150px;">
            <?php 
              if(isset($row_opp['nombreStatus'])){
                if($row_opp['estado'] == 10){
                  echo "<p class='informacion text-center alert alert-success' style='padding:7px;'>$row_opp[nombreStatus]</p>";
                }
                if($row_opp['estado'] == 11){
                  echo "<p class='informacion text-center alert alert-warning' style='padding:7px;'>$row_opp[nombreStatus]</p>";
                }
                if($row_opp['estado'] == 16){
                  echo "<p class='informacion text-center alert alert-danger' style='padding:7px;'>$row_opp[nombreStatus]</p>";
                }
              }else{
                echo "<p class='text-center'>No Disponible</p>";
              }
             ?>
          </td>
          <td>
            <?php 
              $vigenciafin = date('d-m-Y', strtotime($row_opp['vigenciafin']));
              $timeVencimiento = strtotime($row_opp['vigenciafin']);
              $timeRestante = ($timeVencimiento - $timeActual);

              if(isset($row_opp['vigenciafin'])){
                if($timeVencimiento < $timeActual){
                  $alerta = "alert alert-danger";
                }else{
                  $alerta = "alert alert-success";
                }
                echo "<p style='padding:7px;width:80px;' class='text-center $alerta'><b>$vigenciafin</b></p>";
              }else{
                echo "<p class='text-center'>No Disponible</p>";
              }
            ?>
          </td>
          <td>
            <?php 
              if(isset($row_opp['nombreOPP'])){
                echo "<p class='text-center'>".$row_opp['nombreOPP']."</p>";
              }else{
                echo "<p class='alert alert-danger'>No Disponible</p>";
              } 
            ?>
          </td>
          <td>
            <?php 
              if(isset($row_opp['abreviacion'])){
                echo "<p class='text-center'>".$row_opp['abreviacion']."</p>";
              }else{
                echo "<p class='alert alert-danger'>No Disponible</p>";
              } 
            ?>
          </td>      
          <!--<td>
            <h6 class="text-center">
              <?php 
                if(isset($row_opp['sitio_web'])){
                  echo "<p class='text-center'>".$row_opp['sitio_web']."</p>";
                }else{
                  echo "<p class='alert alert-danger'>No Disponible</p>";
                } 
              ?>
            </h6>
          </td>-->
          <!--<td>
            <h6 class="text-center">
              <?php 
                if(isset($row_opp['email'])){
                  echo "<p class='text-center'>".$row_opp['email']."</p>";
                }else{
                  echo "<p class='alert alert-danger'>No Disponible</p>";
                } 
              ?>
            </h6>
          </td>-->
          <td>
            <?php echo $row_opp['pais']; ?>
          </td>
          <td>      
            <?
              $query_topp = "SELECT abreviacion FROM oc where idoc='".$row_opp['idoc']."'";
              $topp = mysql_query($query_topp, $dspp) or die(mysql_error());
              $row_topp = mysql_fetch_assoc($topp);
            ?>
            <?php if(isset($row_topp['abreviacion'])){ ?>
              <a style="width:100%" href="?OC&amp;detail&amp;idoc=<?php echo $row_opp['idoc']; ?>&contact">
                <?php  echo "<p class='alert alert-success' style='padding:7px;'>".$row_topp['abreviacion']."</p>"; ?>
              </a>
            <?php }else{ ?>    
                No Disponible
            <?php } ?>
          </td>
          <!--<td>
            <h6 class="text-center">
              <?php 
                if(isset($row_opp['razon_social'])){
                  echo "<p class='text-center'>".$row_opp['razon_social']."</p>";
                }else{
                  echo "<p class='alert alert-danger'>No Disponible</p>";
                } 
              ?>
            </h6>
          </td>-->
          <!--<td>
            <h6 class="text-center">
              <?php 
                if(isset($row_opp['direccion_fiscal'])){
                  echo "<p class='text-center'>".$row_opp['direccion_fiscal']."</p>";
                }else{
                  echo "<p class='alert alert-danger'>No Disponible</p>";
                } 
              ?>
            </h6>
          </td>-->         
          <!--<td>
            <h6 class="text-center">
              <?php 
                if(isset($row_opp['rfc'])){
                  echo "<p class='text-center'>".$row_opp['rfc']."</p>";
                }else{
                  echo "<p class='alert alert-danger'>No Disponible</p>";
                } 
              ?>
            </h6>
          </td>-->
          <!--<td>
              <form action="" method="post">
              <button class="btn btn-danger btn-sm" type="submit" value="Eliminar">
                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Eliminar
              </button>        
              <input type="hidden" value="OPP eliminado correctamente" name="mensaje" />
              <input type="hidden" value="1" name="opp_delete" />
              <input type="hidden" value="<?php echo $row_opp['idOPP']; ?>" name="idopp" />
              </form>
          </td>-->
          <td>
            <!--<button class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top" title="Visualizar">
              <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
            </button>-->
            <a href="<?php echo "?OPP&detail&idopp=$row_opp[idOPP]&contact"; ?>" class="btn btn-xs btn-info col-xs-6" data-toggle="tooltip" data-placement="top" title="Editar">
              <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
            </a>
            <form action="" method="post" name="formularioEliminar" ONSUBMIT="return preguntar();">
              <button class="btn btn-xs btn-danger col-xs-6" type="subtmit" value="Eliminar"  data-toggle="tooltip" data-placement="top" title="Eliminar">
                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
              </button>        
              <input type="hidden" value="OPP eliminado correctamente" name="mensaje" />
              <input type="hidden" value="1" name="opp_delete" />
              <input type="hidden" value="<?php echo $row_opp['idOPP']; ?>" name="idopp" />
            </form>

            <!--<button class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar">
               <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
            </button>-->
            
            
           
          </td>
          <td class="text-center">

            <div name="formulario">
              <input type="checkbox" name="idopp" id="<?php echo "idopp".$contador; ?>" value="<?php echo $row_opp['idOPP'] ?>" onclick="addCheckbox()">
              <!--<p>Nombre del idopp:<br><input type="text" name="idopp" id="idopp"></p>-->
              <!--<p><input type="button" onclick="addCheckbox()" name="guardar" id="guardar" value="guardar"></p>
              <p><input type="button" onclick="limpiarVista()" name="limpiar" id="limpiar" value="Limpiar Vista"></p>
              <p><input type="button" onclick="borrarTodo()" name="borrar" id="borrar" value="Borrar todo"></p>-->
            </div>
          </td>

        </tr>
        <?php }  ?>
        <? if($contador==0){?>
        <tr><td colspan="11" class="alert alert-info" role="alert">No se encontraron registros</td></tr>
        <? }?>
    </tbody>
  </table>


  </div>
</div>



<table>
  <tr>
  <td width="20"><?php if ($pageNum_opp > 0) { // Show if not first page ?>
  <a href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, 0, $queryString_opp); ?>">
  <span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span>
  </a>
  <?php } // Show if not first page ?></td>
  <td width="20"><?php if ($pageNum_opp > 0) { // Show if not first page ?>
  <a href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, max(0, $pageNum_opp - 1), $queryString_opp); ?>">
  <span class="glyphicon glyphicon-backward" aria-hidden="true"></span>
  </a>
  <?php } // Show if not first page ?></td>
  <td width="20"><?php if ($pageNum_opp < $totalPages_opp) { // Show if not last page ?>
  <a href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, min($totalPages_opp, $pageNum_opp + 1), $queryString_opp); ?>">
  <span class="glyphicon glyphicon-forward" aria-hidden="true"></span>
  </a>
  <?php } // Show if not last page ?></td>
  <td width="20"><?php if ($pageNum_opp < $totalPages_opp) { // Show if not last page ?>
  <a href="<?php printf("%s?pageNum_opp=%d%s", $currentPage, $totalPages_opp, $queryString_opp); ?>">
  <span class="glyphicon glyphicon-fast-forward" aria-hidden="true"></span>
  </a>
  <?php } // Show if not last page ?></td>
  </tr>
</table>
<?php
mysql_free_result($opp);
?>
