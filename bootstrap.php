<?php

use Core\App;
use Core\Container;
use Core\DatabaseDAOImpl;

$container = new Container();

$container->bind('Core\DatabaseDAOImpl', function () {
    $config = require base_path('config.php');

    return new DatabaseDAOImpl($config['database']);
});

App::setContainer($container);
