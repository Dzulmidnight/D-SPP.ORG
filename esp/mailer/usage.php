<?php
$mailer = new AttachMailer("nicolas@icare-net.com", "nico@k-wi.com", "mail sender class", "hello <b>content</b>");
$mailer->attachFile("a.pdf");
$mailer->attachFile("a.pdf");
$mailer->send() ? "envoye": "probleme envoi";