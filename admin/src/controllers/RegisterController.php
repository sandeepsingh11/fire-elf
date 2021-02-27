<?php

class RegisterController extends Controller {


    function __construct(...$models) {
        parent::__construct($models);

        // continue only if user is logged in
        $this->isLoggedIn();
    }



    public function get() {
        // inject css
        $this->css = array(
            $this->getStylesheet('normalize'),
            $this->getStylesheet('main')
        );



        $this->page_title = 'Register';
        $this->view('register');
    }


    public function post() {
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