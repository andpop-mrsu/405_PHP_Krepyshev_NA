<?php
session_start();
require_once 'init_db.php';

if (empty($_SESSION['game_id'])) {
    header('Location: index.php');
    exit;
}

$db = get_db();
$game_id = $_SESSION['game_id'];
$round_num = $_SESSION['round'] ?? 1;

// Проверяем что игра существует
$stmt = $db->prepare("SELECT * FROM games WHERE id = ?");
$stmt->execute([$game_id]);
$game = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$game) {
    header('Location: index.php');
    exit;
}

if ($game['finished']) {
    header('Location: result.php');
    exit;
}

$total_rounds = $game['total'];
$message = '';

// функция НОД
function my_gcd($a, $b) {
    while ($b != 0) {
        $t = $b;
        $b = $a % $b;
        $a = $t;
    }
    return $a;
}

// получить или создать текущий раунд
$stmt = $db->prepare("SELECT * FROM rounds WHERE game_id = ? ORDER BY id LIMIT ?, 1");
$stmt->execute([$game_id, $round_num - 1]);
$round = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$round) {
    // создаём новый раунд
    $a = rand(1, 100);
    $b = rand(1, 100);
    $gcd = my_gcd($a, $b);
    $db->prepare("INSERT INTO rounds (game_id, a, b, gcd) VALUES (?, ?, ?, ?)")
       ->execute([$game_id, $a, $b, $gcd]);
    $stmt = $db->prepare("SELECT * FROM rounds WHERE game_id = ? ORDER BY id LIMIT ?, 1");
    $stmt->execute([$game_id, $round_num - 1]);
    $round = $stmt->fetch(PDO::FETCH_ASSOC);
}

// обработка ответа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answer'])) {
    $answer = trim($_POST['answer']);
    $is_correct = (intval($answer) === intval($round['gcd'])) ? 1 : 0;

    // сохранить ответ
    $db->prepare("UPDATE rounds SET user_answer = ?, is_correct = ? WHERE id = ?")
       ->execute([$answer, $is_correct, $round['id']]);

    if ($is_correct) {
        $message = 'Правильно! НОД = ' . $round['gcd'];
        // обновить счёт
        $db->prepare("UPDATE games SET score = score + 1 WHERE id = ?")->execute([$game_id]);
    } else {
        $message = 'Неправильно. Правильный ответ: ' . $round['gcd'];
    }

    // следующий раунд или конец
    if ($round_num >= $total_rounds) {
        $db->prepare("UPDATE games SET finished = 1 WHERE id = ?")->execute([$game_id]);
        $_SESSION['round'] = 1;
        // редирект после небольшой задержки через мета-тег
        $go_result = true;
    } else {
        $_SESSION['round'] = $round_num + 1;
        $go_next = true;
    }
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Игра НОД - Раунд <?= $round_num ?></title>
    <?php if (!empty($go_result)): ?>
    <meta http-equiv="refresh" content="2; url=result.php">
    <?php elseif (!empty($go_next)): ?>
    <meta http-equiv="refresh" content="2; url=play.php">
    <?php endif ?>
</head>
<body>
<h1>Игра НОД</h1>
<p>Игрок: <b><?= htmlspecialchars($_SESSION['player_name'] ?? '') ?></b> | Раунд: <?= $round_num ?> из <?= $total_rounds ?></p>

<?php if ($message): ?>
    <p><b><?= htmlspecialchars($message) ?></b></p>
    <p>Переход через 2 секунды...</p>
<?php else: ?>
    <p>Найдите НОД чисел:</p>
    <h2><?= $round['a'] ?> и <?= $round['b'] ?></h2>
    <form method="post">
        <label>Ваш ответ: <input type="number" name="answer" autofocus></label>
        <input type="submit" value="Ответить">
    </form>
<?php endif ?>

<br>
<a href="index.php">На главную</a>
</body>
</html>
