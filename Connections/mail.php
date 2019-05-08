<?php 
include_once("../PHPMailer/class.phpmailer.php");
include_once("../PHPMailer/class.smtp.php");

$mail = new PHPMailer();
$mail->IsSMTP();
//$mail->SMTPSecure = "ssl";
$mail->Host = "smtp.mailtrap.io";
//$mail->Port = 25;
$mail->Port = 2525;
$mail->SMTPAuth = true;
$mail->Username = "a00f86d865d26f";
$mail->Password = "caee3727a36bbb";
//$mail->SMTPDebug = 1;

$mail->From = "soporte@d-spp.org";
$mail->FromName = utf8_decode("CERTIFICACIÓN-CERTIFICATION SPP");
$mail->AddBCC("yasser.midnight@gmail.com", "correo Oculto");

$correoCert = "cert@spp.coop";


/*
$mail = new PHPMailer(true);
$mail->IsSMTP();
$mail->SMTPAuth = true;
$mail->SMTPSecure = "ssl";

$mail->Port = 465;
$mail->Host = "mail.d-spp.org";
$mail->Username = "soporte@d-spp.org";
$mail->Password = "LE=o6U;tCLO?";

$mail->From = "soporte@d-spp.org";
$mail->FromName = utf8_decode("CERTIFICACIÓN-CERTIFICATION SPP");

$mail->AddBCC("yasser.midnight@gmail.com", "correo Oculto");

//$correoCert = "cert@spp.coop";
*/
 ?>
