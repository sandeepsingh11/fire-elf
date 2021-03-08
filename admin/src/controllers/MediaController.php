<?php

class MediaController extends Controller {

    public $media_arr;



    public function __construct(...$models)
    {
        array_push($models, new Media());
        parent::__construct($models);

        // continue only if user is logged in
        $this->isLoggedIn();
    }



    public function get() {
        // get all media
        $this->media_arr = $this->Media->getAllMedia();

        // inject css
        $css_arr = array(
            $this->getStylesheet('normalize'),
            $this->getStylesheet('main')

        );
        $this->css = $css_arr;

        $this->page_title = 'Media';
        $this->view('media');
    }


    public function post() {
        if (!isset($_FILES["media-upload"])) {
            // if nothing uploaded
            $this->Session->setError('You did not upload anything!');
            
            $this->redirect('media-lib');
        }
        else {
            if ($_FILES["media-upload"]["error"] != 0) {
                // if error exists

                $uploadErrMessage = $this->Media->phpFileUploadErrors[$_FILES['media-upload']['error']];
                $this->Session->setError($uploadErrMessage);
                
                $this->redirect('media-lib');
            }
            else {
                // get media object
                $uploadedMediaObj = $_FILES["media-upload"];


                // check uploaded media type
                if (!$this->Media->fileTypeAllowed($uploadedMediaObj['tmp_name'])) {
                    
                    $errorMessage = 'That file type is not allowed. Please try again.';
                    $this->Session->setError($errorMessage);

                    $this->redirect('media-lib');
                    exit();
                }
                

                // move media file to media folder
                if (!$this->Media->storeImage($uploadedMediaObj)) {

                    $errorMessage = 'Image was not stored successfully. Please try again.';
                    $this->Session->setError($errorMessage);

                    $this->redirect('media-lib');
                    exit();
                }

                
                // success! Redirect back to media page
                $this->Session->setSuccess('Media uploaded!');
                $this->redirect('media-lib');
            }
        }
    }



    /**
     * handle media delete route
     */
    public function delete() {
        if (isset($_POST['delete'])) {
            $mediaId = $_POST['delete-id'];

            $this->Media->deleteMedia($mediaId);
        }
        

        header('Location: /media-lib');
    }
}