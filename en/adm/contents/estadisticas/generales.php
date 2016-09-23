<?php require_once('../Connections/dspp.php'); ?>
<?php
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO opp (idf, password, nombre, abreviacion, sitio_web, telefono, email, pais, idoc, razon_social, direccion_fiscal, rfc) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['idf'], "text"),
                       GetSQLValueString($_POST['password'], "text"),
                       GetSQLValueString($_POST['nombre'], "text"),
                       GetSQLValueString($_POST['abreviacion'], "text"),
                       GetSQLValueString($_POST['sitio_web'], "text"),
                       GetSQLValueString($_POST['telefono'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['pais'], "text"),
                       GetSQLValueString($_POST['idoc'], "int"),
                       GetSQLValueString($_POST['razon_social'], "text"),
                       GetSQLValueString($_POST['direccion_fiscal'], "text"),
                       GetSQLValueString($_POST['rfc'], "text"));

  mysql_select_db($database_dspp, $dspp);
  $Result1 = mysql_query($insertSQL, $dspp) or die(mysql_error());

  $insertGoTo = "main_menu.php?OPP&add&mensaje=OPP agregado correctamente";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_dspp, $dspp);
$query_pais = "SELECT nombre FROM paises ORDER BY nombre ASC";
$pais = mysql_query($query_pais, $dspp) or die(mysql_error());
$row_pais = mysql_fetch_assoc($pais);
$totalRows_pais = mysql_num_rows($pais);

mysql_select_db($database_dspp, $dspp);
$query_oc = "SELECT idoc, idf, abreviacion, pais FROM oc ORDER BY nombre ASC";
$oc = mysql_query($query_oc, $dspp) or die(mysql_error());
$row_oc = mysql_fetch_assoc($oc);
$totalRows_oc = mysql_num_rows($oc);

/* MUESTRA LAS SOLICITUDES CON LOS OPP SEPARADOS
SELECT opp.*, solicitud_certificacion.*, COUNT(solicitud_certificacion.idsolicitud_certificacion) AS "TOTAL_SOLICITUD" FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE opp.pais = "PerÃº" GROUP BY opp.idopp
*/

