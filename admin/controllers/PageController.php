<?php

class PageController extends Controller {

    private $pages;

    function __construct() {
        $this->pages = new Pages();    
    }

    public function get() {
        $pagesInfo_arr = $this->pages->getPagesInfo();
        $pagesInfo_arr = $pagesInfo_arr['pages'];
        
        include_once __DIR__ . '/../views/pages.php';
    }
}