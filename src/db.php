<?php
    $params = parse_url("mysql://bb710f418623bc:2cff9494@us-cdbr-iron-east-04.cleardb.net/heroku_8b370682bd1ee9a?reconnect=true");
    $app->register(new Silex\Provider\DoctrineServiceProvider(), array(
        'db.options' => array(
            'host'      => $params['host'],
            'dbname'    => substr($params["path"], 1),
            'user'      => $params['user'],
            'password'  => $params['pass']
        ),
    ));
    $db = $app['db'];