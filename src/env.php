<?php
    $environment = $_SERVER['SERVER_NAME'] == 'localhost' ? 'dev' : 'prod';
    $path = $environment == 'dev' ? '/all-stars-workout/web' : '';

    define('ENVIRONMENT', $environment);
    define('APP_PATH', $path);
    define('VIEWS_PATH', __DIR__ . '/../views/');
    define('ASSETS_PATH', __DIR__ . '/../assets/');

    $db_params = parse_url("mysql://bb710f418623bc:2cff9494@us-cdbr-iron-east-04.cleardb.net/heroku_8b370682bd1ee9a?reconnect=true");
    $db_params['dbname'] = substr($db_params["path"], 1);
    $local_db_params = array(
        'host' => 'localhost',
        'dbname' => 'heroku_8b370682bd1ee9a',
        'user' => 'bb710f418623bc',
        'pass' => '2cff9494'
    );

    // $db_config = $environment == 'dev' ? $local_db_params : $db_params;
    $db_config = $db_params;
    define('DB_HOST', $db_config['host']);
    define('DB_NAME', $db_config['dbname']);
    define('DB_USER', $db_config['user']);
    define('DB_PASS', $db_config['pass']);

    // Hashing Strings
    define('SECRET_SALT', 'KTF6Y.6NXP,b?%Iqw?n9xlN$mckzpeP%e@%p#D559Yyzc7UQ!IF^lkbo0bM56#UJ');

    // Timezone
    date_default_timezone_set('America/Toronto');
