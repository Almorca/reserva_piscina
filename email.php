<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'lib/Exception.php';
require 'lib/PHPMailer.php';
require 'lib/SMTP.php';


// Instantiation and passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Recipients
    $mail->setFrom('almorca@gmail.com', 'Comunidad Maquinilla 13');
    $mail->addAddress('almorca@gmail.com', 'A 3-1');     // Add a recipient
    //$mail->addReplyTo('info@example.com', 'Information');
    
    // Attachments
    //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

    // Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Reserva piscina';
    $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
	
	//Read an HTML message body from an external file, convert referenced images to embedded,
	//convert HTML into a basic plain-text alternative body
	//$mail->msgHTML(file_get_contents('contents.html'), __DIR__);

    $mail->send();
    echo 'El mensaje se ha enviado';
} catch (Exception $e) {
    echo "El mensaje no ha podido ser enviado. Error: {$mail->ErrorInfo}";
}
