<?php
use Symfony\Component\HttpFoundation\Request;

$workouts = $app['controllers_factory'];
$workouts->get('/', function() use($app) {
    $workout_model = new Workout();
    $user = $app['session']->get('user');
    $workouts = $workout_model->get_workouts_by_user($user['id']);
    return $app['twig']->render('/workouts/main.twig', array('user' => $user, 'workouts' => $workouts));
});
$workouts->get('/new', function() use($app) {
    $user = $app['session']->get('user');
    $workout_model = new Workout();
    $workouts = $workout_model->get_workouts_by_user($user['id']);
    $types = $workout_model->get_workout_types();

    return $app['twig']->render('/workouts/new.twig', array('workouts' => $workouts, 'types' => $types));
});

$workouts->post('/new', function(Request $request) use($app) {
    $workout_model = new Workout();
    $user = $app['session']->get('user');
    $workouts = $workout_model->get_workouts_by_user($user['id']);
    $types = $workout_model->get_workout_types();

    $params['user_id'] = $user['id'];
    $editable_fields = ['title', 'duration', 'reps', 'comments'];
    foreach ($editable_fields as $field) {
        $params[$field] = $request->get($field);
    }

    $params['type'] = $request->get('type_text') ? $request->get('type_text') : $request->get('type_select');

    $result = $workout_model->save_workout($params);

    if ($result) {
        $app['session']->getFlashBag()->add('save_success', 'Workout added.');
        return $app->redirect( APP_PATH . '/workouts/'. $result );
    } else {
        $app['session']->getFlashBag()->add('save_error', 'Could not save workout.');
        return $app['twig']->render('/workouts/new.twig', array('workouts' => $workouts, 'types' => $types));
    }
});

$workouts->get('/{id}', function($id) use ($app) {
    $workout_model = new Workout();
    $workout = $workout_model->get_workout($id);

    if ($workout) {
        return $app['twig']->render('/workouts/view.twig', array('workout' => $workout));
    } else {
        $app->abort(404, "Workout does not exist.");
    }
});

$workouts->get('/edit', function() {
    return "Edit a workout";
});

$app->mount('/workouts', $workouts);