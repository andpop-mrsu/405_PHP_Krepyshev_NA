<?php
session_start();
require_once 'init_db.php';

$db = get_db();
$game_id = isset($_GET['id']) ? intval($_GET['id']) : intval($_SESSION['game_id'] ?? 0);

if (!$game_id) {
    header('Location: index.php');
    exit;
}

$stmt = $db->prepare("SELECT g.*, p.name FROM games g JOIN players p ON g.player_id = p.id WHERE g.id = ?");
$stmt->execute([$game_id]);
$game = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$game) {
    echo 'Игра не найдена. <a href="index.php">На главную</a>';
    exit;
}

$stmt = $db->prepare("SELECT * FROM rounds WHERE game_id = ? ORDER BY id");
$stmt->execute([$game_id]);
$rounds = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Результат игры</title>
</head>
<body>
<h1>Результат игры</h1>

<p>Игрок: <b><?= htmlspecialchars($game['name']) ?></b></p>
<p>Дата: <?= htmlspecialchars($game['date']) ?></p>
<p>Результат: <b><?= $game['score'] ?> из <?= $game['total'] ?></b></p>

<h2>Раунды:</h2>
<table border="1" cellpadding="5">
    <tr>
        <th>#</th>
        <th>Число A</th>
        <th>Число B</th>
        <th>НОД</th>
        <th>Ваш ответ</th>
        <th>Результат</th>
    </tr>
    <?php $i = 1; foreach ($rounds as $r): ?>
    <tr>
        <td><?= $i++ ?></td>
        <td><?= $r['a'] ?></td>
        <td><?= $r['b'] ?></td>
        <td><?= $r['gcd'] ?></td>
        <td><?= htmlspecialchars($r['user_answer'] ?? '-') ?></td>
        <td><?= $r['is_correct'] ? 'Верно' : 'Неверно' ?></td>
    </tr>
    <?php endforeach ?>
</table>

<br>
<a href="index.php">Играть снова</a> |
<a href="history.php">История игр</a>
</body>
</html>
