<?php

class LogoutController extends Controller {

   
    function __construct(...$models) {
        parent::__construct($models);

        // continue only if user is logged in
        $this->isLoggedIn();
    }



    public function logout() {
        $this->User->logout();

        $this->Session->setSuccess('Logout successful!');

        header('Location: /login');
    }
}