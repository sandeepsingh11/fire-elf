<?php

abstract class Controller {
    protected $page_title;
    protected $css = [];
    protected $js = [];



    /**
     * Pretty print PHP array
     * @param array $array
     */
    public static function prettyPrint($array) {
        echo '<pre>'.print_r($array, true).'</pre>';
    }

    
    /**
     * Get an admin page. 
     * Requires the path to the view file
     * @param string $page name of the page
     */ 
    protected function view($page) {
        $page = strtolower($page);
        
        require_once $_SERVER['DOCUMENT_ROOT'] . '/../src/views/' . $page . '.php';
    }



    /**
     * Get a local stylesheet file
     * @param string $css name of the css file
     * @return string stylesheet url
     */ 
    protected function getStylesheet($css) {
        return 'http://' . $_SERVER['HTTP_HOST'] . '/css/' . $css . '.css';
    }



    /**
     * Get a local script file
     * @param string $js name of the js file
     * @return string script url
     */ 
    protected function getScript($js) {
        return 'http://' . $_SERVER['HTTP_HOST'] . '/js/' . $js . '.js';
    }
}