<?php

class Session extends Model {

    /**
     * Session model structure
     * ['error'] :bool
     * - ['error_type'] :string
     * - ['error_message'] :string
     * - ['error_values'] :array
     * 
     * ['notice'] :bool
     * - ['notice_message'] :string
     * 
     * ['success'] :bool
     * - ['success_message'] :string
     */

    public function __construct()
    {
        if (!isset($_SESSION)) {
            session_start();

            if (!isset($_SESSION['error'])) {
                $_SESSION['error'] = false;
            }

            if (!isset($_SESSION['notice'])) {
                $_SESSION['notice'] = false;
            }

            if (!isset($_SESSION['success'])) {
                $_SESSION['success'] = false;
            }
        }
    }





    /**
     * check if an error exists
     * @return bool
     */
    public function errorExists() {
        if ($_SESSION['error'] == true) {
            return true;
        }
        else {
            return false;
        }
    }



    /**
     * get the error message.
     * Clear session if $clearSession = true
     * @param bool $clearSession
     * @return string
     */
    public function getError($clearSession = false) {
        $errorMessage = '';

        // get error message
        if ($this->errorExists()) {
            $startTag = '<div class="error-message">';
            $endTag = '</div>';
            $errorMessage = $startTag . $_SESSION['error_message'] . $endTag;
        }

        // clear session
        if ($clearSession) {
            $this->clearSession();
        }


        return $errorMessage;
    }



    /**
     * set an error message to the session
     * @param string $errorMessage
     * @param array $errorValues
     */
    public function setError($errorMessage, $errorValues = '') {
        $_SESSION['error'] = true;
        $_SESSION['error_message'] = $errorMessage;

        if ($errorValues != '') {
            $_SESSION['error_values'] = $errorValues;
        }
    }



    /**
     * check if a notice exists
     * @return bool
     */
    public function noticeExists() {
        if ($_SESSION['notice'] === true) {
            return true;
        }
        else {
            return false;
        }
    }



    /**
     * get the notice message.
     * Clear session if $clearSession = true
     * @param bool $clearSession
     * @return string
     */
    public function getNotice($clearSession = false) {
        $noticeMessage = '';

        // get success message
        if ($this->noticeExists()) {
            $startTag = '<div class="notice-message">';
            $endTag = '</div>';
            $noticeMessage = $startTag . $_SESSION['notice_message'] . $endTag;
        }

        // clear session
        if ($clearSession) {
            $this->clearSession();
        }
        

        return $noticeMessage;
    }



    /**
     * set a notice message to the session
     * @param string $successMessage
     */
    public function setNotice($noticeMessage) {
        $_SESSION['notice'] = true;
        $_SESSION['notice_message'] = $noticeMessage;
    }



    /**
     * check if a success exists
     * @return bool
     */
    public function successExists() {
        if ($_SESSION['success'] == true) {
            return true;
        }
        else {
            return false;
        }
    }



    /**
     * get the success message.
     * Clear session if $clearSession = true
     * @param bool $clearSession
     * @return string
     */
    public function getSuccess($clearSession = false) {
        $successMessage = '';

        // get success message
        if ($this->successExists()) {
            $startTag = '<div class="success-message">';
            $endTag = '</div>';
            $successMessage = $startTag . $_SESSION['success_message'] . $endTag;
        }

        // clear session
        if ($clearSession) {
            $this->clearSession();
        }
        

        return $successMessage;
    }



    /**
     * set a success message to the session
     * @param string $successMessage
     */
    public function setSuccess($successMessage) {
        $_SESSION['success'] = true;
        $_SESSION['success_message'] = $successMessage;
    }



    /**
     * returns an array containing the messages for error, notice, and success.
     * Clears all session data, unless $clearSession is false
     * @param bool $clearSession
     * @return array
     */
    public function getAllMessages($clearSession = true) {
        $errorMessage = '';
        $noticeMessage = '';
        $successMessage = '';

        // get each message
        $errorMessage = $this->getError();
        $noticeMessage = $this->getNotice();
        $successMessage = $this->getSuccess();


        // save to array to return
        $message_arr = array(
            'error' => $errorMessage,
            'notice' => $noticeMessage,
            'success' => $successMessage
        );


        // clear session
        if ($clearSession) {
            $this->clearSession();
        }

        return $message_arr;
    }



    /**
     * delete all session data. 
     * If session type specified ['error', 'notice', 'success'], 
     * delete only that type of session data
     * @param string $sessionType
     */
    public function clearSession($sessionType = '') {
        if ($sessionType == '') {
            $_SESSION['error'] = false;
            unset($_SESSION['error_message']);
            unset($_SESSION['error_values']);

            $_SESSION['notice'] = false;
            unset($_SESSION['notice_message']);

            $_SESSION['success'] = false;
            unset($_SESSION['success_message']);

            session_destroy();
        }
        else {
            switch ($sessionType) {
                case 'error':
                    $_SESSION['error'] = false;
                    unset($_SESSION['error_message']);
                    unset($_SESSION['error_values']);
                    break;

                case 'notice':
                    $_SESSION['notice'] = false;
                    unset($_SESSION['notice_message']);
                    break;

                case 'success':
                    $_SESSION['success'] = false;
                    unset($_SESSION['success_message']);
                    break;
            }
        }
    }
}