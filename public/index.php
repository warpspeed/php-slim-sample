<?php
require_once '../vendor/autoload.php';
require_once '../.env.php';

$dbc = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
$dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$app = new \Slim\Slim(array(
        "MODE" => "DEVELOPMENT",
        "TEMPLATES.PATH" => './templates'
    ));

$app->get('/', function() use ($dbc, $app) {

    $createTable = "CREATE TABLE IF NOT EXISTS tasks (
                    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
                    `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
                    `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                    `is_complete` tinyint(1) NOT NULL DEFAULT '0',
                    PRIMARY KEY (`id`))";

    $dbc->exec($createTable);

    $stmt  = $dbc->prepare('SELECT * FROM tasks ORDER BY id DESC');
    $stmt->execute();

    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $app->render('template.php', ['tasks' => $tasks]);
});

$app->post('/tasks', function() use ($dbc, $app) {

    if(empty($_POST['name'])) {
        $app->redirect('/');
    }
    $query       = 'INSERT INTO tasks (name, is_complete, created_at, updated_at)
                    VALUES (?,?,?,?)';
    $name        = $_POST['name'];
    $is_complete = false;
    $now         = date("Y-m-d H:i:s");

    $stmt = $dbc->prepare($query);
    $stmt->execute(array($name, $is_complete, $now, $now));

    $app->redirect('/');
});

$app->post('/tasks/:id/toggle-complete', function($id) use ($dbc, $app) {

    $query = "SELECT is_complete FROM tasks WHERE id = :id";
    $stmt  = $dbc->prepare($query);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    $is_complete = !$task['is_complete'];
    $updated_at  = date("Y-m-d H:i:s");

    $query = "UPDATE tasks SET is_complete = :is_complete, updated_at = :updated_at WHERE id = :id";
    $stmt = $dbc->prepare($query);
    $stmt->bindValue(':id',          $id,          PDO::PARAM_INT);
    $stmt->bindValue(':is_complete', $is_complete, PDO::PARAM_INT);
    $stmt->bindValue(':updated_at',  $updated_at,  PDO::PARAM_STR);
    $stmt->execute();

    $app->redirect('/');
});

$app->post('/tasks/clear-complete', function() use ($app, $dbc) {

    $stmt             = $dbc->query('SELECT id FROM tasks WHERE is_complete = TRUE');
    $tasksToBeRemoved = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($tasksToBeRemoved as $task)
    {
        $dbc->exec("DELETE FROM tasks WHERE id = " . $task['id']);
    }

    $app->redirect('/');
});


$app->run();

?>