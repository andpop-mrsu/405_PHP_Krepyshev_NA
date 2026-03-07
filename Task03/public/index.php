<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;

$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);

function db() {
    $d = new PDO('sqlite:' . __DIR__ . '/../db/g.db');
    $d->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $d->exec('CREATE TABLE IF NOT EXISTS g (id INTEGER PRIMARY KEY AUTOINCREMENT, a INTEGER, b INTEGER, ts INTEGER)');
    $d->exec('CREATE TABLE IF NOT EXISTS s (id INTEGER PRIMARY KEY AUTOINCREMENT, gid INTEGER, ans INTEGER, correct INTEGER, ok INTEGER, ts INTEGER)');
    return $d;
}

$app->get('/', function ($req, $res) {
    $html = file_get_contents(__DIR__ . '/index.html');
    $res->getBody()->write($html);
    return $res->withHeader('Content-Type', 'text/html');
});

$app->get('/games', function ($req, $res) {
    $d = db();
    $gs = $d->query('SELECT * FROM g ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
    foreach ($gs as &$g) {
        $st = $d->prepare('SELECT * FROM s WHERE gid = ?');
        $st->execute([$g['id']]);
        $g['steps'] = $st->fetchAll(PDO::FETCH_ASSOC);
    }
    $res->getBody()->write(json_encode($gs));
    return $res->withHeader('Content-Type', 'application/json');
});

$app->get('/games/{id}', function ($req, $res, $args) {
    $d = db();
    $st = $d->prepare('SELECT * FROM g WHERE id = ?');
    $st->execute([$args['id']]);
    $g = $st->fetch(PDO::FETCH_ASSOC);
    $st2 = $d->prepare('SELECT * FROM s WHERE gid = ?');
    $st2->execute([$args['id']]);
    $g['steps'] = $st2->fetchAll(PDO::FETCH_ASSOC);
    $res->getBody()->write(json_encode($g));
    return $res->withHeader('Content-Type', 'application/json');
});

$app->post('/games', function ($req, $res) {
    $d = db();
    $b = json_decode($req->getBody(), true);
    $st = $d->prepare('INSERT INTO g (a, b, ts) VALUES (?, ?, ?)');
    $st->execute([$b['a'], $b['b'], time()]);
    $res->getBody()->write(json_encode(['id' => $d->lastInsertId()]));
    return $res->withHeader('Content-Type', 'application/json');
});

$app->post('/step/{id}', function ($req, $res, $args) {
    $d = db();
    $b = json_decode($req->getBody(), true);
    $st = $d->prepare('INSERT INTO s (gid, ans, correct, ok, ts) VALUES (?, ?, ?, ?, ?)');
    $st->execute([$args['id'], $b['ans'], $b['correct'], $b['ok'], time()]);
    $res->getBody()->write(json_encode(['ok' => true]));
    return $res->withHeader('Content-Type', 'application/json');
});

$app->run();
