<?php 
if(isset($_GET['ordinary'])){
	include('solicitud_ordinaria.php');
}else if(isset($_GET['collective'])){
	include('solicitud_colectiva.php');
}else{
?>
<div class="row" style="margin-top:5em;">
	<div class="col-md-6 text-center">
		<p class="text-justify">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Qui numquam tempora optio asperiores laudantium dolor hic a odit ut vitae. At quae sapiente numquam tempore ut sequi explicabo, nulla delectus.</p>
		<a class="btn btn-success" style="width:50%" href="?SOLICITUD&add&ordinary">New Ordinary Application</a>
	</div>
	<div class="col-md-6 text-center">
		<p class="text-justify">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Error explicabo dolorem deleniti iste, in saepe. Maiores odio aliquam reiciendis impedit ipsa minima enim nemo laboriosam repudiandae, beatae, quas voluptas pariatur.</p>
		<a class="btn btn-warning" style="width:50%" href="?SOLICITUD&add&collective">New Collective Aplication</a>
	</div>
</div>

<?php
}
 ?>