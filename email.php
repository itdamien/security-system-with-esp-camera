<?php

use PHPMailer\PHPMailer\PHPMailer;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

$mail = new PHPMailer();

$mail->isSMTP();


$mail->Host = 'smtp-relay.brevo.com';

$mail->SMTPAuth = true;

$mail->Username = 'ad4713001@smtp-brevo.com';

$mail->Password = 'xsmtpsib-8a7376957121058da74995884dac4761bd9f3929ffaf9ca8f0aea6e8ebea97b7-cQp6HMUusG8OnK3Y';

$mail->Port = 587;

$mail->setFrom('igizenezaprince420@gmail.com');

$mail->addAddress('producerlerim@gmail.com');

$mail->Subject = 'Security Alert';

$mail->Body = '🚨Intruder detected by ESP32-CAM Security System in your House Go check the dashboard for more Data⚠️';

if($mail->send())
{
    include("db.php");
    mysqli_query($conn,"UPDATE sensor_log SET email_status='📩SENT' ORDER BY id DESC LIMIT 1");
    echo "Email Sent";
}
else
{
    include("db.php");
    mysqli_query($conn,"UPDATE sensor_log SET email_status='📝NOT SENT' ORDER BY id DESC LIMIT 1");
    echo "Email Failed";
}

?>