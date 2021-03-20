<?php

class IndexController extends Controller {


    function __construct(...$models) {
        parent::__construct($models);

        // continue only if user is logged in
        $this->isLoggedIn();
    }



    public function index() {
        // inject css
        $css_arr = array(
            $this->getStylesheet('normalize'),
            $this->getStylesheet('main')
        );
        $this->css = $css_arr;
        
        
        
        $this->page_title = 'Home';
        $this->view('index');
    }
}