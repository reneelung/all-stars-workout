<?php
use Symfony\Component\HttpFoundation\Request;

$workouts = $app['controllers_factory'];
$workouts->get('/', function(Request $request) use($app) {
    $workout_model = new Workout();
    $user_model = new User();
    $user = $current_user = $app['session']->get('user');
    $visitor = false;
    if ($request->get('member_id')) {
        $requested_user = $user_model->get_user($request->get('member_id'));
        if ($user_model->sharing_is_on($requested_user)) {
            $user = $requested_user;
            $visitor = true;
        }
    }
    $workouts = $workout_model->get_workouts_by_user($user['id']);
    foreach ($workouts as &$workout) {
        $workout['already_liked'] = $workout_model->already_liked($workout['id'], $current_user['id']);
    }
    return $app['twig']->render('/workouts/main.twig', array('user' => $user, 'workouts' => $workouts, 'visitor' => $visitor));
});

$workouts->get('/summary', function() use($app) {
    $workout_model = new Workout();
    $types = $workout_model->get_workout_types();
    return $app['twig']->render('/workouts/progress.twig', array('types' => $types));
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
    $editable_fields = ['title', 'duration', 'comments'];
    foreach ($editable_fields as $field) {
        $params[$field] = $request->get($field);
    }

    $params['type'] = $request->get('type_text') ? $request->get('type_text') : $request->get('type_select');
    $date = date("Y-m-d",strtotime($request->get('date')));
    $time = date("H:i:s", strtotime($request->get('time')));
    $params['date'] = "$date $time";

    $result = $workout_model->save_workout($params);

    if ($result) {
        $app['session']->getFlashBag()->add('save_success', 'Workout added.');
        return $app->redirect( APP_PATH . '/workouts/'. $result['id'] );
    } else {
        $app['session']->getFlashBag()->add('save_error', 'Could not save workout.');
        return $app['twig']->render('/workouts/new.twig', array('workouts' => $workouts, 'types' => $types));
    }
});

$workouts->get('/edit/{id}', function($id) use ($app) {
    $workout_model = new Workout();
    $workout = $workout_model->get_workout($id);
    $types = $workout_model->get_workout_types();

    if ($workout) {
        return $app['twig']->render('/workouts/edit.twig', array('workout' => $workout, 'types' => $types));
    } else {
        $app->abort(404, "Workout does not exist.");
    }
});

$workouts->post('/edit', function(Request $request) use ($app) {
    $workout_model = new Workout();
    $user = $app['session']->get('user');
    $workouts = $workout_model->get_workouts_by_user($user['id']);
    $types = $workout_model->get_workout_types();

    $params['user_id'] = $user['id'];
    $editable_fields = ['title', 'duration', 'comments'];
    foreach ($editable_fields as $field) {
        $params[$field] = $request->get($field);
    }

    $params['type'] = $request->get('type_text') ? $request->get('type_text') : $request->get('type_select');
    $date = date("Y-m-d",strtotime($request->get('date')));
    $time = date("H:i:s", strtotime($request->get('time')));
    $params['date'] = "$date $time";

    $result = $workout_model->save_workout($params, $request->get('id'));

    if ($result) {
        $app['session']->getFlashBag()->add('save_success', 'Workout saved.');
        return $app->redirect( APP_PATH . '/workouts/'. $result['id'] );
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