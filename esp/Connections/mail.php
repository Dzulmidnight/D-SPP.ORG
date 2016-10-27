<?php 
include_once("../../PHPMailer/class.phpmailer.php");
include_once("../../PHPMailer/class.smtp.php");

$mail = new PHPMailer();
$mail->IsSMTP();
//$mail->SMTPSecure = "ssl";
$mail->Host = "mailtrap.io";
//$mail->Port = 25;
$mail->Port = 25;
$mail->SMTPAuth = true;
$mail->Username = "d4071ef0fb933e";
$mail->Password = "8e53290c3bd811";
//$mail->SMTPDebug = 1;

$mail->From = "soporte@d-spp.org";
$mail->FromName = utf8_decode("CERTIFICACIÓN-CERTIFICATION SPP");


$correoCert = "cert@spp.coop";


/*
$mail = new PHPMailer();
$mail->IsSMTP();
//$mail->SMTPSecure = "ssl";
$mail->Host = "mail.d-spp.org";
//$mail->Port = 25;
$mail->Port = 587;
$mail->SMTPAuth = true;
$mail->Username = "soporte@d-spp.org";
$mail->Password = "/aung5l6tZ";
$mail->SMTPDebug = 1;

$mail->From = "soporte@d-spp.org";
$mail->FromName = utf8_decode("CERTIFICACIÓN-CERTIFICATION SPP");
$mail->AddBCC("yasser.midnight@gmail.com", "correo Oculto");

$correoCert = "cert@spp.coop";
*/
 ?>