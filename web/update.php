<?php

if (empty($_GET['code']) ||
    !file_exists('../../.updateCode.txt') ||
    trim(file_get_contents('../../.updateCode.txt')) !== $_GET['code']
) {
    header('HTTP/1.0 404 Not Found');
    exit();
}

$lockFile = fopen('update.lock', 'w');
if (!flock($lockFile, LOCK_EX)) {
    exit('update.lock exists!');
}

$commands = [
    'cd ..',
    'git pull',
    'composer update --no-dev',
    'php vendor/bin/robo phar:build'
];

exec('(' . implode(' && ', $commands) . ') 2>&1', $output);

mail(
    'Lars@Malach.de',
    'RoboUpdate',
    implode(PHP_EOL, $output) . PHP_EOL . PHP_EOL .
    print_r([
        'input' => json_decode(file_get_contents('php://input') ?: '[]', true),
        '$_SERVER' => $_SERVER,
        '$_POST' => $_POST,
        '$_GET' => $_GET
    ], true)
);

flock($lockFile, LOCK_UN);
@unlink('update.lock');