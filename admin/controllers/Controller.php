<?php

abstract class Controller {
    protected $page_title;

    /**
     * get the page 
     */ 
    public function view($page) {
        $page = strtolower($page);

        return $_SERVER['DOCUMENT_ROOT'] . '/src/views/' . $page . '.php';
    }


    protected function getScript($js) {
        return 'http://' . $_SERVER['HTTP_HOST'] . '/js/' . $js . '.js';
    }
}