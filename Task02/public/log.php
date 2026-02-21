<?php
$db = new PDO('sqlite:' . dirname(__DIR__) . '/db/data.db');
$rows = $db->query("SELECT * FROM res ORDER BY id DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="ru">
<head><meta charset="utf-8"><title>История</title></head>
<body>
<h2>История игр</h2>
<table border="1" cellpadding="4">
    <tr><th>Имя</th><th>Дата</th><th>A</th><th>B</th><th>НОД</th><th>Ответ</th><th>Результат</th></tr>
    <?php foreach ($rows as $r): ?>
    <tr>
        <td><?= htmlspecialchars($r['name']) ?></td>
        <td><?= $r['dt'] ?></td>
        <td><?= $r['a'] ?></td>
        <td><?= $r['b'] ?></td>
        <td><?= $r['gcd'] ?></td>
        <td><?= $r['ans'] ?></td>
        <td><?= $r['ok'] ? 'Верно' : 'Неверно' ?></td>
    </tr>
    <?php endforeach ?>
</table>
<br><a href="index.php">Играть</a>
</body>
</html>
