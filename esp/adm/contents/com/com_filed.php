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

if(isset($_POST['com_delete'])){
	$query=sprintf("delete from com where idcom = %s",GetSQLValueString($_POST['idcom'], "text"));
	$ejecutar=mysql_query($query,$dspp) or die(mysql_error());
}

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_com = 40;
$pageNum_com = 0;
if (isset($_GET['pageNum_com'])) {
  $pageNum_com = $_GET['pageNum_com'];
}
$startRow_com = $pageNum_com * $maxRows_com;

mysql_select_db($database_dspp, $dspp);

if(isset($_GET['query'])){
  $query_com = "SELECT *, com.idcom AS 'idCOM', com.nombre AS 'nombreCOM', com.estado AS 'estadoCOM', status.idstatus, status.nombre AS 'nombreStatus', certificado.idcertificado, certificado.vigenciainicio, certificado.vigenciafin FROM com LEFT JOIN status ON com.estado = status.idstatus LEFT JOIN certificado ON com.idcom = certificado.idcom WHERE idoc = $_GET[query] AND com.estado = 'ARCHIVADO' ORDER BY com.idcom ASC";

  $queryExportar = "SELECT com.*, contacto.*  FROM com LEFT JOIN contacto ON com.idcom = contacto.idcom WHERE idoc = $_GET[query] AND com.estado = 'ARCHIVADO' ORDER BY com.idcom ASC";



}else if(isset($_POST['filtroPalabra']) && $_POST['filtroPalabra'] == "1"){
  $palabraClave = $_POST['palabraClave'];

  $query_com = "SELECT *, com.idcom AS 'idCOM' ,com.nombre AS 'nombreCOM', com.estado AS 'estadoCOM' ,status.idstatus, status.nombre AS 'nombreStatus', certificado.idcertificado, certificado.vigenciainicio, certificado.vigenciafin FROM com LEFT JOIN status ON com.estado = status.idstatus LEFT JOIN certificado ON com.idcom = certificado.idcom WHERE (idf LIKE '%$palabraClave%') OR (com.nombre LIKE '%$palabraClave%') OR (com.abreviacion LIKE '%$palabraClave%') OR (sitio_web LIKE '%$palabraClave%') OR (email LIKE '%$palabraClave%') OR (pais LIKE '%$palabraClave%') OR (razon_social LIKE '%$palabraClave%') OR (direccion_fiscal LIKE '%$palabraClave%') OR (rfc LIKE '%$palabraClave%') AND com.estado = 'ARCHIVADO' ORDER BY com.idcom ASC";

  $queryExportar = "SELECT com.*, contacto.*  FROM com LEFT JOIN contacto ON com.idcom = contacto.idcom WHERE (com.idf LIKE '%$palabraClave%') OR (com.nombre LIKE '%$palabraClave%') OR (com.abreviacion LIKE '%$palabraClave%') OR (sitio_web LIKE '%$palabraClave%') OR (email LIKE '%$palabraClave%') OR (pais LIKE '%$palabraClave%') OR (razon_social LIKE '%$palabraClave%') OR (direccion_fiscal LIKE '%$palabraClave%') OR (rfc LIKE '%$palabraClave%') AND com.estado = 'ARCHIVADO' ORDER BY com.idcom ASC";



}/*else if(isset($_POST['filtroPais']) && $_POST['filtroPais'] == "2" && $_POST['busquedaPais'] != NULL){
  $pais = $_POST['busquedaPais'];
  $query_com = "SELECT * FROM com WHERE pais LIKE '%$pais%'";
}*/else{
	$query_com = "SELECT *, com.idcom AS 'idCOM', com.nombre AS 'nombreCOM', com.estado AS 'estadoCOM', status.idstatus, status.nombre AS 'nombreStatus', certificado.idcertificado, certificado.vigenciainicio, certificado.vigenciafin FROM com LEFT JOIN status ON com.estado = status.idstatus LEFT JOIN certificado ON com.idcom = certificado.idcom WHERE com.estado = 'ARCHIVADO' ORDER BY com.idcom ASC";
  $queryExportar = "SELECT com.*, contacto.*  FROM com LEFT JOIN contacto ON com.idcom = contacto.idcom WHERE com.estado = 'ARCHIVADO' ORDER BY com.idcom ASC";

}

$query_limit_com = sprintf("%s LIMIT %d, %d", $query_com, $startRow_com, $maxRows_com);
$com = mysql_query($query_limit_com, $dspp) or die(mysql_error());

