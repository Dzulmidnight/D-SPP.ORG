<?php
require_once('Connections/dspp.php');
mysql_select_db($database_dspp, $dspp);




function mayuscula($variable) {
  $variable = strtr(strtoupper($variable),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
  return $variable;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="img/FUNDEPPO.png">
    <title>SPP GLOBAL | D-SPP</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap-theme.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>


  </head>
<body>
<?php 
$query2 = "SELECT contactos.*, opp.nombre AS 'nombreOPP', opp.abreviacion AS 'abreviacionOPP', opp.pais AS 'paisOPP', empresa.abreviacion AS 'abreviacionEmpresa', empresa.pais AS 'paisEmpresa', lista_contactos.nombre AS 'nombreLista' FROM contactos LEFT JOIN opp ON contactos.idopp = opp.idopp LEFT JOIN empresa ON contactos.idempresa = empresa.idempresa LEFT JOIN lista_contactos ON contactos.lista_contactos = lista_contactos.idlista_contactos GROUP BY contactos.nombre ORDER BY contactos.nombre";
$query = mysql_query("SELECT contactos.*, opp.nombre AS 'nombreOPP', opp.abreviacion AS 'abreviacionOPP', opp.pais AS 'paisOPP', empresa.abreviacion AS 'abreviacionEmpresa', empresa.pais AS 'paisEmpresa', lista_contactos.nombre AS 'nombreLista' FROM contactos LEFT JOIN opp ON contactos.idopp = opp.idopp LEFT JOIN empresa ON contactos.idempresa = empresa.idempresa LEFT JOIN lista_contactos ON contactos.lista_contactos = lista_contactos.idlista_contactos GROUP BY contactos.nombre ORDER BY contactos.nombre", $dspp) or die(mysql_error());
$numContactos = mysql_num_rows($query);
echo '<h4>Contactos: '.$numContactos.'</h4>';
?>
<table class="table table-bordered" style="font-size:12px;">
  <thead>
    <tr>
      <th>
        Exportar Lista
        <!--<a href="#" target="_blank" onclick="document.formulario1.submit()"><img src="../../img/pdf.png"></a>-->
        <a href="#" onclick="document.formulario2.submit()"><img src="img/excel.png"></a>

<<<<<<< Updated upstream

        <form name="formulario2" method="POST" action="reportes/lista_contactos.php">
          <input type="hidden" name="lista_excel" value="2">
          <input type="hidden" name="query_excel" value="<?php echo $query2; ?>">
        </form>
      </th>
    </tr>
    <tr>
      <th>Tipo</th>
      <th>Organización</th>
      <th>Pais</th>
      <th>Nombre</th>
      <th>Puesto</th>
      <th>Correo</th>
      <th>Telefono</th>
    </tr>
  </thead>
  <tbody>
    <?php
      while($contacto = mysql_fetch_assoc($query)){
      ?>
        <tr>
          <td>
            <?php
            $tipo = '';
            if(!empty($contacto['idopp'])){
              $tipo = 'OPP';
            }else if(!empty($contacto['idempresa'])){
              $tipo = 'Empresa';
            }else if(!empty($contacto['lista_contactos'])){
              $tipo = $contacto['nombreLista'];
            }
             ?>
            <?php echo $tipo; ?>
          </td>
          <td>
            <?php 
            if(isset($contacto['abreviacionOPP'])){
              echo $contacto['abreviacionOPP'];
            }else{
              echo '<p style="color:red">'.$contacto['abreviacionEmpresa'].'</p>';
            }
             ?>
          </td>
          <td>
          <?php 
          if(isset($contacto['paisOPP'])){
            echo $contacto['paisOPP'];
          }else{
            echo $contacto['paisEmpresa'];
          }
           ?>
          </td>
          <td><?php echo mayuscula($contacto['nombre']).'ID: '.$contacto['idcontacto']; ?></td>
          <td><?php echo mayuscula($contacto['cargo']); ?></td>
          <td>
            <?php echo '<p>'.$contacto['email1'].'</p>'; ?>
            <?php echo '<p>'.$contacto['email2'].'</p>'; ?>
          </td>
          <td>
            <?php echo '<p>'.$contacto['telefono1'].'</p>'; ?>
            <?php echo '<p>'.$contacto['telefono2'].'</p>'; ?>
          </td>
        </tr>
      <?php
      }
      ?>
  </tbody>
</table>

=======
$query = mysql_query("SELECT contactos.*, opp.abreviacion FROM contactos LEFT JOIN opp ON contactos.idopp = opp.idopp ORDER BY nombre", $dspp) or die(mysql_error());
$totalContactos = mysql_num_rows($query);
echo '<h4>'.$totalContactos.'</h4>';
?>
<table class="table table-bordered table-condensed" style="font-size: 12px;">
  <thead>
    <tr>
      <th>TIPO</th>
      <th>NOMBRE</th>
      <th>ORGANIZACIÓN</th>
    </tr>
  </thead>
  <tbody>
    <?php 
    while($contactos = mysql_fetch_assoc($query)){
    ?>
      <tr>
        <td><?php echo 'TIPO'; ?></td>
        <td><?php echo $contactos['nombre']; ?></td>
        <td><?php echo $contactos['abreviacion']; ?></td>
      </tr>
    <?php
    }
     ?>
  </tbody>
</table>
>>>>>>> Stashed changes
</body>
</html>
