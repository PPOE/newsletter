<?php

require_once('config.inc.php');

try {
    $pdo->query('SELECT 1 FROM users LIMIT 1;');
} catch (\Exception $exception) {
    header('Location: install.php');
}

header('Location: register.php');
