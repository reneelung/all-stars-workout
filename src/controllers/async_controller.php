<?php
use Symfony\Component\HttpFoundation\Request;

$async = $app['controllers_factory'];
$async->get('/', function() use($app) {
    $workout_model = new Workout();
    $user = $app['session']->get('user');
    $workouts = $workout_model->get_workouts_by_user($user['id']);
    return $app['twig']->render('/workouts/main.twig', array('user' => $user, 'workouts' => $workouts));
});

$async->get('/team/workouts', function() use ($app) {
    $workout_model = new Workout();
    $team_workouts = $workout_model->get_workout_data();

    if ($team_workouts) {
        return $app->json($team_workouts);
    }
});

$async->get('/team/workouts/type/{type}', function($type) use($app) {
    $workout_model = new Workout();
    $team_workouts_by_type = $workout_model->get_workouts_by_type(null, $type);

    if ($team_workouts_by_type) {
        return $app->json($team_workouts_by_type);
    }
});

$async->get('/user/workouts', function() use ($app) {
    $workout_model = new Workout();
    $user = $app['session']->get('user');
    $user_workouts = $workout_model->get_workout_data($user['id']);

    if ($user_workouts) {
        return $app->json($user_workouts);
    }
});

$async->get('/user/workouts/type/{type}', function($type) use($app) {
    $workout_model = new Workout();
    $user = $app['session']->get('user');
    $workouts_by_type = $workout_model->get_workouts_by_type($user['id'], $type);

    if ($workouts_by_type) {
        return $app->json($workouts_by_type);
    }
});

$app->mount('/async', $async);