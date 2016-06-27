<?php
use Symfony\Component\HttpFoundation\Request;

$team = $app['controllers_factory'];
$team->get('/', function() use($app) {
    $workout_model = new Workout();
    $types = $workout_model->get_workout_types();
    return $app['twig']->render('/team/progress.twig', array('types' => $types));
});

$team->get('/members', function() use($app) {
    $user_model = new User();
    $members = $user_model->get_all();
    return $app['twig']->render('/team/members.twig', array('members' => $members));
});

$app->mount('/team', $team);