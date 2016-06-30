<?php
use Symfony\Component\HttpFoundation\Request;

$async = $app['controllers_factory'];
$async->get('/', function() use($app) {
    $workout_model = new Workout();
    $user = $app['session']->get('user');
    $workouts = $workout_model->get_workouts_by_user($user['id'], 'ASC');
    return $app['twig']->render('/workouts/main.twig', array('user' => $user, 'workouts' => $workouts));
});

$async->get('/team/workouts', function() use ($app) {
    $workout_model = new Workout();
    $team_workouts = $workout_model->get_workout_data(null, 'ASC');

    if ($team_workouts) {
        return $app->json($team_workouts);
    }
});

$async->get('/team/workouts/type/{type}', function($type) use($app) {
    $workout_model = new Workout();
    $team_workouts_by_type = $workout_model->get_workouts_by_type(null, $type, 'ASC');

    if ($team_workouts_by_type) {
        return $app->json($team_workouts_by_type);
    }
});

$async->get('/user/workouts', function() use ($app) {
    $workout_model = new Workout();
    $user = $app['session']->get('user');
    $user_workouts = $workout_model->get_workout_data($user['id'], 'ASC');

    if ($user_workouts) {
        return $app->json($user_workouts);
    }
});

$async->get('/user/workouts/type/{type}', function($type) use($app) {
    $workout_model = new Workout();
    $user = $app['session']->get('user');
    $workouts_by_type = $workout_model->get_workouts_by_type($user['id'], $type, 'ASC');

    if ($workouts_by_type) {
        return $app->json($workouts_by_type);
    }
});

$async->get('/workouts/like/', function(Request $request) use($app) {
    $workout_model = new Workout();
    $id = $request->get('workout_id');
    $user_id = $request->get('user_id');
    return $app->json($workout_model->like_workout($id, $user_id));
});

$async->get('/workouts/unlike/', function(Request $request) use($app) {
    $workout_model = new Workout();
    $id = $request->get('workout_id');
    $user_id = $request->get('user_id');
    return $app->json($workout_model->unlike_workout($id, $user_id));
});

$app->mount('/async', $async);