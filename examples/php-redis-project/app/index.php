<!DOCTYPE html>
<html>
<head>
    <title>PHP/Redis Project (<?php echo $_ENV['DOCKER_CONTAINER_NAME'] ?>)</title>
</head>
<body>
    <h1>PHP/Redis Project (<?php echo $_ENV['DOCKER_CONTAINER_NAME'] ?>)</h1>

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

    <h2>Redis part</h2>
    <?php
        require_once __DIR__.'/vendor/autoload.php';

        try {
            $client = new \Predis\Client('tcp://redis:6379');
            $serverInfos = $client->info()['Server'];
        } catch (\Predis\Connection\ConnectionException $e) {
            $serverInfos = null;
        }
    ?>
    <ul>
        <li>Version: <strong><?php echo $serverInfos ? $serverInfos['redis_version'] : 'N/A' ?></strong></li>
    </ul>
</body>
</html>
