<?php

class User  {

    private $userPath = __DIR__ . '/../user.json';
    private $userList;
    private $Session;



    public function __construct()
    {
        $temp = file_get_contents($this->userPath);
        $this->userList = json_decode($temp, true);

        $this->Session = new Session();
    }



    public function getUserList() {
        return $this->userList;
    }



    /**
     * Register a new user
     * 
     * @param string $username the username
     * @param string $password the password
     * 
     * @return bool true if successful, false on failure
     */
    public function register($username, $password) {
        // hash password
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $newUser_arr = array(
            'username' => $username,
            'pazz' => $hashed
        );
        
        array_push($this->userList['user'], $newUser_arr);





        // write updated json object
        if ($this->setUserList($this->userList)) {
            $this->Session->setSuccess('New user created!');

            header('Location: /register');
        }
        else {
            $this->Session->setError('New user could not be created, please try again', $username);

            header('Location: /register');
        }
    }



    /**
     * Login with the credentials provided
     * 
     * @param string $username the username
     * @param string $password the password
     * @param string $csrf the csrf token
     * 
     * @return bool true if successful, false on failure
     */
    public function login($username, $password, $csrf) {
        for ($i = 0; $i < sizeof($this->userList['user']); $i++) {
            if ($this->userList['user'][$i]['username'] == $username) {
                // username match
                

                // verify csrf token
                if (!$this->Session->validateCSRF($csrf)) {
                    // token mismatch
                    $this->Session->setError('Security token invalid. Please try again.', [$username]);
                    header('Location: /login');
                    exit();
                }


                // verify pazz
                if (password_verify($password, $this->userList['user'][$i]['pazz'])) {
                    // pazz match

                    $this->Session->login();
                    $this->Session->setSuccess('Login successful!');

                    return true;
                }
                else {
                    $this->Session->setError('Username or password do not match.');

                    return false;
                }
            }
        }

        $this->Session->setError('Username or password do not match.');

        return false;
    }



    /**
     * Write a new json string into 'user.json'
     * 
     * @param string $newUserList userList decoded json object
     * 
     * @return bool true if successful, false on failure
     */
    private function setUserList($newUserList) {
        $userList_json = json_encode($newUserList, JSON_PRETTY_PRINT);
        
        // write to file
        if (file_put_contents($this->userPath, $userList_json)) {
            return true;
        }
        else {
            return false;
        }
    }
}