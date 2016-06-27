<?php
use Symfony\Component\HttpFoundation\Request;

$team = $app['controllers_factory'];
$team->get('/', function() use($app) {
    $workout_model = new Workout();
    $types = $workout_model->get_workout_types();
    return $app['twig']->render('/team/progress.twig', array('types' => $types));
});

$app->mount('/team', $team);