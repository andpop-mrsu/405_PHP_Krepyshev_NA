<?php
function get_db() {
    $db_path = dirname(__DIR__) . '/db/game.db';
    $db = new PDO('sqlite:' . $db_path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $db->exec("CREATE TABLE IF NOT EXISTS players (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL UNIQUE
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS games (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        player_id INTEGER NOT NULL,
        date TEXT NOT NULL,
        score INTEGER DEFAULT 0,
        total INTEGER DEFAULT 0,
        finished INTEGER DEFAULT 0,
        FOREIGN KEY(player_id) REFERENCES players(id)
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS rounds (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        game_id INTEGER NOT NULL,
        a INTEGER NOT NULL,
        b INTEGER NOT NULL,
        gcd INTEGER NOT NULL,
        user_answer TEXT,
        is_correct INTEGER,
        FOREIGN KEY(game_id) REFERENCES games(id)
    )");

    return $db;
}
?>
