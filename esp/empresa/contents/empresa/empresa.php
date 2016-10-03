<ul class="nav nav-pills">
	<li role="presentation" <?php if(isset($_GET['detail'])){ echo "class='active'"; } ?>>
		<a href="?EMPRESA&detail"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> Mi Cuenta</a>
	</li>

	<li role="presentation" <?php if(isset($_GET['contacts'])){ echo "class='active'"; } ?>>
		<a href="?EMPRESA&contacts"><span class="glyphicon glyphicon-book" aria-hidden="true"></span> Mis Contactos</a>
	</li>
</ul>

<?php 

if(isset($_GET['select'])){
	include ("empresa_select.php");
}
else if(isset($_GET['add'])){
	include ("empresa_add.php");
}
else if(isset($_GET['detail'])){
	include ("empresa_detail.php");
}else if(isset($_GET['contacts'])){
	include("empresa_contacts.php");
}
?>