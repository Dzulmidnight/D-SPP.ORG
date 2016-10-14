<ul class="nav nav-pills">
  <li role="presentation" <?php if(isset($_GET['select'])){ echo "class='active'";} ?>>
    <a href="?SOLICITUD&select">
      <span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> SPO applications
    </a>
  </li>
  <li role="presentation" <?php if(isset($_GET['select_empresa'])){ echo "class='active'";} ?>>
    <a href="?SOLICITUD&select_empresa" aria-label="Left Align">
      <span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Companies applications
    </a>
  </li>
  <li role="presentation" <?php if(isset($_GET['add'])){ echo "class='active'";} ?>>
    <div class="btn-group" role="group" aria-label="...">
      <div class="btn-group" role="group">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          New Application
          <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
          <li><a href="?SOLICITUD&add"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> SPO Application</a></li>
          <li><a href="?SOLICITUD&add_empresa"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Company Application</a></li>
        </ul>
      </div>
    </div>
  </li>
  <?php 
  if(isset($_GET['detail'])){
  ?>
    <li role="presentation" class="active">
      <a href="#">Detail</a>
    </li>
  <?php
  }
   ?>
</ul>


<?php 
if(isset($mensaje)){
?>
<div class="col-md-12 alert alert-success alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <?php echo $mensaje; ?>
</div>
<?php
}
?>


<?
if(isset($_GET['select'])){
  include ("solicitud_select.php");
}
else if(isset($_GET['add'])){
  include ("solicitud_add.php");
}
else if(isset($_GET['add_empresa'])){
  include ("empresas/solicitud_add.php");
}
else if(isset($_GET['IDsolicitud'])){
  include ("solicitud_detail.php");
}
else  if(isset($_GET['asdf'])){
  include ("solicitud_detailBlock.php");
}
else if(isset($_GET['IDsolicitud_empresa'])){
  include ("empresas/solicitud_detail.php");
}
else if(isset($_GET['select_empresa'])){
  include ("empresas/solicitud_select.php");
}
?>