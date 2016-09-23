<?php
	$query = "SELECT * FROM oc WHERE idoc = $_SESSION[idoc]";
	$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
	$rowOC = mysql_fetch_assoc($ejecutar);

	$notificacion = "Now you can put up new applications from your menu OC, this option is available in the section APPLICATIONS.  \"New Application\"";
 ?>

<h4>Main Menu OC</h4>

<?php if(!empty($notificacion)){ ?>
	<div class="col-xs-12 alert alert-info text-center"><h3>Update:<br><?php echo $notificacion; ?></h3></div>
<?php } ?>

<?php if(!isset($rowOC['email'])){ ?>
	<div class="col-xs-12 alert alert-danger text-center"><h3>NOT DETECTED AN EMAIL, PLEASE ENTER EMAIL FOR<b> LINKING SYSTEM NOTIFICATIONS D-SPP</b> <br><hr><a href="?OC&detail" class="btn btn-warning">ENTER EMAIL</a></h3></div>
<?php } ?>