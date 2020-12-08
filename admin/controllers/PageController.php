<?php

class PageController extends Controller {

    private $pages;

    function __construct() {
        $this->pages = new Pages();    
    }

    public function get() {
        $pageList = $this->pages->getPageList();
        $pageList = $pageList['pages'];
        
        include_once __DIR__ . '/../views/pages.php';
    }
}