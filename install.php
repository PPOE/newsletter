<?php

require_once('config.inc.php');

try {
    if ($dbLang === 'pgsql') {
        $pdo->query('CREATE TABLE IF NOT EXISTS users (
            id SERIAL,
            email VARCHAR(256) NOT NULL,
            prefs INT NOT NULL,
            sid INT NOT NULL,
            confirmed INT NOT NULL DEFAULT 0
        );');
        $pdo->query('CREATE TABLE IF NOT EXISTS mail_queue (
            id SERIAL,
            mto VARCHAR(256),
            msubject TEXT,
            mbody TEXT,
            mheaders TEXT
        );');
    } else if ($dbLang === 'mysql') {
        $pdo->query('CREATE TABLE IF NOT EXISTS users (
            id INT NOT NULL AUTO_INCREMENT,
            email VARCHAR(256) NOT NULL,
            prefs INT NOT NULL,
            sid INT NOT NULL,
            confirmed INT NOT NULL DEFAULT 0
        );');
        $pdo->query('CREATE TABLE IF NOT EXISTS mail_queue (
            id INT NOT NULL AUTO_INCREMENT,
            mto VARCHAR(256),
            msubject LONGTEXT,
            mbody LONGTEXT,
            mheaders LONGTEXT
        );');
    }
} catch (\Exception $exception) {
    exit($exception->getMessage());
}

header('Location: index.php');