//$row_com = mysql_fetch_assoc($com);

if (isset($_GET['totalRows_com'])) {
  $totalRows_com = $_GET['totalRows_com'];
} else {
  $all_com = mysql_query($query_com);
  $totalRows_com = mysql_num_rows($all_com);
}
$totalPages_com = ceil($totalRows_com/$maxRows_com)-1;

$queryString_com = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_com") == false && 
        stristr($param, "totalRows_com") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_com = "&" . htmlentities(implode("&", $newParams));
  }
}


$queryString_com = sprintf("&totalRows_com=%d%s", $totalRows_com, $queryString_com);


$timeActual = time();


?>

<script language="JavaScript"> 

var contadorPHP = 'qwerty';
var miVariable = [];
var idcom = '';


function addCheckbox(){

var cont = 0;


var checkboxIdcom = document.getElementsByName("idcom");
  for (var i=0; i<checkboxIdcom.length; i++) {
    if (checkboxIdcom[i].checked == true) { 
      idcom = checkboxIdcom[i].value; 
      sessionStorage[idcom] = idcom; 
    }
  }
  for(var i=0;i<sessionStorage.length;i++){
    var idcom=sessionStorage.key(i);
    miVariable[i] = idcom;
    document.cookie = 'variable='+miVariable;
  }
}

