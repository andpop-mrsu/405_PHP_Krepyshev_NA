<?php
require_once 'init_db.php';

$db = get_db();

$stmt = $db->query("SELECT g.id, g.date, g.score, g.total, g.finished, p.name
                    FROM games g
                    JOIN players p ON g.player_id = p.id
                    ORDER BY g.id DESC
                    LIMIT 50");
$games = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>История игр</title>
</head>
<body>
<h1>История игр</h1>

<?php if (empty($games)): ?>
    <p>Игр пока нет.</p>
<?php else: ?>
<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Игрок</th>
        <th>Дата</th>
        <th>Счёт</th>
        <th>Статус</th>
        <th>Подробнее</th>
    </tr>
    <?php foreach ($games as $g): ?>
    <tr>
        <td><?= $g['id'] ?></td>
        <td><?= htmlspecialchars($g['name']) ?></td>
        <td><?= htmlspecialchars($g['date']) ?></td>
        <td><?= $g['score'] ?> / <?= $g['total'] ?></td>
        <td><?= $g['finished'] ? 'Завершена' : 'В процессе' ?></td>
        <td><a href="result.php?id=<?= $g['id'] ?>">Смотреть</a></td>
    </tr>
    <?php endforeach ?>
</table>
<?php endif ?>

<br>
<a href="index.php">На главную</a>
</body>
</html>
