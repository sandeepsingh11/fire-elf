<?php

class LoginController extends Controller {

    private $User;

    public $messages;



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



        $this->page_title = 'Login';
        $this->messages = $this->Session->getAllMessages();
        $this->view('login');
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