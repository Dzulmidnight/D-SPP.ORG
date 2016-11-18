<div class="row" style="margin-left:-40px;">
  <div class="col-md-12">
    <!--<div class="col-md-12">
      <div class="list-group">
        <span  class="list-group-item disabled">
          Menú
        </span>
        <a href="?CORREO&send" class="list-group-item <?php if(isset($_GET['send'])){echo "active";} ?>"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> Enviar Correo</a>
        <a href="?CORREO&list" class="list-group-item <?php if(isset($_GET['list'])){echo "active";} ?>" ><span class="glyphicon glyphicon-folder-open" aria-hidden="true"></span> Consultar Correos</a>

      </div>
      <div class="alert alert-info">
        El envío de correos masivos puede tardar un momento.
      </div>

    </div>-->
    <div class="col-md-4">
      <a href="?CORREO&send" class="list-group-item <?php if(isset($_GET['send'])){echo "active";} ?>"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> Enviar Correo</a>
    </div>
    <div class="col-md-4">
      <a href="?CORREO&list" class="list-group-item <?php if(isset($_GET['list'])){echo "active";} ?>" ><span class="glyphicon glyphicon-folder-open" aria-hidden="true"></span> Consultar Contactos</a>
    </div>
    <div class="col-md-4">
      <a href="?CORREO&add" class="list-group-item <?php if(isset($_GET['add'])){echo "active";} ?>" ><span class="glyphicon glyphicon-folder-open" aria-hidden="true"></span> Nueva Lista de Contactos</a>
    </div>


    <div class="col-md-12">
      <!--<div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Panel Correo</h3>
        </div>
        <div class="panel-body">-->
          <? /*if(isset($_GET['mensaje'])){?>
            <p>
              <div class="alert alert-success" role="alert"><? echo $_GET['mensaje']?></div>
            </p>
          <? }*/?>
          <?
          /*if(isset($_GET['select'])){include ("opp_select.php");}
          else*/
          if(isset($_GET['send'])){include ("correo_send.php");}
          else
          if(isset($_GET['detail'])){include ("opp_detail.php");}
          else
          if(isset($_GET['list'])){include ("correo_list.php");}
          else
          if(isset($_GET['add'])){include ("correo_add.php");}

          ?>

       <!--</div>
      </div>-->
    </div>


  </div>
</div>