/*
SELECT opp.idopp, opp.pais, solicitud_certificacion.idsolicitud_certificacion, solicitud_certificacion.idopp, solicitud_certificacion.idoc , solicitud_certificacion.status ,COUNT(solicitud_certificacion.idsolicitud_certificacion) AS "TOTAL_SOLICITUD" FROM opp INNER JOIN solicitud_certificacion ON opp.idopp = solicitud_certificacion.idopp WHERE opp.pais = "PerÃº"
*/

  $codigoHTML='
  <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml">
  <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>LISTA D-SPP</title>
  </head>
  <body>';
  //CAPTURO EL NOMBRE DE LA TABLA, PARA CONSULTAR EL NOMBRE DE SUS COLUMNAS
  if(isset($_POST['consulta1']) && $_POST['consulta1'] == 1){
    $tabla = $_POST['tabla'];
    //$consultaTabla = "SELECT * FROM ".$tabla."";
    $consultaTabla = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".$tabla."'";
    //$_GLOBALS["tabla"] = $_POST["tabla"];
  }

  //CONSULTO LOS NOMBRES DE LAS COLUMNAS DE X TABLA
  if(isset($_POST['consulta2']) && $_POST['consulta2'] == 1){
    $tabla_nombre = $_POST['nombreTabla'];
    $consultaTabla = $_POST['consultaTabla'];
    $arrayNombre[] = "";
    $cont = 0;
      $columna = "";
      $coma = ", ";


    $arrayColumna = $_POST['columna'];

    if($arrayColumna[0] == "*"){
      //$consulta2 = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".$tabla_nombre."'";
      $ejecutar = mysql_query($consultaTabla,$dspp) or die(mysql_error());
      while($row = mysql_fetch_row($ejecutar)){
        $arrayNombre[] = $row[0]; 
      }

      $total = count($arrayNombre);

      for ($i=0; $i < $total ; $i++) { 
        $columna.= $arrayNombre[$i];
        $arrayNombre[$i] = $arrayNombre[$i];
          if(($i+1 < $total) && $i != 0){
            $columna.= $coma;
          }
      }
      $consultaColumna = "SELECT ".$columna." FROM ".$tabla_nombre."";
    }else{

      $total = count($arrayColumna);

      for ($i=0; $i < $total ; $i++) { 
        $columna.= $arrayColumna[$i];
        $arrayNombre[$i] = $arrayColumna[$i];
          if($i+1 < $total){
            $columna.= $coma;
          }
      }
      $consultaColumna = "SELECT ".$columna." FROM ".$tabla_nombre."";      
    }

  }

  if(isset($_POST['consulta3']) && $_POST['consulta3'] == 3){

      $campoBuscar = $_POST['campoBuscar'];
      $consultaColumna = $_POST['consultaColumna'];
      $nombreTabla = $_POST['nombreTabla'];

      $arrayColumna2 = unserialize($_POST['arrayColumna2']);
      //var_dump($arrayColumna2);
      //$arrayColumna2 = $_POST['arrayColumna2'];
      $busqueda = "WHERE ";
      $total = count($arrayColumna2);
      $or = " OR ";
      for ($i=0; $i < count($arrayColumna2); $i++) {
        if(!empty($arrayColumna2[$i])){
          $busqueda.= "(".$arrayColumna2[$i]." LIKE '%".$campoBuscar."%')";
          if($i+1 < $total){
            $busqueda.= $or;
          }

        }            
      }
      $queryBusqueda = $consultaColumna." ".$busqueda;
  }
