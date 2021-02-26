<?php

class IndexController extends Controller {

    public $messages;



    function __construct($session) {
        parent::__construct($session);
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