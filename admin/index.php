<?php

/**
 * include config vals
 * include db connection
 * autoload to include all classes
 */
require __DIR__ . '/config.php';
require __DIR__ . '/../vendor/autoload.php';



/**
 * get url and redirect accordingly
 */
Router::load(__DIR__ . '/routes.php')->direct(getUri(), getMethod());

function getUri() {
    $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

    return $uri;
}

function getMethod() {
    $method = $_SERVER['REQUEST_METHOD'];

    return $method;
}