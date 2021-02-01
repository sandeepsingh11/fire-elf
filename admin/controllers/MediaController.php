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
                if ($_FILES['media-upload']['size'] > MEDIA_SIZE_LIMIT) { 
                    // can't be larger than xx MB
                    $mbSize = (MEDIA_SIZE_LIMIT / 1048576);
                    echo "Your image cannot be larger than $mbSize MBs";
                    exit();
                }

                // move media file to media folder
                $success = move_uploaded_file($uploadedMediaObj["tmp_name"], '../' . MEDIA_DIR . $uploadedMediaObj["name"]);
                if (!$success) {
                    echo "Image was not stored successfully. Try again";
                    exit();
                }
                else {
                    // set values for media_list.json
                    $date = date('m-d-Y h:ia');
    
                    $newMediaInfo = array(
                        'name' => $uploadedMediaObj["name"], 
                        'uploaded_at' => $date
                    );
    
                    $mediaList = $this->mediaList->getMediaList();
                    array_push($mediaList['media'], $newMediaInfo);
                    
                    // write new json
                    $this->mediaList->setMediaList($mediaList);
    
                    
                    // success! Redirect back to media page
                    header('Location: /media-lib');
                }
            }
        }
    }



    /**
     * Pretty print PHP array
     * @param array $array
     */
    public function prettyPrint($array) {
        echo '<pre>'.print_r($array, true).'</pre>';
    }
}