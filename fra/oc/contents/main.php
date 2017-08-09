<?php
	$query = "SELECT * FROM oc WHERE idoc = $_SESSION[idoc]";
	$ejecutar = mysql_query($query,$dspp) or die(mysql_error());
	$oc = mysql_fetch_assoc($ejecutar);

	$notificacion = 'Maintenant, vous pouvez à nouveau entrer de nouvelles demandes depuis le menu OC. Cette option est accessible dans la section DEMANDES via le bouton "Nouvelle demande"';
 ?>

<h4>Menu principal OC</h4>

<?php if(!empty($notificacion)){ ?>
	<div class="col-xs-12 alert alert-info text-center"><h3>Mise à jour: <br><?php echo $notificacion; ?></h3></div>
<?php } ?>

<?php if(!isset($oc['email1'])){ ?>
	<div class="col-xs-12 alert alert-danger text-center">
		<h3>
			Aucune adresse de courrier électronique n'a été détectée, merci d'entrer une adresse courriel pour recevoir les notifications du système D-SPP.
			<br><hr><a href="?OC&detail" class="btn btn-warning">Entrer le courriel</a>
		</h3>
	</div>
<?php } ?>
