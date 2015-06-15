<?php

require '../vendor/autoload.php';
require __DIR__ . '/../models/Task.php';
$_ENV = require __DIR__ . '/../.env.php';

$app = new \Slim\Slim(array(
        "MODE" => $_ENV['MODE'],
        "TEMPLATES.PATH" => './templates'
    ));

$app->get('/', function() use ($app) {

    $tasks = Task::all();

    $app->render('template.php', ['tasks' => $tasks]);
});

$app->post('/tasks', function() use ($app) {

    if(empty($_POST['name'])) {
        $app->redirect('/');

        exit;
    }

    $now = date("Y-m-d H:i:s");

    $task = new Task();
    $task->name        = $_POST['name'];
    $task->is_complete = false;
    $task->updated_at  = $now;
    $task->created_at  = $now;
    $task->save();

    $app->redirect('/');
});

$app->post('/tasks/:id/toggle-complete', function($id) use ($app) {

    $task = Task::find($id);

    $task->updated_at = date('Y-m-d H:i:s');

    $task->is_complete = !$task->is_complete;
    // $task->toggleComplete();

    $task->save();
    $app->redirect('/');
});

$app->post('/tasks/clear-complete', function() use ($app) {

    Task::clearComplete();

    $app->redirect('/');
});


$app->run();

?>