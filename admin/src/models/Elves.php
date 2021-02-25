<?php

class Elves  {

    private $elvesPath = __DIR__ . '/../elves.json';
    private $elvesList;
    private $Session;



    public function __construct()
    {
        $temp = file_get_contents($this->elvesPath);
        $this->elvesList = json_decode($temp, true);

        $this->Session = new Session();
    }



    public function getElvesList() {
        return $this->elvesList;
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
        $newElf_arr = array(
            'username' => $username,
            'pazz' => $password
        );

        array_push($this->elvesList['elves'], $newElf_arr);

        // write updated json object
        if ($this->setElvesList($this->elvesList)) {
            $this->Session->setSuccess('New elf created!');

            header('Location: /register');
        }
        else {
            $this->Session->setError('New elf could not be created, please try again', $username);

            header('Location: /register');
        }
    }



    /**
     * Login with the credentials provided
     * 
     * @param string $username the username
     * @param string $password the password
     * 
     * @return bool true if successful, false on failure
     */
    public function login($username, $password) {
        for ($i = 0; $i < sizeof($this->elvesList['elves']); $i++) {
            if ($this->elvesList['elves'][$i]['username'] == $username) {
                // username match

                if ($this->elvesList['elves'][$i]['pazz'] == $password) {
                    // pazz match

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
     * Write a new json string into 'elves.json'
     * 
     * @param string $newElvesList elvesList decoded json object
     * 
     * @return bool true if successful, false on failure
     */
    private function setElvesList($newElvesList) {
        $elvesList_json = json_encode($newElvesList, JSON_PRETTY_PRINT);
        
        // write to file
        if (file_put_contents($this->elvesPath, $elvesList_json)) {
            return true;
        }
        else {
            return false;
        }
    }
}