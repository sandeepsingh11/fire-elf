<?php

class MediaController extends Controller {

    private $mediaList;

    function __construct() {
        $this->mediaList = new Media();    
    }



    public function get() {
        // get all media
        $mediaList = $this->mediaList->getMediaList();

        include_once __DIR__ . '/../views/media.php';
    }


    public function post() {
        if (!isset($_FILES["media-upload"])) {
            // if nothing uploaded
            echo "You did not upload anything!";
            exit();
            // redirect
        }
        else {
            if ($_FILES["media-upload"]["error"]) {
                // if error exists
                echo 'Error: ' . $_FILES["media-upload"]["error"];
                exit();
            }
            else {
                // get media object
                $uploadedMediaObj = $_FILES["media-upload"];

                // check media size limit
                $success = $this->mediaList->checkImageSize($uploadedMediaObj);
                if (!$success) {
                    echo "Exeeded image size limit!";
                    exit();
                }

                // move media file to media folder
                $success = $this->mediaList->storeImage($uploadedMediaObj);
                if (!$success) {
                    echo "Image was not stored successfully. Try again";
                    exit();
                }

                
                // success! Redirect back to media page
                header('Location: /media-lib');
            }
        }
    }
}