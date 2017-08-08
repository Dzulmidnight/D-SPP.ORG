<ul class="nav nav-pills">
	<li role="presentation" <?php if(isset($_GET['select'])){echo "class='active'"; } ?>>
		<a href="?EMPRESAS&select">Empresa</a>
	</li>
	<li role="presentation" <?php if(isset($_GET['add'])){echo "class='active'"; } ?>>
		<a href="?EMPRESAS&add" aria-label="Left Align">
			<span class="glyphicon glyphicon-open-file" aria-hidden="true"></span> Nueva Empresa
		</a>		
	</li>
	<? if(isset($_GET['detail'])){?>
	<li role="presentation" 
	 class="active" ><a href="#">
		<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Detalle 	
	 </a>
	</li>
	<? }?>

</ul>


<?
if(isset($_GET['select'])){include ("empresa_select.php");}
else
if(isset($_GET['add'])){include ("empresa_add.php");}
else
if(isset($_GET['detail'])){include ("empresa_detail.php");}
?>