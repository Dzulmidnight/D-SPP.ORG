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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
mysql_select_db($database_dspp, $dspp);
$status = 1; //posible cliente

$row_clientes = mysql_query("SELECT contactos_crm.idcontacto, contactos_crm.nombre, contactos_crm.apellido, contactos_crm.compania, contactos_crm.telefono1, contactos_crm.telefono2, contactos_crm.email1, contactos_crm.email2, contactos_crm.nivel_interes, nivel_interes.nivel, contactos_crm.creado_por, adm.username FROM contactos_crm INNER JOIN adm ON contactos_crm.creado_por = adm.idadm INNER JOIN nivel_interes ON contactos_crm.nivel_interes = nivel_interes.idnivel_interes WHERE status = $status", $dspp) or die(mysql_error());

 ?>
<table class="table table-condensed" style="font-size:12px;">
	<thead>
		<tr>
			<th><button class="btn btn-default">Enviar Correo</button></th>
			<th><button class="btn btn-default">Eliminar</button></th>
			<th><button class="btn btn-default">Exportar <span class="glyphicon glyphicon-print" aria-hidden="true"></span></button></th>
		</tr>
		<tr>
			<th># <input type="checkbox"></th>
			<th>Nivel Interes</th>
			<th>Nombre Posibles Cliente</th>
			<th>Empresa</th>
			<th>Telefono</th>
			<th>Email</th>
			<th>Agregado por</th>
			<th><input type="text" placeholder="buscar"></th>
		</tr>	
	</thead>
	<tbody>
		<?php
		$contador = 1;
		while($clientes = mysql_fetch_assoc($row_clientes)){
			switch ($clientes['nivel_interes']) {
				case '1': //bajo
					$clase_interes = 'info';
					break;
				case '2': //normal
					$clase_interes = 'warning';
					break;
				case '3': //alto
					$clase_interes = 'danger';
					break;		
				
				default:
					# code...
					break;
			}
		?>
		<tr>
			<td><?php echo $contador; ?> <input type="checkbox"></td>
			<td class="<?php echo $clase_interes; ?>"><?php echo $clientes['nivel']; ?></td>
			<td><?php echo $clientes['nombre'].' '.$clientes['apellido']; ?></td>
			<td><?php echo $clientes['compania']; ?></td>
			<td>
				<?php 
					echo "$clientes[telefono1] <br/>";
					echo "$clientes[telefono2] <br/>"; 
				?>
			</td>
			<td>
				<?php 
					echo "$clientes[email1] <br/>";
					echo "$clientes[email2] <br/>";
				?>
			</td>
			<td><?php echo $clientes['username']; ?></td>
		</tr>
		<?php
		$contador++;
		}
		 ?>
	</tbody>
</table>