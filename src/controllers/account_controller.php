<?php
use Symfony\Component\HttpFoundation\Request;

$account = $app['controllers_factory'];
$account->get('/', function() use($app) {
    $user = $app['session']->get('user');
    return $app['twig']->render('/account/account.twig', array('user' => $user));
});

$account->post('/edit', function(Request $request) use($app) {
    $editable_fields = ['first_name', 'last_name', 'email'];
    foreach ($editable_fields as $field) {
        $params[$field] = $request->get($field);
    }

    $user_model = new User();
    $user = $app['session']->get('user');
    if ($user_model->save_user($params, $user['id'])) {
        return $app->redirect( APP_PATH . '/account/' );
    }
});

$account->get('/edit', function() use ($app) {
    $user = $app['session']->get('user');
    return $app['twig']->render('/account/edit.twig', array('user' => $user));
});

$app->mount('/account', $account);