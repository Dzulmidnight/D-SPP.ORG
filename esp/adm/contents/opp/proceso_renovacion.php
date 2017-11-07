  <div class="col-md-2">
    <button class="btn btn-default" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
      <span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span> Filtro Avanzado
    </button>  
  </div>
  <div class="col-md-10">
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
                echo "<option value='".$pais['pais']."'>".$pais['pais']."</option>";
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
          <!--<div class="col-xs-3">
            Estatus Certificado
            <select class="form-control" name="buscar_estatus" id="">
              <option value=''>Estatus Certificado</option>
              <option value="13">CERTIFICADA</option>
              <option value="14">AVISO DE RENOVACIÓN</option>
              <option value="15">CERTIFICADO POR EXPIRAR</option>
              <option value="16">CERTIFICADO EXPIRADO</option>
            </select>
          </div>-->

          <div class="col-xs-12">
            <button type="submit" class="btn btn-success" name="busqueda_filtros" style="width:100%" value="1">Filtrar Información</button>
          </div>
        </div>
      </div>
    </form>

</div>
<!-- TERMINA CUADRO DE BUSQUEDA ACANZADA -->

<div class="row">
  <div class="col-md-12">
    <h4>ORGANIZACIONES ARCHIVADAS</h4>
  </div>  
</div>

  
  <!--<div class="panel-body">-->
  <table class="table table-condensed table-bordered table-hover" style="font-size:11px;">
    <thead>
      <tr>
        <th colspan="1">
          <!--<a class="btn btn-sm btn-warning" href="?OPP&filed">OPP(s) Archivado(s)</a>-->
          <button class="btn btn-sm btn-info" onclick="guardarDatos()"><span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Guardar Cambios</button><!-- BOTON GUARDAR DATOS -->
        </th>
        <th colspan="4">
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
        <th colspan="7" class="success text-center">
          <p style="font-size:12px;color:red">Total OPP(s): <?php echo $totalOPP; ?></p>
        </th>
      </tr>
      <tr>
        <th class="text-center">#SPP</th>
        <th class="text-center">Solicitud</th>
        <th class="text-center">Organización</th>
        <!--<th class="text-center">Abreviación</th>-->
        <th class="text-center">País</th>
        <!--<th class="text-center">Situación <br>OPP</th>-->
        <th class="text-center">Estatus Publico</th>
        <!--<th class="text-center">Proceso Certificación</th>
        <th class="text-center">Fecha Final<br>(Certificado)</th>-->
        <th class="text-center"><a href="#" data-toggle="tooltip" title="Proceso de Certificación en el que se encuentra la OPP"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>Proceso certificación</a></th>
        <th class="text-center">
          <a href="#" data-toggle="tooltip" title="Fecha en la que expira el Certificado"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>Ultima Fecha de Certificado</a>
        </th>
        <th class="text-center"><a href="#" data-toggle="tooltip" title="Estatus general en el que se encuentra la OPP">
          <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>Estatus de la OPP</a>
        </th>
        <!--<th class="text-center">Sitio WEB</th>-->
        <!--<th class="text-center">Email OPP</th>-->
        <th class="text-center">
          Productos
          <br>
          <a href="../../traducir_producto.php?opp" target="ventana1" onclick="ventanaNueva ('', 500, 400, 'ventana1');"><span class="glyphicon glyphicon-book glyphicon" aria-hidden="true"></span> Traducir</a>
        </th>
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
              <!--<button class="btn btn-xs btn-info" type="subtmit" value="1" name="archivar" data-toggle="tooltip" data-placement="top" title="Archivar">
                <span class="glyphicon glyphicon-folder-open" aria-hidden="true"></span>
              </button>--> 
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
          $query_solicitud = "SELECT idsolicitud_certificacion, tipo_solicitud, idopp FROM solicitud_certificacion WHERE idopp = '$opp[idopp]'";
          $row_solicitud = mysql_query($query_solicitud, $dspp) or die(mysql_error());
          $solicitud = mysql_fetch_assoc($row_solicitud);
        ?>
          <tr <?php if($opp['estatus_interno'] == 10){ echo 'class="alert alert-danger"'; } ?>>
            <!--- INICIA CODIGO SPP ---->
            <td>
                <a class="btn btn-primary btn-xs" style="width:100%;font-size:10px;" href="?OPP&amp;detail&amp;idopp=<?php echo $opp['idopp']; ?>">Consultar<br>
                  <!--<?php echo "<br>IDOPP: ".$row_opp['idOPP']; ?>-->
                </a>
                <input type="text" name="spp<?php echo $opp['idopp'];?>" value="<?php echo $opp['spp_opp']; ?>">
            </td>
            <!--- TERMINA CODIGO SPP ---->
            <td>
              <?php 
              if($solicitud['tipo_solicitud'] == 'RENOVACION'){
                echo "<span style='font-weight:bold;color:#e67e22'>$solicitud[tipo_solicitud]</span>";
              }else if($solicitud['tipo_solicitud'] == 'NUEVA'){
                echo "<span style='font-weight:bold;color:#2ecc71'>$solicitud[tipo_solicitud]</span>";
              }else{
                echo "<span style='font-weight:bold;color:#2980b9'>SIN SOLICITUD</span>";
              }
              ?>
            </td>
            <!--- INICIA NOMBRE ---->
            <td>
              <p style="color:#2c3e50"><b><?php echo $opp['nombre']; ?></b></p>
              <p style="color:#2980b9"><?php echo $opp['abreviacion_opp']; ?></p>
            </td>
            <!--- TERMINA NOMBRE ---->

            <!--- INICIA ABREVIACIÓN ---->
            <!--<td>
              <?php
              echo $opp['abreviacion_opp'];
               ?>
            </td>-->
            <!--- TERMINA ABREVIACIÓN ---->
            <!--- INICIA PAIS ---->
            <td>
              <b style="color:#e74c3c"><?php echo $opp['pais']; ?></b>
            </td>
            <!--- TERMINA PAIS ---->

            <!--- INICIA SITUACION OPP ---->
            <!--<td>
              <select name="estatus_opp<?php  echo $opp['idopp']; ?>" id="">
                <option>...</option>
                <option value="NUEVA" <?php if($opp['estatus_opp'] == 'NUEVA'){ echo 'selected';} ?>>NUEVA</option>
                <option value="RENOVACION" <?php if($opp['estatus_opp'] == 'RENOVACION'){ echo 'selected';} ?>>RENOVACIÓN</option>
                <option value="SUSPENDIDA" <?php if($opp['estatus_opp'] == 'SUSPENDIDA'){ echo 'selected';} ?>>SUSPENDIDA</option>
                <option value="CANCELADO" <?php if($opp['estatus_opp'] == 'CANCELADO'){ echo 'selected';} ?>>CANCELADO</option>
              </select>
              <?php 
              if($opp['estatus_opp'] == 'NUEVA'){
                echo "<p class='alert alert-success' style='font-size:10px;padding:5px;'>NUEVA</p>";
              }else if($opp['estatus_opp'] == 'RENOVACION'){
                echo "<p class='alert alert-warning' style='font-size:10px;padding:5px;'>RENOVACIÓN</p>";
              }else if($opp['estatus_opp'] == 'CANCELADO'){
                echo "<p class='alert alert-danger' style='font-size:10px;padding:5px;'>CANCELADO</p>";
              }
               ?>
            </td>-->
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
                $vigenciafin = date('d-m-Y', strtotime($opp['fecha_fin']));
                $timeVencimiento = strtotime($opp['fecha_fin']);
              
               ?>
              <input type="date" name="vigencia_fin<?php echo $opp['idopp']; ?>" value="<?php echo $opp['fecha_fin']; ?>">
              <?php 
              if(isset($opp['idcertificado'])){
                $estatus_certificado = mysql_query("SELECT idcertificado, estatus_certificado, estatus_dspp.nombre FROM certificado LEFT JOIN estatus_dspp ON certificado.estatus_certificado = estatus_dspp.idestatus_dspp WHERE idcertificado = $opp[idcertificado]", $dspp) or die(mysql_error());
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
                 echo "<p style='padding:5px;' class='".$clase."'>".$certificado['nombre']."</p>";
              }else{
                echo "<p style='padding:5px;'>No Disponible</p>";
              }
                //echo $opp['estatus_certificado'];
               ?>

            </td>
            <!--- TERMINA FECHA_FINAL ---->

            <!--- INICIA ESTATUS_CERTIFICADO ---->
            <td>
            <?php 
            /*$row_certificadas = mysql_query("SELECT opp.idopp, certificado.idopp FROM opp INNER JOIN certificado ON opp.idopp = certificado.idopp WHERE opp.idopp = '$pais[pais]' AND (opp.estatus_dspp != 16 AND opp.estatus_interno != 10 AND opp.estatus_interno != 11 OR opp.estatus_interno = 15) GROUP BY certificado.idopp", $dspp);
            $num_certificadas = mysql_num_rows($row_certificadas);
            $total_certificada += $num_certificadas;
            */
            if($opp['estatus_dspp'] == 14 OR $opp['estatus_dspp'] == 15 OR $opp['estatus_dspp'] == 13){
              echo "<p class='text-center alert alert-success' style='padding:5px;'>Certificada</p>";
            }else if($solicitud['tipo_solicitud'] == 'RENOVACION' && $opp['estatus_dspp'] = 16 ){
              echo "<p class='text-center alert alert-warning' style='padding:5px;'>En Proceso de Renovación</p>";
            }else if(!isset($opp['fecha_fin']) && $solicitud['tipo_solicitud'] == 'NUEVA'){
              echo "<p class='text-center alert alert-info' style='padding:5px;'>Solicitud Inicial</p>";
            }else if($opp['estatus_dspp'] == 16 && !isset($solicitud['tipo_solicitud'])){
              echo "<p class='text-center alert alert-danger' style='padding:5px;'>Certificación Expirada</p>";
            }else{
              echo '<p style="color:red">No Disponible</p>';
            }

             ?>
            </td>
            <!--- TERMINA ESTATUS_CERTIFICADO ---->

            <!--- INICIA PRODUCTOS ---->
            <td>
              <?php 
              $row_productos = mysql_query("SELECT * FROM productos WHERE idopp = $opp[idopp] GROUP BY productos.producto", $dspp) or die(mysql_error());
              $total_productos = mysql_num_rows($row_productos);
              ?>


              <a style="font-size:14px;" href="../../agregar_producto.php?idopp=<?php echo $opp['idopp']; ?>" target="ventana1" onclick="ventanaNueva ('', 500, 400, 'ventana1');"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span></a>
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

<?php 
  $query = "SELECT * "
 ?>
<table class="table table-bordered">
  <tr>
    <td>adsfasf</td>
    <td>adsfasf</td>
    <td>adsfasf</td>
  </tr>
</table>