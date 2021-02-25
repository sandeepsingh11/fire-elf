<?php

class LoginController extends Controller {

    private $Elves;
    private $Session;

    public $messages;



    public function __construct()
    {
        $this->Elves = new Elves();
        $this->Session = new Session();
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

            if ($this->Elves->login($username, $pazz)) {
                header('Location: /');
            }
            else {
                header('Location: /login');
            }
        }
    }
}