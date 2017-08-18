<?php 
if(isset($_GET['ordinary'])){
	include('solicitud_ordinaria.php');
}else if(isset($_GET['collective'])){
	include('solicitud_colectiva.php');
}else{
?>
<div class="row" style="margin-top:5em;">
	<div class="col-md-6 text-center">
		<p class="text-justify well">
			<b>Demande de Certification pour les Organisations de Petits Producteurs</b><br>
			Cette demande doit être remplie par les petites organisations de producteurs qui postulent la certification SPP selon la procédure conventionnelle.
		</p>
		<a class="btn btn-success" style="width:50%" href="?SOLICITUD&add&ordinary">Nouvelle demande conventionnelle</a>
	</div>
	<div class="col-md-6 text-center">
		<p class="text-justify well">
			<b>Demande de certification collective pour les petites organisations de producteurs</b><br>
			Cette demande doit être remplie par les petites organisations de producteurs qui demandent la certification SPP dans le cadre de la procédure de certification collective.
		</p>
		<a class="btn btn-warning" style="width:50%" href="?SOLICITUD&add&collective">Nouvelle demande collective</a>
	</div>
</div>

<?php
}
 ?>