<?php 
require_once('../Connections/dspp.php');
require_once('../Connections/mail.php');


if(isset($_POST['botonBuscar']) && $_POST['botonBuscar'] == 1){
  foreach ($_POST['actorCheckbox'] as $actor) {
    $query = "SELECT * FROM $actor";
    echo "<script>alert('la consulta es: $query')</script>"; 
  }
}


$query = "SELECT contacto.idopp AS 'idoppContacto', contacto.idoc AS 'idocContacto', contacto.idcom AS 'idcomContacto', contacto.contacto, contacto.cargo, contacto.telefono1, contacto.telefono2, contacto.email1, contacto.email2, opp.idopp, opp.idf, opp.nombre, opp.abreviacion, opp.email AS 'emailOPP', com.idcom, com.nombre, com.abreviacion, com.email AS 'emailCOM', oc.idoc, oc.abreviacion, oc.email AS 'emailOC', oc.email2 FROM contacto LEFT JOIN opp ON contacto.idopp = opp.idopp LEFT JOIN com ON contacto.idcom = com.idcom LEFT JOIN oc ON contacto.idoc = oc.idoc";
$ejecutar = mysql_query($query,$dspp) or die(mysql_error());


$queryOPP = "SELECT contacto.idopp AS 'idoppContacto', contacto.contacto, contacto.cargo, contacto.telefono1, contacto.telefono2, contacto.email1, contacto.email2, opp.idopp, opp.idf, opp.nombre, opp.abreviacion, opp.email AS 'emailOPP' FROM contacto INNER JOIN opp ON contacto.idopp = opp.idopp ";
$ejecutarOPP = mysql_query($queryOPP,$dspp) or die(mysql_error());


$queryCOM = "SELECT contacto.idcom AS 'idcomContacto', contacto.contacto, contacto.cargo, contacto.telefono1, contacto.telefono2, contacto.email1, contacto.email2,  com.idcom, com.nombre, com.abreviacion, com.email AS 'emailCOM' FROM contacto INNER JOIN com ON contacto.idcom = com.idcom ";
$ejecutarCOM = mysql_query($queryCOM,$dspp) or die(mysql_error());


$queryOC = "SELECT contacto.idoc AS 'idocContacto', contacto.contacto, contacto.cargo, contacto.telefono1, contacto.telefono2, contacto.email1, contacto.email2, oc.idoc, oc.abreviacion, oc.email AS 'emailOC', oc.email2 FROM contacto INNER JOIN oc ON contacto.idoc = oc.idoc";
$ejecutarOC = mysql_query($queryOC,$dspp) or die(mysql_error());


$queryOpp = "SELECT idopp, idf, nombre, abreviacion, email FROM opp";
$ejecutarOpp = mysql_query($queryOpp,$dspp) or die(mysql_error());

$queryCom = "SELECT idcom, idf, nombre, abreviacion, email FROM com";
$ejecutarCom = mysql_query($queryCom,$dspp) or die(mysql_error());

$queryOc = "SELECT idoc, idf, nombre, abreviacion, email FROM oc";
$ejecutarOc = mysql_query($queryOc,$dspp) or die(mysql_error());


 ?>

