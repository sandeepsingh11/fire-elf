<?php

class Media {

    private $mediaFilenameList;
    private $mediaList;
    private $mediaPath = __DIR__ . '/../media_list.json';
    private $Session;
    public $phpFileUploadErrors;

    public function __construct()
    {
        $temp = file_get_contents($this->mediaPath);
        $this->mediaList = json_decode($temp, true);

        

        // $_FILE error codes
        // https://www.php.net/manual/en/features.file-upload.errors.php
        $this->phpFileUploadErrors = array(
            0 => 'There is no error, the file uploaded with success',
            1 => 'The uploaded file exceeds ' . $this->getMediaSizeLimit() . ' MBs',
            2 => 'The uploaded file exceeds ' . $this->getMediaSizeLimit() . ' MBs',
            // 2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
            3 => 'The uploaded file was only partially uploaded',
            4 => 'No file was uploaded',
            6 => 'Missing a temporary folder',
            // 6 => 'Missing a temporary folder',
            7 => 'Failed to store image, try again.',
            // 7 => 'Failed to write file to disk.',
            8 => 'Internal error, try again.',
            // 8 => 'A PHP extension stopped the file upload.',
        );


        $this->Session = new Session();
    }



    public function getMediaList() {
        return $this->mediaList;
    }



    /**
     * get all media from media_list.json
     * @return array
     */
    public function getAllMedia() {
        return $this->mediaList['media'];
    }



    /**
     * validate the size of the uploading image.
     * $uploadedMedia is the submitted image ($_FILES['image'])
     * @param array $uploadedMedia
     * @return boolean
     */
    public function checkImageSize($uploadedMedia) {
        if ($uploadedMedia['size'] > MEDIA_SIZE_LIMIT) { 
            // can't be larger than xx MB
            
            return false;
        }

        return true;
    }



    /**
     * get media size limit
     * @return int
     */
    public function getMediaSizeLimit() {
        return (MEDIA_SIZE_LIMIT / 1048576);
    }



    /**
     * Check uploaded media's type
     * 
     * @param string $filename name of the uploaded file
     * 
     * @return bool true if file type is allowed, false if not allowed
     */
    public function fileTypeAllowed($filename) {
        // get uploaded file type
        $uploadedFileType = exif_imagetype($filename);

        // check (compare) uploaded file type with allowed types
        if (in_array($uploadedFileType, ALLOWED_MEDIA_TYPES)) {
            return true;
        }
        else {
            return false;
        }
    }



    /**
     * store an image locally that was uploaded from a form.
     * $uploadedMedia is the submitted image ($_FILES['image'])
     * @param array $uploadedMedia
     * @return boolean
     */
    public function storeImage($uploadedMedia) {
        $success = move_uploaded_file($uploadedMedia["tmp_name"], __DIR__ . '/../../../' . MEDIA_DIR . $uploadedMedia["name"]);
        if (!$success) {
            return false;
        }

        // prep json values
        $date = date('m-d-Y h:ia');

        $newMediaInfo = array(
            'name' => $uploadedMedia['name'], 
            'uploaded_at' => $date
        );

        $mediaList = $this->getMediaList();
        array_push($mediaList['media'], $newMediaInfo);

        // write to media json
        $this->setMediaList($mediaList);

        return true;
    }



    /**
     * convert base64 image data into image data
     * to store locally, and replace base64 with
     * html img tags, under the context of 
     * nadar\quill\Lexer ops structs
     * @param array $opsObj
     * @param string $imageNames
     * @return array
     */
    public function base64ToImage($opsObj, $imageNames) {
        $ops = $opsObj["ops"];
        $imageNames_arr = explode(",", $imageNames);
        $j = 0;


        for ($i = sizeof($ops) - 1; $i >= 0; $i--) {

            // loop through each base64 image
            if (gettype($ops[$i]["insert"]) == "array") {
                $base64_str = $ops[$i]["insert"]["image"];
                
                if (strpos($base64_str, 'base64')) {
                    // if image is base64

                    // [0] = "data:image/png;base64"
                    // [1] = "iVBORw0KGgoAAAANSU..."
                    $base64_arr = explode(",", $base64_str);
                    
    
                    // get img extention. Trim out ";base64" for easy substr()
                    $base64_arr[0] = rtrim($base64_arr[0], ";base64");
                    $imgExt = substr($base64_arr[0], 11);
                    $imgData = base64_decode($base64_arr[1]);
    
    
                    // get image name
                    $imageName = $imageNames_arr[$j];
                    $imageName = explode(".", $imageName)[0];
                    $j++;
                    
    
                    // write img data to new file
                    $mediaPath = __DIR__ . '/../../../' . MEDIA_DIR . $imageName . "." . $imgExt;
                    $this->writeMedia($mediaPath, $imgData, $imageName . '.' . $imgExt);
    
    
                    // replace base64 value with html img value
                    $ops[$i]["insert"]["image"] = MEDIA_URL . $imageName . "." . $imgExt;
                    
                    // delete corresponding nadar\quill\Lexer imageBlob data
                    // (used for base64 images)
                    unset($ops[$i + 1]);
                }
            }
        }
        
        // re-index array from unsetting
        $ops = array_values($ops);
        
        $opsObj["ops"] = $ops;
        return $opsObj;
    }



