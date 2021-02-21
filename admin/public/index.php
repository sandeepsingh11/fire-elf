<?php

/**
 * include config vals
 * autoload to include all classes
 */
require __DIR__ . '/../config.php';
require __DIR__ . '/../../vendor/autoload.php';



// create Router instance and load routes
$Router = new Router(__DIR__ . '/../src/routes.php');

// get uri and method
$uri = $Router->getUri();
$method = $Router->getMethod();

// load the view
$Router->direct($uri, $method);









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