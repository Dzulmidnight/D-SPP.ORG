<ul class="nav nav-pills">
	<li role="presentation" <?php if(isset($_GET['detail'])){ echo "class='active'"; } ?>>
		<a href="?OPP&detail"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> Mes informations</a>
	</li>

	<li role="presentation" <?php if(isset($_GET['contacts'])){ echo "class='active'"; } ?>>
		<a href="?OPP&contacts"><span class="glyphicon glyphicon-book" aria-hidden="true"></span> Mes contacts</a>
	</li>
</ul>

<?php 

if(isset($_GET['select'])){
	include ("opp_select.php");
}
else if(isset($_GET['add'])){
	include ("opp_add.php");
}
else if(isset($_GET['detail'])){
	include ("opp_detail.php");
}else if(isset($_GET['contacts'])){
	include("opp_contacts.php");
}
?>