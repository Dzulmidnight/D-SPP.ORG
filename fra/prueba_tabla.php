<?php require_once('Connections/dspp.php');
mysql_select_db($database_dspp, $dspp);

 ?>

<!DOCTYPE html>
<html lang="es">
  <head>
<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
  <script>tinymce.init({ selector:'textarea' });</script>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>D-SPP.ORG</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap-theme.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <!--<script src="../js/fileinput.min.js" type="text/javascript"></script>
    <script src="../js/fileinput_locale_es.js"></script>-->


     <!---LIBRERIAS DE Bootstrap File Input-->

    <script type="text/javascript" src="js/bootstrap-filestyle.js"></script>
    <link rel="stylesheet" href="chosen/chosen.css">


    <!------------------- bootstrap-switch -------------->

      <link href="bootstrap-switch-master/bootstrap-switch.css" rel="stylesheet">
      <script src="bootstrap-switch-master/bootstrap-switch.js"></script>

    <!------------------- bootstrap-switch -------------->    

  <style>
  .chosen-container-multi .chosen-choices li.search-field input[type="text"]{padding: 15px;}
  </style>
 
  </head>

  <body>
    <?php 
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

          //$tabla = $_POST['tabla'];
          //$consultaTabla = "SELECT * FROM ".$columna."";

          //$_GLOBALS["tabla"] = $_POST["tabla"];
        }

        if(isset($_POST['consulta3']) && $_POST['consulta3'] == 3){

  //$query_opp = "SELECT *, opp.nombre AS 'nombreOPP', status.idstatus, status.nombre AS 'nombreStatus' FROM opp LEFT JOIN status ON opp.estado = status.idstatus  WHERE (idf LIKE '%$palabraClave%') OR (opp.nombre LIKE '%$palabraClave%') OR (opp.abreviacion LIKE '%$palabraClave%') OR (sitio_web LIKE '%$palabraClave%') OR (email LIKE '%$palabraClave%') OR (pais LIKE '%$palabraClave%') OR (razon_social LIKE '%$palabraClave%') OR (direccion_fiscal LIKE '%$palabraClave%') OR (rfc LIKE '%$palabraClave%') ORDER BY opp.nombre ASC";

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


      /*if(isset($_POST['tabla'])){
        if(isset($_POST['registros3'])){

          $tabla = $_POST['tablaOculta'];
          $registros = $_POST['registros'];
          $consultaRegistro = "";
          if($registros[0] == "todos"){
            $consultaRegistro = "SELECT * FROM ".$tabla.";";
          }else{
                $consultaRegistro.="SELECT ";
                for ($i=0;$i<count($registros);$i++)    
                {     
                $consultaRegistro.= $registros[$i]; 
                  if($i<(count($registros)-1)){
                    $consultaRegistro.= " , ";
                  }
                } 
                $consultaRegistro.=" FROM ".$tabla.";";
          }
          
        }else{

          $tabla = $_POST['tabla'];

          $query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".$tabla."'";
          $ejecutar = mysql_query($query,$dspp) or die(mysql_error());
        

          $query_informacion = "SELECT * FROM $tabla";
          $ejecutar_informacion = mysql_query($query_informacion,$dspp) or die(mysql_error());
          $numero_filas = mysql_num_rows($ejecutar);
        }
      }*/
     ?>


    <!--<form action="" name="ingresar" method="POST"  accept-charset="UTF-8" enctype="application/x-www-form-urlencoded">-->

      <div class="container">
        <div class="row">
          <div class="col-xs-12">
          
            <div class="col-xs-6 well">
              <form name="tabla2" method="POST">
                <select style="padding:30px;" class="form-control chosen-select-deselect" data-placeholder="Seleccione una Tabla" name="tabla" id="" >
                  
                  <!--<option value="certificado">Certificado</option>-->
                  <option value="">Seleccionar</option>
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
                <form name="ingresar2" method="POST">
                  <select class="form-control chosen-select-deselect" data-placeholder="Seleccione una Tabla" name="columna[]" id="" multiple required>
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
                    <a href="#" onclick="document.ingresar4.submit()"><img src="../img/pdf.png"></a>
                    <a href="#" onclick="document.ingresar5.submit()"><img src="../img/excel.png"></a>
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
                      <input class='form-control' name='campoBuscar' type='text'>
                      <button type='submit' class='btn btn-success'>Buscar</button>
                      <input name='arrayColumna2' type='hidden' value='".serialize($arrayNombre)."'>
                      <input name='consultaColumna' type='hidden' value='".$consultaColumna."'>
                      <input name='nombreTabla' type='hidden' value='".$tabla_nombre."'>
                      <input name='consulta3' type='hidden' value='3'>
                    </div>";
                  ?>
                  <div class="col-xs-6 well">
                    <a href="#" onclick="document.ingresar4.submit()"><img src="../img/pdf.png"></a>
                    <a href="#" onclick="document.ingresar5.submit()"><img src="../img/excel.png"></a>
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
 
            <form name="ingresar4" action="../reporte.php" method="POST">
              <?php
                echo "<input type='hidden' name='codigoHTML' value='".$codigoHTML."'>";
                echo "<input type='hidden' name='generarPDF' value='pdf'>";
              ?>
            </form>
            <form name="ingresar5" action="../reporte.php" method="POST">
              <?php
                echo "<input type='hidden' name='codigoHTML' value='".$codigoHTML."'>";
                echo "<input type='hidden' name='generarExcel' value='excel'>";
              ?>
            </form>


          </div>
        </div>
      </div> 
    <!--</form>-->

  </body>
</html>




  <script src="chosen/chosen.jquery.js" type="text/javascript"></script>
  <script type="text/javascript">
    var config = {
      '.chosen-select'           : {},
      '.chosen-select-deselect'  : {allow_single_deselect:true},
      '.chosen-select-no-single' : {disable_search_threshold:10},
      '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
      '.chosen-select-width'     : {width:"95%"}
    }
    for (var selector in config) {
      $(selector).chosen(config[selector]);
    }
  </script>