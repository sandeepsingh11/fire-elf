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



    /**
     * Rearrage $_FILES array from input's multiple upload option.
     * Code snippet from: 
     * https://www.php.net/manual/en/features.file-upload.multiple.php#106565
     * 
     * @param array $arr $_FILES input array
     * 
     * @return array rearranged input array
     */
    function rearrange( $arr ) {
        foreach( $arr as $key => $all ){
            foreach( $all as $i => $val ){
                $new[$i][$key] = $val;   
            }   
        }

        return $new;
    }
    


    public function post() {
        // rearrange $_FILES array
        $files = $this->rearrange($_FILES["media-upload"]);
        

        // check if user uploaded a file
        if ($files[0]['error'] == 4) {
            $errorMessage = $this->Media->uploadErrors[$files[0]['error']];
            $this->Session->setError($errorMessage);
            
            $this->redirect('media-lib');
        }


        // loop through each uploaded file
        foreach ($files as $file) {
            if ($file["error"] != 0) {
                // if error exists
    
                $errorMessage = $file['name'] . ': ' . $this->Media->uploadErrors[$file['error']];
                $this->Session->setError($errorMessage);
                
                $this->redirect('media-lib');
                exit();
            }
            else {
                // no errors, continue

                // check uploaded media type
                if (!$this->Media->fileTypeAllowed($file['tmp_name'])) {
                    
                    $errorMessage = $file['name'] . ': That file type is not allowed. Please try again.';
                    $this->Session->setError($errorMessage);
    
                    $this->redirect('media-lib');
                    exit();
                }
                
    
                // move media file to media folder
                if (!$this->Media->storeImage($file)) {
    
                    $errorMessage = $file['name'] . ': Image was not stored successfully. Please try again.';
                    $this->Session->setError($errorMessage);
    
                    $this->redirect('media-lib');
                    exit();
                }
            }
        }


        // success! Redirect back to media page
        $this->Session->setSuccess('Media uploaded!');
        $this->redirect('media-lib');
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