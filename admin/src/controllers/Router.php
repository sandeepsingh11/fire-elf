<?php

class Router {

    /** 
     * Registered GET and POST route list
    */
    public $routes = [
        'GET' => [],
        'POST' => []
    ];



    public function __construct($routesFile)
    {
        $this->load($routesFile);
    }



    /**
     * Load the registered routes list
     * @param string $routesFile
     * @return Router new router object with GET and POST values
     */
    public function load($routesFile) {
        // populates $routes values
        require $routesFile;
    }



    /**
     * Store a GET route.
     * @param string $uri the route
     * @param string $controller [controller]@[method]
     */
    public function get($uri, $controller) {
        $this->routes['GET'][$uri] = $controller;
    }



    /**
     *  Store a POST route.
     * @param string $uri the route
     * @param string $controller [controller]@[method]
     */
    public function post($uri, $controller) {
        $this->routes['POST'][$uri] = $controller;
    }



    /**
     * Get the URI (route) from the URL
     * @return string the URI
     */
    function getUri() {
        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    
        return $uri;
    }
    


    /**
     * Get the method (GET or POST)
     * @return string the method
     */
    function getMethod() {
        $method = $_SERVER['REQUEST_METHOD'];
    
        return $method;
    }



    /**
     * Load the URI's requested controller method
     * @param string $uri the route
     * @param string $method 'GET' or 'POST'
     * @return mixed call callAction() or throw error
     */
    public function direct($uri, $method) {

        if (array_key_exists($uri, $this->routes[$method])) {
            // if route ($uri) exists in routes list
            
            $controller__method = explode('@', $this->routes[$method][$uri]);

            // [0] = controller
            // [1] = method
            return $this->callAction($controller__method[0], $controller__method[1]);
        }
        else {
            // route ($uri) does not exist in routes list

            throw new Exception("/$uri [$method] route not recognized. Check your 'routes.php' file");
            // include_once __DIR__ . '/../views/404.php';
        }
        // else {
        //     return $this->callAction(
        //         ...explode('@', $this->routes[$method]['404'])
        //     );
        // }
    }


    
    /**
     * Create controller and call controller's method
     * @param string $controller controller name
     * @param string $action controller's action (method)
     * @return mixed call [controller]@[action]
     */
    public function callAction($controller, $action) {
        // include Session model in all controllers
        $session = new Session();

        $controller = new $controller($session);

        if (! method_exists($controller, $action) ) {
            throw new Exception("{$controller} does not respond to the {$action} action.");
        }

        return $controller->$action();
    }
}