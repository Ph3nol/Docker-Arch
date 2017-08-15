<?php define('REDIS_DOCKER_CONTAINER_NAME', 'redis-598f085d92f1d'); ?>

<!DOCTYPE html>
<html>
<head>
    <title>Simple PHP/Redis Project (<?php echo $_ENV['DOCKER_CONTAINER_NAME'] ?>)</title>
</head>
<body>
    <h1>Simple PHP/Redis Project (<?php echo $_ENV['DOCKER_CONTAINER_NAME'] ?>)</h1>

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

    <h2>Redis part</h2>
    <?php
        $redis = new \Redis();
        $redis->connect(REDIS_DOCKER_CONTAINER_NAME, 6379);
    ?>
    <ul>
        <?php
            try {
                $infos = $redis->info();
                echo '<li>Version: <strong>' . $infos['redis_version'] . '</strong></li>'."\n";
                echo '<li>Ping result: ' . $redis->ping() . '</li>'."\n";
            } catch (\RedisException $e) {
                echo '<li>Error: <strong>' . $e->getMessage() . '</strong>';
            }
        ?>
    </ul>
</body>
</html>
