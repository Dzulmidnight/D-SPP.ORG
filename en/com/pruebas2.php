<?php

/***********************************************************************************************************************/
/***********************************************************************************************************************/
/***********************************************************************************************************************/


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

  /*if(is_array($_POST['op_resp4'])){
    foreach($_POST['op_resp4'] as $resp4){
        $array_resp4 .= $resp4." - ";
    }
  }else{
      $array_resp4 = "no hay";
  }*/
  $rutaArchivo = "croquis/";


    $_FILES["op_resp15"]["name"];
      move_uploaded_file($_FILES["op_resp15"]["tmp_name"], $rutaArchivo.date("Ymd H:i:s")."_".$_FILES["op_resp15"]["name"]);
      $croquis = $rutaArchivo.basename(date("Ymd H:i:s")."_".$_FILES["op_resp15"]["name"]);


  $array_resp4 = "";
  
  if(is_array($_POST['op_resp4'])){
    $resp4 = $_POST['op_resp4'];

    for ($i=0; $i < count($resp4) ; $i++) { 
      $array_resp4 .= $resp4[$i]." - ";
    }
  }else{
      $array_resp4 = NULL;
  }


  if(isset($_POST['op_resp13']) && $_POST['op_resp13'] == "mayor"){
    $op_resp13 = $_POST['op_resp13_1'];
  }else{
    $op_resp13 = $_POST['op_resp13'];
  }
$updateSQL = "UPDATE solicitud_certificacion SET 
  idopp= $_POST[idopp], 
  ciudad= '$_POST[ciudad]', 
  ruc= '$_POST[ruc]', 
  p1_nombre= '$_POST[p1_nombre]', 
  p1_cargo= '$_POST[p1_cargo]', 
  p1_telefono= '$_POST[p1_telefono]', 
  p1_email= '$_POST[p1_email]', 
  p2_nombre= '$_POST[p2_nombre]', 
  p2_cargo= '$_POST[p2_cargo]', 
  p2_telefono= '$_POST[p2_telefono]', 
  p2_email= '$_POST[p2_email]', 
  adm_nom1= '$_POST[adm_nom1]', 
  adm_nom2= '$_POST[adm_nom2]', 
  adm_tel1= '$_POST[adm_tel1]', 
  adm_tel2= '$_POST[adm_tel2]', 
  adm_email1= '$_POST[adm_email1]', 
  adm_email2= '$_POST[adm_email2]', 
  resp1= '$_POST[resp1]', 
  resp2= '$_POST[resp2]', 
  resp3= '$_POST[resp3]', 
  resp4= '$_POST[resp4]', 
  op_resp1= '$_POST[op_resp1]',  
  op_resp2= '$_POST[op_resp2]', 
  op_resp3= '$_POST[op_resp3]', 
  op_resp4= '$array_resp4', 
  op_resp5= '$_POST[op_resp5]', 
  op_resp6= '$_POST[op_resp6]', 
  op_resp7= '$_POST[op_resp7]', 
  op_resp8= '$_POST[op_resp8]', 
  op_resp10= '$_POST[op_resp10]', 
  op_resp11= '$_POST[op_resp11]', 
  op_resp12= '$_POST[op_resp12]', 
  op_resp13= '$_POST[op_resp13]', 
  op_resp14= '$_POST[op_resp14]', 
  op_resp15= '',
  fecha_elaboracion= $_POST[fecha_elaboracion], 
  status= '$_POST[status]' 
  WHERE idsolicitud_certificacion= $_POST[idsolicitud_certificacion]";