    /**
     * Populate empty html image alts
     * @param string $htmlStr
     * @return string
     */
    public function populateImgAlt($htmlStr) {
        $imgIndexBegin = 0;
        $imgIndexEnd = 0;
        $imgLen = 0;
        
        // loop through each <img> tag
        while ($imgIndexBegin = strpos($htmlStr, '<img', $imgIndexEnd)) {
            $imgIndexEnd = strpos($htmlStr, '>', $imgIndexBegin);
            $imgIndexEnd++;

            // ... <img ... > ...
            $imgLen = $imgIndexEnd - $imgIndexBegin;
            $imgSubstr = substr($htmlStr, $imgIndexBegin, $imgLen);

            
            // if <img> contains empty alt attribute, populate it
            if (strpos($imgSubstr, 'alt=""')) {
                // get src value (link) segment
                $srcIndexBegin = strpos($imgSubstr, 'src="');
                $srcIndexEnd = strpos($imgSubstr, 'alt', $srcIndexBegin);
                $srcLen = $srcIndexEnd - $srcIndexBegin;

                // src attribute string
                $srcSubstr = substr($imgSubstr, $srcIndexBegin, $srcLen);

                // isolate the image filename from the src
                // $srcSeg_arr[last index] = xxx.png" 
                $srcSeg_arr = explode("/", $srcSubstr);
                $imageName = array_pop($srcSeg_arr);

                // [0] = xxx
                // [1] = png
                $imageName = explode(".", $imageName);

                // populate alt attr
                $imgSubstr = str_replace('alt=""', 'alt="' . $imageName[0] . '"', $imgSubstr);

                // update html string with the new image tag
                $htmlStr = substr_replace($htmlStr, $imgSubstr, $imgIndexBegin, $imgLen);
            }
        }


        return $htmlStr;
    }



    /**
     * write media and store locally, update media json
     * @param string $mediaPath
     * @param mixed $mediaData
     * @param string $imageName
     */
    private function writeMedia($mediaPath, $mediaData, $imageName) {
        // write img data to new file
        file_put_contents($mediaPath, $mediaData);



        // prep json values
        $date = date('m-d-Y h:ia');

        $newMediaInfo = array(
            'name' => $imageName, 
            'uploaded_at' => $date
        );

        $mediaList = $this->getMediaList();        
        array_push($mediaList['media'], $newMediaInfo);

        // write to media json
        $this->setMediaList($mediaList);
    }



    /**
     * Check if the specified media exists
     * @param string $mediaName name of the media file
     * @return bool true if media exists, false if it does not exist
     */
    public function mediaExists($mediaName) {
        $mediaList = $this->getMediaList();

        // loop through each media entry
        foreach ($mediaList['media'] as $media) {
            if ($media['name'] == $mediaName) {
                return true;
            }
        }

        return false;
    }
    


    /**
     * write to the 'media list' json object
     * @param array $mediaList media_list.json var
     */
    public function setMediaList($mediaList) {
        $mediaList_json = json_encode($mediaList, JSON_PRETTY_PRINT);
        file_put_contents($this->mediaPath, $mediaList_json);
    }



    /**
     * delete a media entry from local storage and media_list.json
     * @param string $mediaName media name (filename)
     * @return bool true if successful, false if fails
     */
    public function deleteMedia($mediaName) {
        if ($this->mediaExists($mediaName)) {
            // if media exists

            $mediaList = $this->getMediaList();
    

            //
            // ─── DELETE ENTRY FROM MEDIA_LIST.json ─────────────────────────────────
            //
    
            // loop through each media
            for ($i = 0; $i < sizeof($mediaList['media']); $i++) {
                if ($mediaList['media'][$i]['name'] == $mediaName) {
                    
                    // delete entry from array
                    array_splice($mediaList['media'], $i, 1);
                    
                    // update media_list.json
                    $this->setMediaList($mediaList);

                    break;
                }
            }
            
            // ─────────────────────────────────────────────────────────────────
    
    
    
    
    
            //
            // ─── DELETE FILE ON CLIENT SIDE ──────────────────────────────────
            //
    
            // delete file
            unlink(__DIR__ . '/../../../' . MEDIA_DIR . $mediaName);

            // ─────────────────────────────────────────────────────────────────
    


            $this->Session->setSuccess('Media deletion successful.');

            return true;
        }
        else {
            // if media does not exist

            $this->Session->setError('Media not found. Deletion failed.');
        }
    }
}