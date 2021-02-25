<?php

class IndexController extends Controller {

    private $Session;

    public $messages;



    function __construct() {
        $this->Session = new Session();
    }



    public function get() {
        // inject css
        $css_arr = array(
            $this->getStylesheet('normalize'),
            $this->getStylesheet('main')
        );
        $this->css = $css_arr;
        
        
        
        $this->page_title = 'Home';
        $this->messages = $this->Session->getAllMessages();
        $this->view('index');
    }
}