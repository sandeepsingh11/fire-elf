<?php

class PageController extends Controller {

    function __construct() {
        
    }

    public function get() {
        include_once __DIR__ . '/../views/pages.php';
    }
}