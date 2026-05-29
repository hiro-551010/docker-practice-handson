<?php

$dbStatus = 'not checked';
$mailStatus = 'not checked';

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

$socket = @fsockopen(getenv('MAIL_HOST') ?: 'mail', (int) (getenv('MAIL_PORT') ?: 1025), $errno, $errstr, 5);
if ($socket) {
    $mailStatus = 'mail:1025 is reachable';
    fclose($socket);
} else {
    $mailStatus = sprintf('%s (%d)', $errstr, $errno);
}

$mailWebPort = getenv('MAIL_WEB_PORT') ?: '8025';
$mailWebUrl = sprintf('http://localhost:%s', $mailWebPort);
?>
<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>Docker Practice Handson Step 3</title>
</head>
<body>
    <h1>Step 3: app + db + mail</h1>
    <p>DB status: <?= htmlspecialchars($dbStatus, ENT_QUOTES, 'UTF-8') ?></p>
    <p>Mail status: <?= htmlspecialchars($mailStatus, ENT_QUOTES, 'UTF-8') ?></p>
    <p>Mailpit Web UI: <a href="<?= htmlspecialchars($mailWebUrl, ENT_QUOTES, 'UTF-8') ?>"><code><?= htmlspecialchars($mailWebUrl, ENT_QUOTES, 'UTF-8') ?></code></a></p>
</body>
</html>
