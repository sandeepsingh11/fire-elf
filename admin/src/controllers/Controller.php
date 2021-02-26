<?php

abstract class Controller {

    protected $Session;
    protected $page_title;
    protected $css = [];
    protected $js = [];


    public function __construct($session)
    {
        $this->Session = $session;
    }



    /**
     * Pretty print PHP array
     * @param array $array
     */
    public static function prettyPrint($array) {
        echo '<pre>'.print_r($array, true).'</pre>';
    }



    /**
     * Check if running a dev or prod server
     * @return bool true if dev, false if prod
     */
    public function isDevServer() {
        if (filter_var(ini_get('display_errors'),FILTER_VALIDATE_BOOLEAN)) {
            return true;
        } 
        else {
            return false;
        }
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
        if ($this->isDevServer()) {
            return 'http://' . $_SERVER['HTTP_HOST'] . '/styles/css-dev/' . $css . '.css';
        }
        else {
            return 'http://' . $_SERVER['HTTP_HOST'] . '/styles/css/' . $css . '.css';
        }
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