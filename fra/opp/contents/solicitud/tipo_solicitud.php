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
			<b>Solicitud de Certificación para Organizaciones de Pequeños Productores</b><br>
			Esta solicitud debe ser llenada por las Organizaciones de Pequeños Productores que solicitan la Certificación SPP a través del procedimiento convencional.
		</p>
		<a class="btn btn-success" style="width:50%" href="?SOLICITUD&add&ordinary">Nueva Solicitud Convencional</a>
	</div>
	<div class="col-md-6 text-center">
		<p class="text-justify well">
			<b>Solicitud de Certificación Colectiva para Organizaciones de Pequeños Productores</b><br>
			Esta solicitud debe ser llenada por las Organizaciones de Pequeños Productores que solicitan la Certificación SPP mediante el procedimiento de Certificación Colectiva.
		</p>
		<a class="btn btn-warning" style="width:50%" href="?SOLICITUD&add&collective">Nueva Solicitud Colectiva</a>
	</div>
</div>

<?php
}
 ?>