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





// error handling (from https://phpdelusions.net/articles/error_reporting#code)
error_reporting(E_ALL);

function exceptionHandler ($e)
{
    error_log($e);
    http_response_code(500);
    if (filter_var(ini_get('display_errors'),FILTER_VALIDATE_BOOLEAN)) {
        echo $e;
    } else {
        echo "<h1>500 Internal Server Error</h1>
              An internal server error has been occurred.<br>
              Please try again later.";
    }
}

set_exception_handler('exceptionHandler');

set_error_handler(function ($level, $message, $file = '', $line = 0)
{
    throw new ErrorException($message, 0, $level, $file, $line);
});

register_shutdown_function(function ()
{
    $error = error_get_last();
    if ($error !== null) {
        $e = new ErrorException(
            $error['message'], 0, $error['type'], $error['file'], $error['line']
        );
        exceptionHandler($e);
    }
});