<?php

class MediaController extends Controller {

    private $mediaList;

    public $message;
    public $media_arr;



    function __construct($session) {
        parent::__construct($session);
        $this->mediaList = new Media();
    }



    public function get() {
        // get all media
        $this->media_arr = $this->mediaList->getAllMedia();

        // inject css
        $css_arr = array(
            $this->getStylesheet('normalize'),
            $this->getStylesheet('main')

        );
        $this->css = $css_arr;

        $this->page_title = 'Media';
        $this->messages = $this->Session->getAllMessages();
        $this->view('media');
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

                    $errorMessage = 'Image was not stored successfully. Please try again';
                    $this->Session->setError($errorMessage);

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