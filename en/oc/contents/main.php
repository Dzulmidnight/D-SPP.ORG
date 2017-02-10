<?php
	$query = "SELECT * FROM oc WHERE idoc = $_SESSION[idoc]";
	$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
	$oc = mysql_fetch_assoc($ejecutar);

	$notificacion = "Now you can put up new applications from your menu CE, this option is available in the APPLICATIONS section in the \"New Application\" button";

 ?>

<h4>Main menu CE</h4>

<?php if(!empty($notificacion)){ ?>
	<div class="col-xs-12 alert alert-info text-center"><h3>Update: <br><?php echo $notificacion; ?></h3></div>
<?php } ?>

<?php if(!isset($oc['email1'])){ ?>
	<div class="col-xs-12 alert alert-danger text-center"><h3>NO EMAIL HAS BEEN DETECTED, PLEASE ENTER AN EMAIL TO <b> LINK D-SPP SYSTEM NOTICES</b>. <br><hr><a href="?OC&detail" class="btn btn-warning">ENTER EMAIL</a></h3></div>
<?php } ?>