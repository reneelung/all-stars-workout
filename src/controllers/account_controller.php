<?php
use Symfony\Component\HttpFoundation\Request;

$account = $app['controllers_factory'];
$account->get('/', function() use($app) {
    $user = $app['session']->get('user');
    return $app['twig']->render('/account/account.twig', array('user' => $user));
});

$account->post('/edit', function(Request $request) use($app) {
    $editable_fields = ['first_name', 'last_name', 'email', 'derby_name', 'theme'];
    foreach ($editable_fields as $field) {
        $params[$field] = $request->get($field);
    }

    if($request->get('sharing') == 1) {
        $params['private'] = 0;
    }

    $user_model = new User();
    $user = $app['session']->get('user');

    $new_password = $request->get('password_change');
    $new_password_confirm = $request->get('password_confirm');
    if ($new_password == $new_password_confirm && strlen($new_password)) {
        $obfuscated = $user_model->obfuscate_password($request->get('password_change'), $user['id']);
        if ($obfuscated) {
            $params['password'] = $obfuscated;
        }
    }

    if ($user_model->save_user($params, $user['id'])) {
        $app['session']->getFlashBag()->add('account_notify', 'Changes saved. Way to go!');
        $user_model->refresh_session_user_data();
        return $app->redirect( APP_PATH . '/account/edit' );
    }
});

$account->get('/edit', function() use ($app) {
    $user = $app['session']->get('user');
    $sharing_on = intval($user['private']) > 0 ? false : true;
    return $app['twig']->render('/account/edit.twig', array('user' => $user, 'sharing_on' => $sharing_on));
});

$app->mount('/account', $account);