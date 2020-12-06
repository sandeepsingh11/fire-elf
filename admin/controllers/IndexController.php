<?php

class IndexController extends Controller {

    function __construct() {
        
    }

    public function get() {
        include_once __DIR__ . '/../views/index.php';
    }
}