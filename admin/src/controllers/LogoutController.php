<?php

class LogoutController extends Controller {

    private $User;

    public $messages;



    public function __construct($session)
    {
        parent::__construct($session);
        $this->User = new User();
    }



    public function get() {
        $this->User->logout();

        $this->Session->setSuccess('Logout successful!');

        header('Location: /login');
    }
}