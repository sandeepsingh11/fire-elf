<?php

abstract class Controller {

    protected $Session;
    protected $User;
    protected $Page;
    protected $Media;
    protected $Blog;

    protected $route_title;
    protected $css = [];
    protected $js = [];


    /**
     * @param array $models included models
     */
    public function __construct($models)
    {
        // accepts variable args (from Router class).
        // if adding a new model, add a new case
        for ($i = 0; $i < sizeof($models); $i++) {
           
            $className = get_class($models[$i]);
            switch ($className) {
                case 'Session':
                    $this->Session = $models[$i];
                    break;
                
                case 'User':
                    $this->User = $models[$i];
                    break;

                case 'Page':
                    $this->Page = $models[$i];
                    break;

                case 'Media':
                    $this->Media = $models[$i];
                    break;

                case 'Blog':
                    $this->Blog = $models[$i];
                    break;
            }
        }
    }



    /**
     * Pretty print PHP array
     * 
     * @param array $array any array to pretty print
     */
    public static function prettyPrint($array) {
        echo '<pre>'.print_r($array, true).'</pre>';
    }



    /**
     * Check if running a dev or prod server
     * 
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
     * Escape html string
     * 
     * @param string $html the unescaped html string
     * 
     * @return string an escaped html string
     */
    public function escape($html) {
        return htmlspecialchars($html, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }


    
    /**
     * Get an admin page. 
     * Requires the path to the view file
     * 
     * @param string $route name of the page
     */ 
    protected function view($route) {
        $route = strtolower($route);
        
        require_once $_SERVER['DOCUMENT_ROOT'] . '/../src/views/' . $route . '.php';
    }



    /**
     * Get a local stylesheet file
     * 
     * @param string $css name of the css file
     * 
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
     * 
     * @param string $js name of the js file
     * 
     * @return string script url
     */ 
    protected function getScript($js) {
        return 'http://' . $_SERVER['HTTP_HOST'] . '/js/' . $js . '.js';
    }



    /**
     * The current page is login-protected; 
     * checks if the user is logged in. If
     * not, then redirect to login page
     */
    protected function isLoggedIn() {
        if (!$this->Session->isLoggedIn()) {
            $this->redirect('login');
        }
    }



    /**
     * Redirect to the specified route / page
     * 
     * @param string $route name of the route to redirect to
     */
    protected function redirect($route) {
        $route = strtolower($route);

        header('Location: /' . $route);
        exit;
    }
}