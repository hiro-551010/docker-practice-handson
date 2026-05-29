<?php

$dbStatus = 'not checked';

try {
    $pdo = new PDO(
        sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            getenv('DB_HOST') ?: 'db',
            getenv('DB_PORT') ?: '3306',
            getenv('DB_DATABASE') ?: 'app_db',
        ),
        getenv('DB_USERNAME') ?: 'app_user',
        getenv('DB_PASSWORD') ?: 'password',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ],
    );

    $message = $pdo->query('SELECT body FROM messages ORDER BY id DESC LIMIT 1')->fetch();
    $dbStatus = $message['body'] ?? 'connected';
} catch (Throwable $e) {
    $dbStatus = $e->getMessage();
}
?>
<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>Docker Practice Handson Step 2</title>
</head>
<body>
    <h1>Step 2: app + db</h1>
    <p>DB status: <?= htmlspecialchars($dbStatus, ENT_QUOTES, 'UTF-8') ?></p>
</body>
</html>
