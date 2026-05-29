<?php

$message = getenv('APP_MESSAGE') ?: 'Step 1: app container is running.';
$environment = getenv('APP_ENV') ?: 'local';
?>
<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>Docker Practice Handson</title>
</head>
<body>
    <h1>Docker Practice Handson</h1>
    <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
    <p>Environment: <?= htmlspecialchars($environment, ENT_QUOTES, 'UTF-8') ?></p>
    <p>Edit <code>src/index.php</code>, then reload the browser.</p>
</body>
</html>
