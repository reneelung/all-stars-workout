<?php
    $app->register(new Silex\Provider\DoctrineServiceProvider(), array(
        'db.options' => array(
            'host'      => DB_HOST,
            'dbname'    => DB_NAME,
            'user'      => DB_USER,
            'password'  => DB_PASS
        ),
    ));
    $db = $app['db'];