<div class="row">
  
  <form class="form-inline" action="" method="POST">
    <div class="col-md-12">
      <div class="col-md-4 input-group"> 
        <input type="text" class="form-control" name="campoBuscar" placeholder="email@example.com" aria-describedby="sizing-addon2">
      </div>
      <button type="submit" class="btn btn-primary" name="botonBuscar" value="1">Buscar Correo</button>

      <div class="col-md-12">
        <label class="col-md-2 alert">
          <input type="checkbox" name="todos" id="todos" value="todos" onclick="ocultarTodos()"> TODOS
        </label>
        <label class="col-md-2 alert">
          <input type="checkbox" name="actorCheckbox[]" id="checkbox1" value="adm" onclick="ocultar()"> ADM
        </label>


        <label class="alert alert-info col-md-2">
            <input type="checkbox" name="actorCheckbox[]" id="checkbox2" value="opp" onclick="ocultar()"> OPP
        </label>    

        <label class="col-md-2 alert alert-warning">
          <input type="checkbox" name="actorCheckbox[]" id="checkbox3" value="com" onclick="ocultar()"> EMPRESAS
        </label>
        
        <label class="col-md-2 alert alert-success">
          <input type="checkbox" name="actorCheckbox[]" id="checkbox4" value="oc" onclick="ocultar()"> OC         
        </label>
        

      </div>
    </div>

  </form>

  <hr>
  <div class="col-md-12">

    <table class="table table-condensed table-bordered text-center" style="font-size:12px;">
      <thead>
        <tr>
          <th class="text-center" colspan="11" style="background:#2c3e50;color:#ecf0f1;">CONTACTOS OPP</th>
        </tr>
        <tr>
          <th class='text-center'>Nº</th>
          
          <th class='text-center'>#SPP</th>
          <th class='text-center'>NOMBRE</th>
          <th class='text-center'>ABREVIACIÓN</th>
          <th class='text-center'>EMAIL</th>
        </tr>
      </thead>

      <tbody>
        <?php 
        $contador = 1;
        while($datos = mysql_fetch_assoc($ejecutarOpp)){
          echo "<tr class='alert alert-info'>";
            echo "<td>$contador</td>";
            
            echo "<td>$datos[idf]</td>";
            echo "<td>$datos[nombre]</td>";
            echo "<td>$datos[abreviacion]</td>";
            echo "<td>$datos[email]</td>";
          echo "</tr>";

          $queryContacto = "SELECT contacto.idopp AS 'ID', contacto.contacto, contacto.cargo, contacto.telefono1, contacto.telefono2, contacto.email1, contacto.email2 FROM contacto WHERE contacto.idopp = $datos[idopp]";
          $ejecutarOtra = mysql_query($queryContacto,$dspp) or die(mysql_error());
          $total = mysql_num_rows($ejecutarOtra);
          if(empty($total)){
            echo "<tr style='background:#e74c3c;color:white'><td colspan='6'><table><tr><td>No se encontraron contactos</td></tr></table></td></tr>";
          }else{
            echo "<tr>";
              echo "<td colspan='6'>";
                echo "<table class='table table-condensed table-striped text-center'>";
                  echo "<thead>";
                    echo "<tr>";
                      echo "<th class='text-center'>Nº</th>";
                      
                      echo "<th class='text-center'>CONTACTO</th>";
                      echo "<th class='text-center'>CARGO</th>";
                      echo "<th class='text-center'>TELEFONO 1</th>";
                      echo "<th class='text-center'>TELEFONO 2</th>";
                      echo "<th class='text-center'>EMAIL 1</th>";
                      echo "<th class='text-center'>EMAIL 2</th>";
                    echo "</tr>";
                  echo "</thead>";
                  echo "<tbody>";
                  $contador2 = 1;
                while($contactos = mysql_fetch_assoc($ejecutarOtra)){
                  echo "<tr>";
                    echo "<td>$contador2</td>";
                    
                    echo "<td>$contactos[contacto]</td>";
                    echo "<td>$contactos[cargo]</td>";
                    echo "<td>$contactos[telefono1]</td>";
                    echo "<td>$contactos[telefono2]</td>";
                    echo "<td>$contactos[email1]</td>";
                    echo "<td>$contactos[email2]</td>";
                  echo "</tr>";
                  $contador2++;

                }
                $contador++;
                  echo "</tbody>";
                echo "</table>";
              echo "</td>";
            echo "</tr>";
          }
        }
         ?>
      </tbody>
    </table>

    <table class="table table-condensed table-bordered" style="font-size:12px;">
      <thead>
        <tr>
          <th class="text-center" colspan="11" style="background:#2c3e50;color:#ecf0f1;">CONTACTOS EMPRESA</th>
        </tr>
        <tr>
          <th class='text-center'>Nº</th>
          
          <th class='text-center'>#SPP</th>
          <th class='text-center'>NOMBRE</th>
          <th class='text-center'>ABREVIACIÓN</th>
          <th class='text-center'>EMAIL</th>
        </tr>
      </thead>

      <tbody>
        <?php 
        $contador = 1;
        while($datos = mysql_fetch_assoc($ejecutarCom)){
          echo "<tr class='alert alert-warning'>";
            echo "<td>$contador</td>";
            
            echo "<td>$datos[idf]</td>";
            echo "<td>$datos[nombre]</td>";
            echo "<td>$datos[abreviacion]</td>";
            echo "<td>$datos[email]</td>";
          echo "</tr>";

          $queryContacto = "SELECT contacto.idcom AS 'ID', contacto.contacto, contacto.cargo, contacto.telefono1, contacto.telefono2, contacto.email1, contacto.email2 FROM contacto WHERE contacto.idcom = $datos[idcom]";
          $ejecutarOtra = mysql_query($queryContacto,$dspp) or die(mysql_error());
          $total = mysql_num_rows($ejecutarOtra);
          if(empty($total)){
            echo "<tr style='background:#e74c3c;color:white'><td colspan='6'><table><tr><td>No se encontraron contactos</td></tr></table></td></tr>";
          }else{
            echo "<tr>";
              echo "<td colspan='6'>";
                echo "<table class='table table-condensed table-striped text-center'>";
                  echo "<thead>";
                    echo "<tr>";
                      echo "<th class='text-center'>Nº</th>";
                      
                      echo "<th class='text-center'>CONTACTO</th>";
                      echo "<th class='text-center'>CARGO</th>";
                      echo "<th class='text-center'>TELEFONO 1</th>";
                      echo "<th class='text-center'>TELEFONO 2</th>";
                      echo "<th class='text-center'>EMAIL 1</th>";
                      echo "<th class='text-center'>EMAIL 2</th>";
                    echo "</tr>";
                  echo "</thead>";
                  echo "<tbody>";
                  $contador2 = 1;
                while($contactos = mysql_fetch_assoc($ejecutarOtra)){
                  echo "<tr>";
                    echo "<td>$contador2</td>";
                    
                    echo "<td>$contactos[contacto]</td>";
                    echo "<td>$contactos[cargo]</td>";
                    echo "<td>$contactos[telefono1]</td>";
                    echo "<td>$contactos[telefono2]</td>";
                    echo "<td>$contactos[email1]</td>";
                    echo "<td>$contactos[email2]</td>";
                  echo "</tr>";
                  $contador2++;

                }
                $contador++;
                  echo "</tbody>";
                echo "</table>";
              echo "</td>";
            echo "</tr>";
          }

        }
         ?>
      </tbody>
    </table>

    <table class="table table-condensed table-bordered" style="font-size:12px;">
      <thead>
        <tr>
          <th class="text-center" colspan="11" style="background:#2c3e50;color:#ecf0f1;">CONTACTOS OC</th>
        </tr>
        <tr>
          <th class='text-center'>Nº</th>
          
          <th class='text-center'>#SPP</th>
          <th class='text-center'>NOMBRE</th>
          <th class='text-center'>ABREVIACIÓN</th>
          <th class='text-center'>EMAIL</th>
        </tr>
      </thead>

      <tbody>
        <?php 
        $contador = 1;
        while($datos = mysql_fetch_assoc($ejecutarOc)){
          echo "<tr class='alert alert-success'>";
            echo "<td>$contador</td>";
            
            echo "<td>$datos[idf]</td>";
            echo "<td>$datos[nombre]</td>";
            echo "<td>$datos[abreviacion]</td>";
            echo "<td>$datos[email]</td>";
          echo "</tr>";

          $queryContacto = "SELECT contacto.idoc AS 'ID', contacto.contacto, contacto.cargo, contacto.telefono1, contacto.telefono2, contacto.email1, contacto.email2 FROM contacto WHERE contacto.idoc = $datos[idoc]";
          $ejecutarOtra = mysql_query($queryContacto,$dspp) or die(mysql_error());
          $total = mysql_num_rows($ejecutarOtra);
          if(empty($total)){
            echo "<tr style='background:#e74c3c;color:white'><td colspan='6'><table><tr><td>No se encontraron contactos</td></tr></table></td></tr>";
          }else{
            echo "<tr>";
              echo "<td colspan='6'>";
                echo "<table class='table table-condensed table-striped text-center'>";
                  echo "<thead>";
                    echo "<tr>";
                      echo "<th class='text-center'>Nº</th>";
                      
                      echo "<th class='text-center'>CONTACTO</th>";
                      echo "<th class='text-center'>CARGO</th>";
                      echo "<th class='text-center'>TELEFONO 1</th>";
                      echo "<th class='text-center'>TELEFONO 2</th>";
                      echo "<th class='text-center'>EMAIL 1</th>";
                      echo "<th class='text-center'>EMAIL 2</th>";
                    echo "</tr>";
                  echo "</thead>";
                  echo "<tbody>";
                  $contador2 = 1;
                while($contactos = mysql_fetch_assoc($ejecutarOtra)){
                  echo "<tr>";
                    echo "<td>$contador2</td>";
                    
                    echo "<td>$contactos[contacto]</td>";
                    echo "<td>$contactos[cargo]</td>";
                    echo "<td>$contactos[telefono1]</td>";
                    echo "<td>$contactos[telefono2]</td>";
                    echo "<td>$contactos[email1]</td>";
                    echo "<td>$contactos[email2]</td>";
                  echo "</tr>";
                  $contador2++;

                }
                $contador++;
                  echo "</tbody>";
                echo "</table>";
              echo "</td>";
            echo "</tr>";
          }

        }
         ?>
      </tbody>
    </table>




  </div>






</div>

<script>
  function ocultar()
  {
    document.getElementById('todos').checked = 0;
  }
  function ocultarTodos()
  {
    document.getElementById('checkbox1').checked = 0;
    document.getElementById('checkbox2').checked = 0;
    document.getElementById('checkbox3').checked = 0;
    document.getElementById('checkbox4').checked = 0;

  }

</script>