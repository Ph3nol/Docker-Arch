<!DOCTYPE html>
<html>
<head>
    <title>PHP/Atmo Project (<?php echo $_ENV['DOCKER_CONTAINER_NAME'] ?>)</title>
</head>
<body>
    <h1>PHP/Atmo Project (<?php echo $_ENV['DOCKER_CONTAINER_NAME'] ?>)</h1>

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

    <h2>Places list (from Atmo example endpoint)</h2>

    <p>Here are some places:</p>
    <ul id="fast-foods-list"></ul>
    <p><em id="xhr-request"></em></p>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script>
        $(function () {
            var $xhrRequestContainer = $('#xhr-request');
            var placesRequestUrl = 'http://atmo.api:<?php echo $_ENV['DOCKER_CONFIG_LOCALHOST_NGINX_PORT'] ?>/1/places.json';
            $xhrRequestContainer
                .append('Endpoint: ')
                .append($('<a />').attr('href', placesRequestUrl).html(placesRequestUrl));
            var placesRequest = $.ajax({
                url: placesRequestUrl
            });

            var $placesList = $('#fast-foods-list');
            placesRequest
                .done(function (placesResponse) {
                    $.each(placesResponse._embedded.places, function (index, place) {
                        var $placeItem = $('<li />').html(place.name);
                        $placesList.append($placeItem);
                    });
                })
                .fail(function () {
                    console.debug('Impossible to call Atmo example endpoint.');
                });
        });
    </script>
</body>
</html>
