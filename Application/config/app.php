<?php

    use Dotenv\Dotenv;

    $dotenv = Dotenv::createImmutable(__DIR__.'/../');
    $dotenv->load();

    date_default_timezone_set('Europe/Warsaw');

    function config($key) {
        return $key === 'APP_ROOT' ? dirname(__DIR__) : $_ENV[$key];
    }
?>
