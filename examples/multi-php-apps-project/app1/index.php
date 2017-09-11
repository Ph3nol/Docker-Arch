<!DOCTYPE html>
<html>
<head>
    <title>Multi PHP Apps Project (<?php echo $_ENV['DOCKER_CONTAINER_NAME'] ?>)</title>
</head>
<body>
    <h1>Multi PHP Apps Project (<?php echo $_ENV['DOCKER_CONTAINER_NAME'] ?>)</h1>

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
        <li><a href="phpinfo.php" title="PHP configuration">PHP configuration</a></li>
    </ul>
</body>
</html>
