<?php require_once('Connections/dspp.php');
mysql_select_db($database_dspp, $dspp);

 ?>

<!DOCTYPE html>
<html lang="es">
  <head>
<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
  <script>tinymce.init({ selector:'textarea' });</script>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>D-SPP.ORG</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap-theme.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <!--<script src="../js/fileinput.min.js" type="text/javascript"></script>
    <script src="../js/fileinput_locale_es.js"></script>-->


     <!---LIBRERIAS DE Bootstrap File Input-->

    <script type="text/javascript" src="js/bootstrap-filestyle.js"></script>
    <link rel="stylesheet" href="chosen/chosen.css">


    <!------------------- bootstrap-switch -------------->

      <link href="bootstrap-switch-master/bootstrap-switch.css" rel="stylesheet">
      <script src="bootstrap-switch-master/bootstrap-switch.js"></script>

    <!------------------- bootstrap-switch -------------->    

  <style>
  .chosen-container-multi .chosen-choices li.search-field input[type="text"]{padding: 15px;}
  </style>
 
  </head>

  <body>

<!--<input class="btn btn-success" type="button" name="delete_anexo" value="Eliminar" onclick="<?php echo 'eli('. $row_nc_detail['analisis_causa_url'].','.$row_nc_detail['idau_formato'].','.$_GET['idauditoria'].')';?> ">
-->


<form action="#" method="POST" id="idformulario" name="idformulario">
  <input type="button" value="borrar" onclick="borrar('<?php echo "$row_nc_detail['idau_formato']"; ?>', '<?php echo "$_GET['idauditoria']"; ?>')">  
</form>

<script>
  function borrar(idau, idformato){
    document.getElementById('idformulario').submit();
    location.href='https://temp.ecertimex.com/admin/calidad/?auditorias&update&idauditoria='+idau+'&nc_detail&idau_formato='+idformato+'&ncd';

    console.log('https://temp.ecertimex.com/admin/calidad/?auditorias&update&idauditoria='+idau+'&nc_detail&idau_formato='+idformato+'&ncd');

  }
</script>
 


  </body>
</html>




  <script src="chosen/chosen.jquery.js" type="text/javascript"></script>
  <script type="text/javascript">
    var config = {
      '.chosen-select'           : {},
      '.chosen-select-deselect'  : {allow_single_deselect:true},
      '.chosen-select-no-single' : {disable_search_threshold:10},
      '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
      '.chosen-select-width'     : {width:"95%"}
    }
    for (var selector in config) {
      $(selector).chosen(config[selector]);
    }
  </script>