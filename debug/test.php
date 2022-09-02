<?php

use src\core\Exception\EmailException;
use src\support\Email;

require __DIR__ . '/../vendor/autoload.php';
$mail = 'micherlanerod@gmail.com';
$name = 'José Nilton';
//$subject = readline('Assunto: ');
$subject =  'Logo do blog BraPedia';
//$body = readline('Corpo da mensagem: ') . time();
$body = str_replace('{{hour}}', date(' H:i'), file_get_contents(__DIR__ . '/../view/email/email-test.txt') . '');

try {
    $emai = new Email();
    $emai->add($mail, $name, $subject, $body)
       // ->attach(__DIR__ . '/../public/assets/images/bridge.jpg', 'bridge.jpg')
       ->attach(__DIR__ . '/../public/assets/images/logo.svg', 'brapedia.svg');
//       ->email()->addAddress('niltonbatera297@gmail.com', 'Nilton');
//   echo "<p>", ($emai->send() ? 'Email enviado com sucesso' : $emai->error()->getMessage()), "</p>";
//    echo ($emai->queue() ? 'Email agendado com sucesso' : $emai->error()->getMessage());
} catch (EmailException $exception) {
    echo "<p>{$exception->getMessage()}</p>";
} catch (\PHPMailer\PHPMailer\Exception $exception) {
    echo "<p>{$exception->ErrorInfo}</p>";
}
