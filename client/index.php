<?php
$uri = $_SERVER['REQUEST_URI'];

// if uri is /, set to index.php
($uri == '/') ? $uri = '/index' : null;

// set page path
$pagePath = __DIR__ . '/pages' . $uri . '.php';

// display
require_once($pagePath);