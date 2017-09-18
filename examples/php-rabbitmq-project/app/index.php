<!DOCTYPE html>
<html>
<head>
    <title>PHP/RabbitMQ Project (<?php echo $_ENV['DOCKER_CONTAINER_NAME'] ?>)</title>
</head>
<body>
    <h1>PHP/RabbitMQ Project (<?php echo $_ENV['DOCKER_CONTAINER_NAME'] ?>)</h1>

    <p>
        Lorem ipsum dolor sit amet, consectetur adipisicing elit.
        Maiores quam facilis ipsum error aliquid commodi quis consequuntur alias,
        laboriosam dolores ea quia, rem possimus quae autem aut, iusto placeat aspernatur.
    </p>

    <ul>
        <li>System informations: <?php print exec('uname -a') ?></li>
        <li>Remote address: <?php echo $_SERVER['REMOTE_ADDR'] ?></li>
        <li>Hostname: <?php echo $_SERVER['HOSTNAME'] ?></li>
        <li>PHP version: <?php echo $_SERVER['PHP_VERSION'] ?></li>
    </ul>

    <h2>RabbitMQ part</h2>
    <?php
        require_once __DIR__.'/vendor/autoload.php';

        try {
            $connection = new \PhpAmqpLib\Connection\AMQPStreamConnection('rabbitmq', '5672', 'docker', 'docker', 'docker');
            $channel = $connection->channel();
        } catch (\PhpAmqpLib\Exception\AMQPRuntimeException $e) {
            $channel = null;
        }
    ?>
    <ul>
        <li>Channel ID: <strong><?php echo $channel ? $channel->getChannelId() : 'N/A' ?></strong></li>
        <li>Protocol: <strong><?php echo $channel ? $channel->getProtocolVersion() : 'N/A' ?></strong></li>
    </ul>
</body>
</html>
