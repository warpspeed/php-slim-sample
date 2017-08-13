<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$_ENV = require '../.env.php';

require '../vendor/autoload.php';
require '../models/Task.php';

$app = new \Slim\App(array(
    "MODE" => $_ENV['MODE'],
    "TEMPLATES.PATH" => './templates'
));

$container = $app->getContainer();
$container['renderer'] = function($container) {
    return new \Slim\Views\PhpRenderer('./templates/');
};

$app->get('/', function($req, $res) use ($app) {
    $tasks = Task::all();
    return $this->renderer->render($res, 'template.php', ['tasks' => $tasks]);
});

$app->post('/tasks', function($req, $res) use ($app) {

    if(empty($_POST['name'])) {
        return $res->withStatus(200)->withHeader('Location', '/');
    }

    $task = new Task($_POST['name']);
    $task->save();

    return $res->withStatus(200)->withHeader('Location', '/');
});

$app->post('/tasks/{id}/toggle-complete', function($req, $res, $args) use ($app) {

    $task = Task::find($args['id']);
    $task->toggleComplete();

    return $res->withStatus(200)->withHeader('Location', '/');
});

$app->post('/tasks/clear-complete', function($req, $res) use ($app) {

    Task::clearComplete();

    return $res->withStatus(200)->withHeader('Location', '/');
});

$app->run();

