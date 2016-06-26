<?php
use Symfony\Component\HttpFoundation\Request;

$async = $app['controllers_factory'];
$async->get('/', function() use($app) {
    $workout_model = new Workout();
    $user = $app['session']->get('user');
    $workouts = $workout_model->get_workouts_by_user($user['id']);
    return $app['twig']->render('/workouts/main.twig', array('user' => $user, 'workouts' => $workouts));
});

$async->get('/workouts', function() use ($app) {
    $workout_model = new Workout();
    $user = $app['session']->get('user');
    $user_workouts = $workout_model->get_workout_data_by_user($user['id']);

    if ($user_workouts) {
        return $app->json($user_workouts);
    }
});

$async->get('/workouts/type/{type}', function($type) use($app) {
    $workout_model = new Workout();
    $user = $app['session']->get('user');
    $workouts_by_type = $workout_model->get_workouts_by_type($user['id'], $type);

    if ($workouts_by_type) {
        return $app->json($workouts_by_type);
    }
});

$app->mount('/async', $async);