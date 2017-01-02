#!/bin/bash

set -x

git pull
composer update --no-dev
php vendor/bin/robo phar:build