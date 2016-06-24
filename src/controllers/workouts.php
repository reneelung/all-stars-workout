<?php

$workouts = $app['controllers_factory'];
$workouts->get('/', function() {
    return "Workouts Home";
});
$workouts->get('/add', function() {
    return "Add a workout";
});

$app->mount('/workouts', $workouts);