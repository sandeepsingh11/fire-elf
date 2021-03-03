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
     * Get user's username
     * 
     * @param int $userId the user's id. If 0, get the user in session.
     * 
     * @return string the user's username, or '' if user not found
     */
    public function getUsername($userId = 0) {
        if ($userId == 0) {
            // get current user's id
            $userId = $this->Session->getUserId();
        }


        for ($i = 0; $i < sizeof($this->userList['user']); $i++) {
            if ($this->userList['user'][$i]['id'] == $userId) {
                // id match

                return $this->userList['user'][$i]['username'];
            }
        }

        return '';
    }



    /**
     * Get all users
     * 
     * @return array an array of users
     */
    public function getAllUsers() {
        $users = [];
        
        // get each user's values
        foreach ($this->userList['user'] as $user) {
            $user = array (
                'id' => $user['id'],
                'username' => $user['username']
            );
            array_push($users, $user);
        }

        return $users;
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
        // get next id
        $id = $this->getNextId();

        // hash password
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $newuser = array(
            'id' => $id,
            'username' => $username,
            'pazz' => $hashed
        );
        
        array_push($this->userList['user'], $newuser);





        // write updated json object
        if ($this->setUserList($this->userList)) {
            $this->Session->setSuccess('New user created!');

            header('Location: /login');
        }
        else {
            $this->Session->setError('New user could not be created, please try again', $username);

            header('Location: /register');
        }
    }



    /**
     * Get the next available user id
     * 
     * @return int the new id
     */
    public function getNextId() {
        $userLen = sizeof($this->userList['user']);

        $lastId = $this->userList['user'][$userLen - 1]['id'];

        return ($lastId + 1);
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

                    $this->Session->login($this->userList['user'][$i]['id']);
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
     * Log out of the current session
     */
    public function logout() {
        $this->Session->logout();
    }



    /**
     * Update the user's username.
     * 
     * @param int $userID the user's id
     * @param string $newUsername the new username passed
     * 
     * @return bool true on success, false on failure
     */
    public function updateUsername($userId, $newUsername) {
        for ($i = 0; $i < sizeof($this->userList['user']); $i++) {
            if ($this->userList['user'][$i]['id'] == $userId) {
                $this->userList['user'][$i]['username'] = $newUsername;

                // update json
                $this->setUserList($this->userList);

                return true;
            }
        }

        return false;
    }



    /**
     * Update the user's password.
     * 
     * @param int $userID the user's id
     * @param string $currentPw the current password passed
     * @param string $newPw the new password passed
     * 
     * @return bool true on success, false on failure
     */
    public function updatePassword($userId, $currentPw, $newPw) {
        for ($i = 0; $i < sizeof($this->userList['user']); $i++) {
            if ($this->userList['user'][$i]['id'] == $userId) {
                
                // check if current passwords match
                if (password_verify($currentPw, $this->userList['user'][$i]['pazz'])) {
                    // pass match

                    // hash new password
                    $hashed = password_hash($newPw, PASSWORD_DEFAULT);

                    $this->userList['user'][$i]['pazz'] = $hashed;

                    // update json
                    $this->setUserList($this->userList);

                    return true;
                }
                else {
                    // pass mismatch
                    return false;
                }
            }
        }

        return false;
    }



    /**
     * Delete a user.
     * 
     * @param int $userId ID of the user to be deleted
     * 
     * @return bool true on success, false on failure
     */
    public function deleteUser($userId) {
        for ($i = 0; $i < sizeof($this->userList['user']); $i++) {
            if ($this->userList['user'][$i]['id'] == $userId) {
                // delete entry from array
                array_splice($this->userList['user'], $i, 1);

                // update json
                $this->setUserList($this->userList);

                return true;
            }
        }

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