function mostrarDatos(){
  var datosDisponibles=document.getElementById('datosDisponibles');
  datosDisponibles.innerHTML='';
    for(var i=0;i<sessionStorage.length;i++){
      var idcom=sessionStorage.key(i);
      var variablePHP = "<?php $otraVariable = 6; ?>";
      datosDisponibles.innerHTML += '<div>'+idcom+'</div>';
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
        $query = "UPDATE com SET estado = '' WHERE idcom = $token";
        $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
        echo "$token<br>";
        $token = strtok(",");
     }
      echo '<script>borrarTodo();</script>';
      echo '<script>location.href="?COM&select";</script>';
  }

  if(isset($_POST['eliminar']) && $_POST['eliminar'] == 2){
    $miVariable =  $_COOKIE["variable"];
    $token = strtok($miVariable, ",");

     while ($token !== false) 
     {
        $query = "DELETE FROM com WHERE idcom = $token";
        $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
        echo "$token<br>";
        $token = strtok(",");
     }
      echo '<script>borrarTodo();</script>';
      echo '<script>location.href="?COM&filed";</script>';
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
        Consulta COM  | 
        <span> 
          <?php 
            $ejecutar = mysql_query($query_com,$dspp) or die(mysql_error());
            $totalCOM = mysql_num_rows($ejecutar);
          ?>
            Total Empresa(s): <strong style="color:red"><?php echo $totalCOM; ?></strong>

        </span>
      </h4>
    </div>


  </div>


  <hr>
<a class="btn btn-sm btn-warning" href="?COM&filed">Empresa(s) Archivada(s)</a>


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
      <?php $contador=0; while ($row_com = mysql_fetch_assoc($com)) {$contador++; ?>
        <tr>
          <td>

            <a class="btn btn-warning btn-sm" style="width:100%" href="?COM&amp;detail&amp;idcom=<?php echo $row_com['idCOM']; ?>&contact">Consultar<br>
              <?php echo $row_com['idf']; ?>
            </a>
          </td>

          <td style="width:150px;">
            <?php 
              if(isset($row_com['nombreStatus'])){
                if($row_com['estado'] == 10){
                  echo "<p class='informacion text-center alert alert-success' style='padding:7px;'>$row_com[nombreStatus]</p>";
                }
                if($row_com['estado'] == 11){
                  echo "<p class='informacion text-center alert alert-warning' style='padding:7px;'>$row_com[nombreStatus]</p>";
                }
                if($row_com['estado'] == 16){
                  echo "<p class='informacion text-center alert alert-danger' style='padding:7px;'>$row_com[nombreStatus]</p>";
                }
              }else{
                echo "<p class='text-center'>No Disponible</p>";
              }
             ?>
          </td>
          <td>
            <?php 
              $vigenciafin = date('d-m-Y', strtotime($row_com['vigenciafin']));
              $timeVencimiento = strtotime($row_com['vigenciafin']);
              $timeRestante = ($timeVencimiento - $timeActual);

              if(isset($row_com['vigenciafin'])){
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
              if(isset($row_com['nombreCOM'])){
                echo "<p class='text-center'>".$row_com['nombreCOM']."</p>";
              }else{
                echo "<p class='alert alert-danger'>No Disponible</p>";
              } 
            ?>
          </td>
          <td>
            <?php 
              if(isset($row_com['abreviacion'])){
                echo "<p class='text-center'>".$row_com['abreviacion']."</p>";
              }else{
                echo "<p class='alert alert-danger'>No Disponible</p>";
              } 
            ?>
          </td>      
          <!--<td>
            <h6 class="text-center">
              <?php 
                if(isset($row_com['sitio_web'])){
                  echo "<p class='text-center'>".$row_com['sitio_web']."</p>";
                }else{
                  echo "<p class='alert alert-danger'>No Disponible</p>";
                } 
              ?>
            </h6>
          </td>-->
          <!--<td>
            <h6 class="text-center">
              <?php 
                if(isset($row_com['email'])){
                  echo "<p class='text-center'>".$row_com['email']."</p>";
                }else{
                  echo "<p class='alert alert-danger'>No Disponible</p>";
                } 
              ?>
            </h6>
          </td>-->
          <td>
            <?php echo $row_com['pais']; ?>
          </td>
          <td>      
            <?
              $query_tcom = "SELECT abreviacion FROM oc where idoc='".$row_com['idoc']."'";
              $tcom = mysql_query($query_tcom, $dspp) or die(mysql_error());
              $row_tcom = mysql_fetch_assoc($tcom);
            ?>
            <?php if(isset($row_tcom['abreviacion'])){ ?>
              <a style="width:100%" href="?OC&amp;detail&amp;idoc=<?php echo $row_com['idoc']; ?>&contact">
                <?php  echo "<p class='alert alert-success' style='padding:7px;'>".$row_tcom['abreviacion']."</p>"; ?>
              </a>
            <?php }else{ ?>    
                No Disponible
            <?php } ?>
          </td>

          <td>

            <a href="<?php echo "?COM&detail&idcom=$row_com[idCOM]&contact"; ?>" class="btn btn-xs btn-info col-xs-6" data-toggle="tooltip" data-placement="top" title="Editar">
              <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
            </a>
            <form action="" method="post" name="formularioEliminar" ONSUBMIT="return preguntar();">
              <button class="btn btn-xs btn-danger col-xs-6" type="subtmit" value="Eliminar"  data-toggle="tooltip" data-placement="top" title="Eliminar">
                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
              </button>        
              <input type="hidden" value="Empresa eliminada correctamente" name="mensaje" />
              <input type="hidden" value="1" name="com_delete" />
              <input type="hidden" value="<?php echo $row_com['idCOM']; ?>" name="idcom" />
            </form>
           
          </td>
          <td class="text-center">

            <div name="formulario">
              <input type="checkbox" name="idcom" id="<?php echo "idcom".$contador; ?>" value="<?php echo $row_com['idCOM'] ?>" onclick="addCheckbox()">

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
  <td width="20"><?php if ($pageNum_com > 0) { // Show if not first page ?>
  <a href="<?php printf("%s?pageNum_com=%d%s", $currentPage, 0, $queryString_com); ?>">
  <span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span>
  </a>
  <?php } // Show if not first page ?></td>
  <td width="20"><?php if ($pageNum_com > 0) { // Show if not first page ?>
  <a href="<?php printf("%s?pageNum_com=%d%s", $currentPage, max(0, $pageNum_com - 1), $queryString_com); ?>">
  <span class="glyphicon glyphicon-backward" aria-hidden="true"></span>
  </a>
  <?php } // Show if not first page ?></td>
  <td width="20"><?php if ($pageNum_com < $totalPages_com) { // Show if not last page ?>
  <a href="<?php printf("%s?pageNum_com=%d%s", $currentPage, min($totalPages_com, $pageNum_com + 1), $queryString_com); ?>">
  <span class="glyphicon glyphicon-forward" aria-hidden="true"></span>
  </a>
  <?php } // Show if not last page ?></td>
  <td width="20"><?php if ($pageNum_com < $totalPages_com) { // Show if not last page ?>
  <a href="<?php printf("%s?pageNum_com=%d%s", $currentPage, $totalPages_com, $queryString_com); ?>">
  <span class="glyphicon glyphicon-fast-forward" aria-hidden="true"></span>
  </a>
  <?php } // Show if not last page ?></td>
  </tr>
</table>
<?php
mysql_free_result($com);
?>
