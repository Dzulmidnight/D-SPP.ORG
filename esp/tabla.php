<?php
require_once('../Connections/dspp.php');
require_once('../Connections/mail.php');
mysql_select_db($database_dspp, $dspp);




function mayuscula($variable) {
  $variable = strtr(strtoupper($variable),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
  return $variable;
}


  $query = "SELECT opp.abreviacion, comprobante_pago.*, solicitud_certificacion.fecha_registro as 'fecha_solicitud', solicitud_certificacion.tipo_solicitud, membresia.idmembresia, membresia.idmembresia, membresia.fecha_registro as 'fecha_membresia'  FROM `comprobante_pago` INNER JOIN membresia ON comprobante_pago.idcomprobante_pago = membresia.idcomprobante_pago INNER JOIN opp ON membresia.idopp = opp.idopp INNER JOIN solicitud_certificacion ON membresia.idsolicitud_certificacion = solicitud_certificacion.idsolicitud_certificacion WHERE `monto` LIKE '%562%' ORDER BY opp.abreviacion";
  $consulta = mysql_query($query, $dspp) or die(mysql_error());
  //$row_membresia = mysql_fetch_assoc($consulta);

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
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../bootstrap/css/bootstrap-theme.css" rel="stylesheet">
    <link href="../bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="../bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>


  </head>
<body>
<?php
$salida = "";
$query = "SELECT contactos.*, empresa.abreviacion AS 'abreviacion_empresa', empresa.pais FROM contactos INNER JOIN empresa ON contactos.idempresa = empresa.idempresa GROUP BY contactos.nombre ORDER BY contactos.nombre ASC";

if(isset($_POST['consulta'])){
  $q = $_POST['consulta'];
  $query = "SELECT contactos.*, empresa.abreviacion AS 'abreviacion_empresa', empresa.pais FROM contactos INNER JOIN empresa ON contactos.idempresa = empresa.idempresa WHERE contactos.nombre LIKE '%".$q."%' OR empresa.pais LIKE '%".$q."%' OR empresa.abreviacion LIKE '%".$q."%' GROUP BY contactos.nombre ORDER BY contactos.nombre ASC";
}
$resultado = mysql_query($query,$dspp) or die(mysql_error());
$total = mysql_num_rows($resultado);
$contador = 1;


?>
  <div class="col-md-12">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>ORGANIZACIÓN</th>
          <th>FECHA SOLICITUD</th>
          <th>TIPO SOLICITUD</th>
          <th>ESTATUS COMPROBANTE</th>
          <th>MONTO</th>
          <th>FECHA DE PAGO</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        while($membresia = mysql_fetch_assoc($consulta)){
        ?>
          <tr>
            <td>
              <?= $membresia['abreviacion']; ?>
            </td>
            <td>
              <?= date('d/m/Y', $membresia['fecha_solicitud']);?>
            </td>
            <td>
              <?= $membresia['tipo_solicitud']; ?>
            </td>
            <td>
              <?= $membresia['estatus_comprobante']; ?>
            </td>
            <td>
              <?= $membresia['monto']; ?>
            </td>
            <td>
              <?php 
              if(isset($membresia['fecha_membresia'])){
echo date('d/m/Y', $membresia['fecha_membresia']);
              }
               ?>
            </td>
          </tr>
        <?php
        }
         ?>
      </tbody>
    </table>
  </div>
</body>
</html>
