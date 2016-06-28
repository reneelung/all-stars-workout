<?php
use Symfony\Component\HttpFoundation\Request;

$admin = $app['controllers_factory'];
$admin->get('/', function() {
    return 'Admin Home';
});

$admin->get('/users', function() use ($app) {
    $user_model = new User($app['db']);
    $users = $user_model->get_all();
    return print_r($users);
});

$admin->get('/scripts/add_users', function(Request $request) use($app) {
    $csv = array_map('str_getcsv', file('http://localhost:8888/all-stars-workout/web/assets/all-stars-contact-list.csv'));
    $user_model = new User();
    $fields = array('first_name', 'last_name', 'email', 'derby_name');
    $headers = array_pop(array_reverse($csv));
    unset($csv[0]);

    $passwords = array();
    foreach ($csv as $user) {
        $name = explode(' ', $user[1]);
        $password = $user_model->generate_token(8);
        $token = $user_model->generate_token();
        $user_data = array(
            'first_name' => $name[0],
            'last_name' => $name[1],
            'derby_name' => $user[0],
            'email' => $user[3],
            'password' => sha1($password.SECRET_SALT.$token),
            'token' => $token,
            'user_name' => $user[3]
        );
        $app['db']->insert('users', $user_data);
        $passwords[$user[3]] = $password;
        $message = \Swift_Message::newInstance()
            ->setSubject('[tord-all-stars.herokuapp.com] Temporary Password')
            ->setFrom(array('reneelung@gmail.com'))
            ->setTo(array($user[3]))
            ->setBody("Your password is: $password");

        $app['mailer']->send($message);
    }
    return print_r($passwords);
});

$app->mount('/admin', $admin);