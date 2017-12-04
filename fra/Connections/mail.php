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
$mail->Username = "99f9a8c6200b52";
$mail->Password = "943bba851a952d";
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
$mail->Password = "*2w{.?bwxxop";

$mail->From = "soporte@d-spp.org";
$mail->FromName = utf8_decode("CERTIFICACIÓN-CERTIFICATION SPP");

$mail->AddBCC("yasser.midnight@gmail.com", "correo Oculto");
*/

 ?>
