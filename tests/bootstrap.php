<?php

/**
 * Bootstrap des tests : force SQLite en mémoire avant le chargement de Laravel
 * pour que les tests ne dépendent pas de MySQL (phpunit.xml peut être écrasé par .env).
 */
putenv('APP_ENV=testing');
putenv('DB_CONNECTION=sqlite');
putenv('DB_DATABASE=:memory:');
$_ENV['APP_ENV'] = 'testing';
$_ENV['DB_CONNECTION'] = 'sqlite';
$_ENV['DB_DATABASE'] = ':memory:';

require __DIR__ . '/../vendor/autoload.php';
