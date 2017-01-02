<?php

if (empty($_GET['code']) || $_GET['code'] !== '4jCeZuJoNVkQCoFMoVBgptTQtvctpMkF' || empty($_POST)) {
    header('HTTP/1.0 404 Not Found');
    die();
}

if (file_exists('update.lock')) {
    die('update.lock exists!');
}

$commands = [
    'cd ..',
    'git pull',
    'composer update --no-dev',
    'vendor/bin/robo phar:build'
];

exec(implode(' && ', $commands), $output);

mail('Lars@Malach.de', 'RoboUpdate', implode(PHP_EOL, $output));
