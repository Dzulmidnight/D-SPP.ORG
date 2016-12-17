<div class="btn-group" role="group" aria-label="...">
	<a <?php if(isset($_GET['detail'])){ echo "class='btn btn-sm btn-primary'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> href="?INFORME&detail"><span class="glyphicon glyphicon-book" aria-hidden="true"></span> Informe General</a>
	<a <?php if(isset($_GET['add'])){ echo "class='btn btn-sm btn-primary'"; }else{ echo "class='btn btn-sm btn-default'"; } ?> href="?INFORME&add"><span class="glyphicon glyphicon-file" aria-hidde="true"></span> Nuevo Formato</a>
</div>

<?php 

if(isset($_GET['detail'])){
	include ("informe_detail.php");
}
else if(isset($_GET['add'])){
	include ("informe_add.php");
}
?>