?>


    <div class="row">
      <div class="col-xs-12">
      
        <div class="col-xs-6 well">
          <p class="alert alert-info" style="padding:7px;">1.- Seleccione la tabla de desea consultar.</p>
          <form name="tabla2" method="POST">
            <select style="padding:30px;" class="form-control chosen-select-deselect" data-placeholder="Seleccione una Tabla" name="tabla" id="" >
              
              <!--<option value="certificado">Certificado</option>-->
              <option value="">Seleccionar Tabla</option>
              <option value="opp">Opp</option>
              <option value="oc">Oc</option>
              <option value="com">Com(Empresas)</option>
              <option value="paises">Paises</option>
              <option value="solicitud_certificacion">Solicitud_certificación</option>
              <option value="solicitud_registro">Solicitud registro</option>
              
            </select>
            <button type="submit" class="btn btn-primary">Seleccionar</button>
            <input type="hidden" name="consulta1" value="1">
          </form>
        </div>

        <?php
        if(isset($tabla)){
          //echo $consultaTabla;
          $queryTabla = mysql_query($consultaTabla,$dspp) or die(mysql_error());
        ?>
          <div class="col-xs-6 well">
            <p class="alert alert-info" style="padding:7px;">2.- Seleccione los datos que desea visualizar, en caso de requerir todos, seleccionar "Todos".</p>
            <form name="ingresar2" method="POST">
              <select class="form-control chosen-select-deselect" data-placeholder="Seleccione datos" name="columna[]" id="" multiple required>
                <option value="*">Todos</option>
                <?php 
                  while($datosTabla = mysql_fetch_assoc($queryTabla)){
                    echo "<option value='".$datosTabla['COLUMN_NAME']."'>".$datosTabla['COLUMN_NAME']."</option>";
                  }
                ?>
              </select>

              <input type="hidden" name="nombreTabla" value="<?php echo $tabla; ?>">
              <input type="hidden" name="consultaTabla" value="<?php echo $consultaTabla; ?>">
              <input type="hidden" name="consulta2" value="1">
              <button type="submit" class="btn btn-primary">Generar</button>
            </form>
          </div>
        <?php              
        }
         ?>

        <?php 

        if(isset($columna) || !empty($campoBuscar)){
        ?>

        <?php
          if(!empty($campoBuscar)){
            $contador = 1;
            $colspan = count($arrayColumna2)+1;
            //echo $campoBuscar;
            //echo "<br>La consulta es: ".$consultaColumna;
            //echo "<br>El array es: <br>";
            /*for ($i=0; $i < count($arrayColumna2); $i++) { 
              echo "<br>".$arrayColumna2[$i];
            }*/
            //echo "<br>La Busqueda es: ".$busqueda;
            //echo "<br>Query Busqueda es: ".$queryBusqueda;
            ?>
              <div class="col-xs-6 well">
                <a href="#" onclick="document.ingresar4.submit()"><img src="../../img/pdf.png"></a>
                <a href="#" onclick="document.ingresar5.submit()"><img src="../../img/excel.png"></a>
              </div>
            <?php

            echo "<table class='table table-bordered table-hover'>";
            $codigoHTML.='<table width="100%" border="1" cellspacing="0" cellpadding="0" style="font-size:10px;">';
              echo "<thead>";
              $codigoHTML.= "<thead>";
                echo "
                  <tr>                       
                    <th style='text-align:center;' colspan='".$colspan."'>
                    Lista $nombreTabla
                    </th>
                  </tr>";
                $codigoHTML.= '
                  <tr>
                    <th style="text-align:center;" colspan="'.$colspan.'">Lista '.$nombreTabla.'</th>
                  </tr>
                ';

                echo "<tr>";
                $codigoHTML.= "<tr>";
                  echo "<th>Nº</th>";
                  $codigoHTML.= "<th>Nº</th>";
                  for ($i=0; $i < count($arrayColumna2) ; $i++) { 
                    if($arrayColumna2[$i] != ""){
                      echo "<th>".$arrayColumna2[$i]."</th>";
                      $codigoHTML.= "<th>".$arrayColumna2[$i]."</th>";
                    }
                  }
                echo"</tr>";
                $codigoHTML.="</tr>";
              echo "</thead>";
              $codigoHTML.= "</thead>";
              echo "<tbody>";
              $codigoHTML.= "<tbody>";
                $queryColumna = mysql_query($queryBusqueda,$dspp) or die(mysql_error());

                while($columna = mysql_fetch_assoc($queryColumna)){
                  echo "<tr>";
                  $codigoHTML.= "<tr>";
                  echo "<td>".$contador."</td>";
                  $codigoHTML.= "<td>".$contador."</td>";
                  for ($i=0; $i < count($arrayColumna2) ; $i++) { 
                    if($arrayColumna2[$i] != ""){
                      echo "<td>".$columna[$arrayColumna2[$i]]."</td>";
                      $codigoHTML.= "<td>".$columna[$arrayColumna2[$i]]."</td>";
                    }
                    //echo $arrayNombre[$i];
                    
                  }
                  echo "</tr>";
                  $codigoHTML.= "</tr>";
                  $contador++;
                }
              echo "</tbody>";
              $codigoHTML.= "</tbody>";
            echo "</table>";
            $codigoHTML.= "</table>";
            $codigoHTML.='
            </table>
            </body>
            </html>';

          }else{
        ?>
          <div class="col-xs-12">
            <form name="ingresar3" action="" method="POST">
              <?php 
                $contador = 1; 
                $colspan = count($arrayNombre)+1;
              ?>
              <?php //echo "el array_columna es: ".$arrayColumna[0]; ?>
              <?php //echo "<br> La consulta columna es: ".$consultaColumna."<br>"; 

                echo "<div class='col-xs-6 well'>
                  <p class='alert alert-info' style='padding:7px;'>3.- Realizar busqueda dentro de los datos seleccionados.</p>
                  
                  <input class='form-control' name='campoBuscar' type='text'>
                  <button type='submit' class='btn btn-success'>Buscar</button>
                  <input name='arrayColumna2' type='hidden' value='".serialize($arrayNombre)."'>
                  <input name='consultaColumna' type='hidden' value='".$consultaColumna."'>
                  <input name='nombreTabla' type='hidden' value='".$tabla_nombre."'>
                  <input name='consulta3' type='hidden' value='3'>
                </div>";
              ?>
              <div class="col-xs-6 well">
                <div class="col-xs-3">
                  <a href="#" onclick="document.ingresar4.submit()"><img src="../../img/pdf.png"></a>
                  <a href="#" onclick="document.ingresar5.submit()"><img src="../../img/excel.png"></a>
                </div>
                <div class="col-xs-9">
                  <p class="alert alert-info" style="padding:7px;font-size:10px;">El tiempo de generación puede variar de acuerdo al numero de datos consultados.</p>
                </div>
              </div>
            </form>
            <?php
                echo "<table class='table table-bordered table-hover'>";
                $codigoHTML.= '<table width="100%" border="1" cellspacing="0" cellpadding="0" style="font-size:10px;">';

                echo "<thead>";
                $codigoHTML.= "<thead>";
                echo "
                  <tr>                       
                    <th style='text-align:center;' colspan='".$colspan."'>
                    Lista $tabla_nombre
                    </th>
                  </tr>";
                $codigoHTML.= '
                  <tr>
                    <th style="text-align:center;" colspan="'.$colspan.'">Lista '.$tabla_nombre.'</th>
                  </tr>
                ';

                echo "<tr>";
                $codigoHTML.= "<tr>";
                  echo "<th>Nº</th>";
                  $codigoHTML.= "<th>Nº</th>";

                for ($i=0; $i < count($arrayNombre) ; $i++) { 
                  if($arrayNombre[$i] != ""){
                    echo "<th>".$arrayNombre[$i]."</th>";
                    $codigoHTML.= "<th>".$arrayNombre[$i]."</th>";
                  }
                  
                }
                echo "</tr>";
                $codigoHTML.= "</tr>";
                echo "</thead>";
                $codigoHTML.= "</thead>";

                echo "<tbody>";
                $codigoHTML.= "<tbody>";
                $queryColumna = mysql_query($consultaColumna,$dspp) or die(mysql_error());

                while($columna = mysql_fetch_assoc($queryColumna)){
                  echo "<tr>";
                  $codigoHTML.= "<tr>";
                  echo "<td>".$contador."</td>";
                  $codigoHTML.= "<td>".$contador."</td>";
                  for ($i=0; $i < count($arrayNombre) ; $i++) { 
                    if($arrayNombre[$i] != ""){

                      echo "<td>".$columna[$arrayNombre[$i]]."</td>";
                      $codigoHTML.= "<td>".$columna[$arrayNombre[$i]]."</td>";
                    }
                    //echo $arrayNombre[$i];
                    
                  }
                  echo "</tr>";
                  $codigoHTML.= "</tr>";
                  $contador++;
                }
                echo "</tbody>";
                $codigoHTML.= "</tbody>";
                echo "</table>";
                $codigoHTML.= "</table>";


              ?>
          </div>
        <?php
          }
        }

        $codigoHTML.='</body></html>';
        ?>

        <form name="ingresar4" action="../../reporte.php" method="POST">
          <?php
            echo "<input type='hidden' name='codigoHTML' value='".$codigoHTML."'>";
            echo "<input type='hidden' name='generarPDF' value='pdf'>";
          ?>
        </form>
        <form name="ingresar5" action="../../reporte.php" method="POST">
          <?php
            echo "<input type='hidden' name='codigoHTML' value='".$codigoHTML."'>";
            echo "<input type='hidden' name='generarExcel' value='excel'>";
          ?>
        </form>


      </div>
    </div>


