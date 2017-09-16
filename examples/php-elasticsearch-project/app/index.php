<!DOCTYPE html>
<html>
<head>
    <title>Simple PHP/ElasticSearch Project (<?php echo $_ENV['DOCKER_CONTAINER_NAME'] ?>)</title>
</head>
<body>
    <h1>Simple PHP/ElasticSearch Project (<?php echo $_ENV['DOCKER_CONTAINER_NAME'] ?>)</h1>

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

    <h2>ElasticSearch part</h2>
    <?php
        require_once __DIR__.'/vendor/autoload.php';

        try {
            $elasticaClient = new \Elastica\Client([
                'host' => 'elasticsearch',
                'port' => '9200',
            ]);
            $esVersion = $elasticaClient->getVersion();
        } catch (\Exception $e) {
            $esVersion = null;
        }
    ?>
    <ul>
        <li>Version: <strong><?php echo $esVersion ? : 'N/A' ?></strong></li>
    </ul>
</body>
</html>
