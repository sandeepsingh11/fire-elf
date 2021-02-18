<?php

class MediaController extends Controller {

    private $mediaList;
    private $Session;

    function __construct() {
        $this->mediaList = new Media();
        $this->Session = new Session();
    }



    public function get() {
        // get all media
        $mediaList = $this->mediaList->getMediaList();

        $messages_arr = $this->Session->getAllMessages();
        Controller::prettyPrint($messages_arr);

        include_once __DIR__ . '/../views/media.php';
    }


    public function post() {
        if (!isset($_FILES["media-upload"])) {
            // if nothing uploaded
            $this->Session->setError('You did not upload anything!');
            
            header('Location: /media-lib');
        }
        else {
            if ($_FILES["media-upload"]["error"] != 0) {
                // if error exists
                $uploadErrMessage = $this->mediaList->phpFileUploadErrors[$_FILES['media-upload']['error']];
                $this->Session->setError($uploadErrMessage);
                
                header('Location: /media-lib');
            }
            else {
                // get media object
                $uploadedMediaObj = $_FILES["media-upload"];
                

                // move media file to media folder
                $success = $this->mediaList->storeImage($uploadedMediaObj);
                if (!$success) {
                    $this->Session->setError('Image was not stored successfully. Please try again' . $uploadedMediaObj['size']);

                    header('Location: /media-lib');
                    exit();
                }

                
                // success! Redirect back to media page
                $this->Session->setSuccess('Media uploaded!');
                header('Location: /media-lib');
            }
        }
    }



    /**
     * handle media delete route
     */
    public function delete() {
        if (isset($_POST['delete'])) {
            $mediaId = $_POST['delete-id'];

            $this->mediaList->deleteMedia($mediaId);
        }
        

        header('Location: /media-lib');
    }
}