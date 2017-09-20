<?php

require_once __DIR__.'/vendor/autoload.php';

printf("Complex PHP Project (%s)\n", $_ENV['DOCKER_CONTAINER_NAME']);
echo "`php -i` for more informations.\n\n";

echo "Going to send mail through Mailcatcher...\n";
$transport = (new Swift_SmtpTransport('mailcatcher', 25));
$mailer = new Swift_Mailer($transport);
$message = (new Swift_Message('This is an example from Docker Arch '.$_ENV['DOCKER_CONTAINER_NAME'].' instance'))
    ->setFrom(['hello@unicorn.inc' => 'Unicorn Inc.'])
    ->setTo(['mailcatcher@docker-arch' => 'Docker Arch'])
    ->setBody('
        Lorem ipsum dolor sit amet, consectetur adipisicing elit,
        sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
        Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
        Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
        Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
    ');
$mailer->send($message);
echo "... done!\n";
