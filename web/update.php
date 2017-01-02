<?php

if (empty($_GET['code']) ||
    !file_exists('../../.updateCode.txt') ||
    trim(file_get_contents('../../.updateCode.txt')) !== $_GET['code']
) {
    header('HTTP/1.0 404 Not Found');
    exit();
}

if (file_exists('update.lock')) {
    exit('update.lock exists!');
}

$commands = [
    'cd ..',
    'git pull',
    'composer update --no-dev',
    'vendor/bin/robo phar:build'
];

exec('(' . implode(' && ', $commands) . ') 2>&1', $output);

mail(
    'Lars@Malach.de',
    'RoboUpdate',
    implode(PHP_EOL, $output) . PHP_EOL . PHP_EOL .
    print_r(['$_SERVER' => $_SERVER, '$_POST' => $_POST, '$_GET' => $_GET], true)
);
