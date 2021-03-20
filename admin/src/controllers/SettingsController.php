<?php

class SettingsController extends Controller {

    public $users = [];


    
    public function __construct(...$models)
    {
        array_push($models, new Page());
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
        
        
        // display the view
        $this->page_title = 'Settings';
        $this->view('settings');
    }



    public function updateSettings() {
        $userId = $_POST['user-id'];
        
        if (isset($_POST['new-username'])) {
            // change the user's username
            $newUsername = $_POST['new-username'];
            
            if ($this->User->updateUsername($userId, $newUsername)) {
                $this->Session->setSuccess('Username updated.');
            }
            else {
                $this->Session->setError('Username could not be updated. Please try again.');
            }
        }
        else if (isset($_POST['new-password'])) {
            // change the user's password
            $currentPw = $_POST['current-password'];
            $newPw = $_POST['new-password'];
            
            if ($this->User->updatePassword($userId, $currentPw, $newPw)) {
                $this->Session->setSuccess('Password updated.');
            }
            else {
                $this->Session->setError('Password could not be updated. Please try again.');
            }
        }

        $this->redirect('settings');
    }



    public function deleteSettings() {
        if (isset($_POST['delete-user'])) {
            $userId = $_POST['user-id'];

            if ($this->User->deleteUser($userId)) {
                $this->Session->setSuccess('User deleted.');
            }
            else {
                $this->Session->setError('User could not be deleted.');
            }
        }
        
        $this->redirect('settings');
    }
}