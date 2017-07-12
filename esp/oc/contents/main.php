<?php
	$query = "SELECT * FROM oc WHERE idoc = $_SESSION[idoc]";
	$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
	$oc = mysql_fetch_assoc($ejecutar);

	$notificacion = "Ahora puedes dar de alta nuevas solicitudes desde tu menú de OC, esta opción esta disponible en la sección de SOLICITUDES en el botón \"Nueva Solicitud\"";
 ?>

<h4>Menú principal OC</h4>

<?php if(!empty($notificacion)){ ?>
	<div class="col-xs-12 alert alert-info text-center"><h3>Actualización:<br><?php echo $notificacion; ?></h3></div>
<?php } ?>

<?php if(!isset($oc['email1'])){ ?>
	<div class="col-xs-12 alert alert-danger text-center"><h3>NO SE HA DETECTADO UN CORREO ELECTRÓNICO(EMAIL), POR FAVOR INGRESAR UN CORREO ELECTRÓNICO(EMAIL) PARA <b>VINCULAR LAS NOTIFICACIONES DEL SISTEMA D-SPP</b>. <br><hr><a href="?OC&detail" class="btn btn-warning">INGRESAR EMAIL</a></h3></div>
<?php } ?>

NO SE HA DETECTADO UN CORREO ELECTRÓNICO(EMAIL), POR FAVOR INGRESAR UN CORREO ELECTRÓNICO(EMAIL) PARA <b>VINCULAR LAS NOTIFICACIONES DEL SISTEMA D-SPP. INGRESAR EMAIL