/*$updateSQL = sprintf("UPDATE solicitud_certificacion SET idopp=%s, ciudad=%s, ruc=%s, p1_nombre=%s, p1_cargo=%s, p1_telefono=%s, p1_email=%s, p2_nombre=%s, p2_cargo=%s, p2_telefono=%s, p2_email=%s, adm_nom1=%s, adm_nom2=%s, adm_tel1=%s, adm_tel2=%s, adm_email1=%s, adm_email2=%s, resp1=%s, resp2=%s, resp3=%s, resp4=%s, op_resp1=%s, op_area1=%s, op_area2=%s, op_area3=%s, op_area4=%s, op_resp2=%s, op_resp3=%s, op_resp4=%s, op_resp5=%s, op_resp6=%s, op_resp7=%s, op_resp8=%s, op_resp10=%s, op_resp11=%s, op_resp12=%s, op_resp13=%s, op_resp14=%s, op_resp15=%s, fecha_elaboracion=%s, status=%s WHERE idsolicitud_certificacion=%s",

                       GetSQLValueString($_POST['idopp'], "int"),
                       GetSQLValueString($_POST['ciudad'], "text"),
                       GetSQLValueString($_POST['ruc'], "text"),
                       GetSQLValueString($_POST['p1_nombre'], "text"),
                       GetSQLValueString($_POST['p1_cargo'], "text"),
                       GetSQLValueString($_POST['p1_telefono'], "text"),
                       GetSQLValueString($_POST['p1_email'], "text"),
                       GetSQLValueString($_POST['p2_nombre'], "text"),
                       GetSQLValueString($_POST['p2_cargo'], "text"),
                       GetSQLValueString($_POST['p2_telefono'], "text"),
                       GetSQLValueString($_POST['p2_email'], "text"),
                       GetSQLValueString($_POST['adm_nom1'], "text"),
                       GetSQLValueString($_POST['adm_nom2'], "text"),
                       GetSQLValueString($_POST['adm_tel1'], "text"),
                       GetSQLValueString($_POST['adm_tel2'], "text"),
                       GetSQLValueString($_POST['adm_email1'], "text"),
                       GetSQLValueString($_POST['adm_email2'], "text"),
                       GetSQLValueString($_POST['resp1'], "text"),
                       GetSQLValueString($_POST['resp2'], "text"),
                       GetSQLValueString($_POST['resp3'], "text"),
                       GetSQLValueString($_POST['resp4'], "text"),
                       GetSQLValueString($_POST['op_resp1'], "text"),
                       GetSQLValueString($_POST['op_area1'], "text"),
                       GetSQLValueString($_POST['op_area2'], "text"),
                       GetSQLValueString($_POST['op_area3'], "text"),
                       GetSQLValueString($_POST['op_area4'], "text"),
                       GetSQLValueString($_POST['op_resp2'], "text"),
                       GetSQLValueString($_POST['op_resp3'], "text"),
                       GetSQLValueString($array_resp4, "text"),
                       GetSQLValueString($_POST['op_resp5'], "text"),
                       GetSQLValueString($_POST['op_resp6'], "text"),
                       GetSQLValueString($_POST['op_resp7'], "text"),
                       GetSQLValueString($_POST['op_resp8'], "text"),
                       GetSQLValueString($_POST['op_resp10'], "text"),
                       GetSQLValueString($_POST['op_resp11'], "text"),
                       GetSQLValueString($_POST['op_resp12'], "text"),
                       GetSQLValueString($op_resp13, "text"),
                       GetSQLValueString($_POST['op_resp14'], "text"),
                       GetSQLValueString($croquis, "text"),
                       GetSQLValueString($_POST['fecha_elaboracion'], "int"),
                       GetSQLValueString($_POST['status'], "text"),
                       GetSQLValueString($_POST['idsolicitud_certificacion'], "int"));
  */
  #mysql_select_db($database_dspp, $dspp);
#  $Result1 = mysql_query($updateSQL, $dspp) or die(mysql_error());



    $certificacion = $_POST['certificacion'];
    $certificadora = $_POST['certificadora'];
    $ano_inicial = $_POST['ano_inicial'];
    $interrumpida = $_POST['interrumpida'];
    $idcertificacion = $_POST['idcertificacion'];

    for($i=0;$i<count($certificacion);$i++){
      if($certificacion[$i] != NULL){
        #for($i=0;$i<count($certificacion);$i++){

        $updateSQL = "UPDATE certificaciones SET certificacion= '".$certificacion[$i]."', certificadora='".$certificadora[$i]."', ano_inicial= '".$ano_inicial[$i]."', interrumpida= '".$interrumpida[$i]."' WHERE idcertificacion= '".$idcertificacion[$i]."'";

  #      $Result1 = mysql_query($updateSQL, $dspp) or die(mysql_error());
        }
    }


      $producto = $_POST['producto'];
      $volumen = $_POST['volumen'];
      $materia = $_POST['materia'];
      $destino = $_POST['destino'];
      $idproducto = $_POST['idproducto'];
      /*$marca_propia = $_POST['marca_propia'];
      $marca_cliente = $_POST['marca_cliente'];
      $sin_cliente = $_POST['sin_cliente'];*/

    for ($i=0;$i<count($producto);$i++) { 
      if($producto[$i] != NULL){

      $array1 = "terminado".$i; 
      $array2 = "marca_propia".$i;
      $array3 = "marca_cliente".$i;
      $array4 = "sin_cliente".$i;

      $terminado = $_POST[$array1];
      $marca_propia = $_POST[$array2];
      $marca_cliente = $_POST[$array3];
      $sin_cliente = $_POST[$array4];


          /*$updateSQL = "UPDATE productos SET 
          producto= '".$producto[$i]."',
          volumen= '".$volumen[$i]."',
          terminado= '".$terminado[$i]."',
          materia='".$materia[$i]."',
          destino='".$destino[$i]."',
          marca_propia='". $marca_propia[$i]."',
          marca_cliente='".$marca_cliente[$i]."', 
          sin_cliente= '".$sin_cliente[$i]."' 
          WHERE idproducto= '".$idproducto[$i]."'";
          #$Result = mysql_query($updateSQL, $dspp) or die(mysql_error());*/

          echo "<br>la consulta es: ".$producto[$i]." - terminado: ".$terminado." - marca: ".$marca_propia." - cliente: ".$marca_cliente." - sin ".$sin_cliente."<br>";
      }
    }


}




 ?>