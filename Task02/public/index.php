<?php
$db = new PDO('sqlite:' . dirname(__DIR__) . '/db/data.db');
$db->exec("CREATE TABLE IF NOT EXISTS res (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT,
    dt TEXT,
    a INTEGER,
    b INTEGER,
    gcd INTEGER,
    ans INTEGER,
    ok INTEGER
)");

function gcd($a, $b) {
    while ($b) { $t = $b; $b = $a % $b; $a = $t; }
    return $a;
}

$a = rand(10, 99);
$b = rand(10, 99);
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $ans  = (int)($_POST['ans'] ?? 0);
    $a    = (int)$_POST['a'];
    $b    = (int)$_POST['b'];
    $gcd  = gcd($a, $b);
    $ok   = (int)($ans === $gcd);

    $db->prepare("INSERT INTO res (name,dt,a,b,gcd,ans,ok) VALUES (?,?,?,?,?,?,?)")
       ->execute([$name, date('Y-m-d H:i:s'), $a, $b, $gcd, $ans, $ok]);

    $msg = $ok ? "Верно! НОД = $gcd" : "Неверно. Правильный ответ: $gcd";

    $a = rand(10, 99);
    $b = rand(10, 99);
}
?>
<!doctype html>
<html lang="ru">
<head><meta charset="utf-8"><title>НОД</title></head>
<body>
<h2>Найдите НОД</h2>
<?php if ($msg): ?><p><b><?= $msg ?></b></p><?php endif ?>
<p>Числа: <b><?= $a ?></b> и <b><?= $b ?></b></p>
<form method="post">
    <input type="hidden" name="a" value="<?= $a ?>">
    <input type="hidden" name="b" value="<?= $b ?>">
    Имя: <input type="text" name="name"><br><br>
    НОД: <input type="number" name="ans"><br><br>
    <input type="submit" value="Ответить">
</form>
<br><a href="log.php">История</a>
</body>
</html>
