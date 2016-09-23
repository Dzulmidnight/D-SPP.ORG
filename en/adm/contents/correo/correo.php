<div class="row">
  <div class="col-md-12">
    <div class="col-xs-3">
      <div class="list-group">
        <span  class="list-group-item disabled">
          Menú
        </span>
        <a href="?CORREO&add" class="list-group-item <?php if(isset($_GET['add'])){echo "active";} ?>"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> Enviar Correo</a>
        <a href="#" class="list-group-item">Lista de Correos</a>
        <!--<a href="#" class="list-group-item">Porta ac consectetur ac</a>
        <a href="#" class="list-group-item">Vestibulum at eros</a>-->
      </div>
    </div>

    <div class="col-xs-9">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Panel Correo</h3>
        </div>
        <div class="panel-body">
          <? /*if(isset($_GET['mensaje'])){?>
            <p>
              <div class="alert alert-success" role="alert"><? echo $_GET['mensaje']?></div>
            </p>
          <? }*/?>
          <?
          /*if(isset($_GET['select'])){include ("opp_select.php");}
          else*/
          if(isset($_GET['add'])){include ("correo_add.php");}
          else
          if(isset($_GET['detail'])){include ("opp_detail.php");}
          ?>

        </div>
      </div>      
    </div>


  </div>
</div>



