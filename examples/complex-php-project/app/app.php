<?php

require_once __DIR__.'/vendor/autoload.php';

printf("Complex PHP Project (%s)\n", $_ENV['DOCKER_CONTAINER_NAME']);
echo "`php -i` for more informations.\n\n";

echo "Going to send mail through Mailcatcher...\n";
$transport = (new Swift_SmtpTransport('mailcatcher', 1025));
$mailer = new Swift_Mailer($transport);
$message = (new Swift_Message('This is an example from Docker Arch '.$_ENV['DOCKER_CONTAINER_NAME'].' instance'))
    ->setFrom(['john@doe.com' => 'John Doe'])
    ->setTo(['jane@doe.com' => 'Jane Doe'])
    ->setBody('This is an example.');
$mailer->send($message);
echo "... done!\n";
