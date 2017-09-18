<!DOCTYPE html>
<html>
<head>
    <title>PHP/MySQL Project (<?php echo $_ENV['DOCKER_CONTAINER_NAME'] ?>)</title>
</head>
<body>
    <h1>PHP/MySQL Project (<?php echo $_ENV['DOCKER_CONTAINER_NAME'] ?>)</h1>

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

    <h2>MySQL part</h2>
    <?php
        try {
            $connection = new \PDO('mysql:dbname=docker;host=mysql', 'docker', 'docker');
            $connectionStatus = $connection->getAttribute(\PDO::ATTR_CONNECTION_STATUS);
        } catch (\PDOException $pdoException) {
            $connectionStatus = $pdoException->getMessage();
        }
    ?>
    <ul>
        <li>Connection status: <strong><?php echo $connectionStatus ?></strong></li>
        <?php if (false === isset($pdoException)) { ?>
            <li>Server version: <?php echo $connection->getAttribute(\PDO::ATTR_SERVER_VERSION) ?></li>
            <li>Server informations: <?php echo $connection->getAttribute(\PDO::ATTR_SERVER_INFO) ?></li>
        <?php } ?>
    </ul>
</body>
</html>
