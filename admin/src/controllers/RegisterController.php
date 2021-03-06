<?php

class RegisterController extends Controller {


    function __construct(...$models) {
        parent::__construct($models);

        // continue only if user is logged in
        $this->isLoggedIn();
    }



    public function index() {
        // inject css
        $this->css = array(
            $this->getStylesheet('normalize'),
            $this->getStylesheet('main')
        );


        // inject js
        $this->js = array(
            $this->getScript('validate')
        );
        

        $this->page_title = 'Register';
        $this->view('register');
    }


    public function register() {
        if ( ($_POST['username'] == '') || ($_POST['pazz'] == '') ) {
            if ($_POST['username'] != '') {
                $username = $_POST['username'];
            }
            else {
                $username = '';
            }

            $this->Session->setError('Please fill in all required fields.', [$username]);

            header('Location: /register');
        }
        else {
            $username = $_POST['username'];
            $pazz = $_POST['pazz'];

            $this->User->register($username, $pazz);   
        }
    }
}