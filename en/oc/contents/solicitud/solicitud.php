<script>
	var variableGlobal = "";
</script>
<ul class="nav nav-pills">
<li role="presentation"
<? if(isset($_GET['select'])){?> class="active" <? }?>>
	<a href="?SOLICITUD&select" aria-label="Left Align">
		<span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span>  OPP Applications
	</a>
</li>
<li role="presentation"
<? if(isset($_GET['selectCOM'])){?> class="active" <? }?>>
	<a href="?SOLICITUD&selectCOM" aria-label="Left Align">
		<span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Companies Applications
	</a>
</li>
<li role="presentation"
<? if(isset($_GET['add'])){?> class="active" <? }?>>
<div class="btn-group" role="group" aria-label="...">
  <div class="btn-group" role="group">
    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      Nueva Solicitud
      <span class="caret"></span>
    </button>
    <ul class="dropdown-menu">
      <li><a href="?SOLICITUD&add"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Solicitud OPP</a></li>
      <li><a href="?SOLICITUD&addCOM"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Solicitud Empresa</a></li>
    </ul>
  </div>
</div>
</li>

<? if(isset($_GET['detail'])){?>
<li role="presentation" 
 class="active" ><a href="#">Detalle</a></li>
<? }?>

</ul>

<? if(isset($_GET['mensaje'])){?>
<p>
<div class="alert alert-success" role="alert"><? echo $_GET['mensaje']?></div>
</p>
<? }?>


<?
if(isset($_GET['select'])){include ("solicitud_select.php");}
else
if(isset($_GET['add'])){include ("solicitud_add.php");}
else
if(isset($_GET['addCOM'])){include ("com/solicitud_add.php");}
else
if(isset($_GET['detail'])){include ("solicitud_detail.php");}
else 
if(isset($_GET['detailBlock'])){include ("solicitud_detailBlock.php");}
else
if(isset($_GET['detailCOM'])){include ("com/solicitud_detail.php");}
else
if(isset($_GET['selectCOM'])){include ("com/solicitud_select.php");}
?>