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
			<b>Application for Certification for Small Producers’Organizations</b><br>
			This application should be filled by the Small Producer’s Organizations which request the SPP Certification through the conventional procedure.
		</p>
		<a class="btn btn-success" style="width:50%" href="?SOLICITUD&add&ordinary">New Conventional Application</a>
	</div>
	<div class="col-md-6 text-center">
		<p class="text-justify well">
			<b>Application for Collective Certification for Small Producers’ Organizations</b><br>
			This application should be filled by the Small Producers’ Organizations which request the SPP Certification through the Collective Certification procedure.
		</p>
		<a class="btn btn-warning" style="width:50%" href="?SOLICITUD&add&collective">New Collective Aplication</a>
	</div>
</div>

<?php
}
 ?>