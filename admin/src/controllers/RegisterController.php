<?php

class RegisterController extends Controller {

    private $User;



    public function __construct($session)
    {
        parent::__construct($session);
        $this->User = new User();
    }



    public function get() {
        // inject css
        $this->css = array(
            $this->getStylesheet('normalize'),
            $this->getStylesheet('main')
        );



        $this->page_title = 'Register';
        $this->messages = $this->Session->getAllMessages();
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