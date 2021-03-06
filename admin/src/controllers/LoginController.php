<?php

class LoginController extends Controller {

    
    function __construct(...$models) {
        parent::__construct($models);
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


        $this->page_title = 'Login';
        $this->view('login');
    }



    public function login() {
        if ( ($_POST['username'] == '') || ($_POST['pazz'] == '') ) {
            if ($_POST['username'] != '') {
                $username = $_POST['username'];
            }
            else {
                $username = '';
            }

            $this->Session->setError('Please fill in all required fields.', [$username]);

            header('Location: /login');
        }
        else {
            $username = $_POST['username'];
            $pazz = $_POST['pazz'];
            $csrf = $_POST['csrf-token'];

            if ($this->User->login($username, $pazz, $csrf, $this->Session)) {
                header('Location: /');
            }
            else {
                header('Location: /login');
            }
        }
    }
}