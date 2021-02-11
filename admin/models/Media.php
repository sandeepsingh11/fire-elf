<?php

class Media {

    private $mediaFilenameList;
    private $mediaList;
    private $pageMediaPath = './media_list.json';

    public function __construct()
    {
        // get all media content
        $this->mediaFilenameList = scandir('../' . MEDIA_DIR);

        // remove '.' and '..', then re-index
        unset($this->mediaFilenameList[0]);
        unset($this->mediaFilenameList[1]);
        $this->mediaFilenameList = array_values($this->mediaFilenameList);

        $temp = file_get_contents($this->pageMediaPath);
        $this->mediaList = json_decode($temp, true);
    }


    public function getMediaList() {
        // return $this->mediaFilenameList;
        return $this->mediaList;
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
            $mbSize = (MEDIA_SIZE_LIMIT / 1048576);
            echo "Your image cannot be larger than $mbSize MBs";
            // exit();
            return false;
        }

        return true;
    }



    /**
     * store an image locally that was uploaded from a form.
     * $uploadedMedia is the submitted image ($_FILES['image'])
     * @param array $uploadedMedia
     * @return boolean
     */
    public function storeImage($uploadedMedia) {
        $success = move_uploaded_file($uploadedMedia["tmp_name"], '../' . MEDIA_DIR . $uploadedMedia["name"]);
        if (!$success) {
            echo "Image was not stored successfully. Try again";
            // exit();
            return false;
        }

        // write to media json
        $this->setMediaList($uploadedMedia["name"]);

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
            if ( gettype($ops[$i]["insert"]) == "array") {
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
                    $mediaPath = '../' . MEDIA_DIR . $imageName . "." . $imgExt;
                    $this->writeMedia($mediaPath, $imgData, $imageName);
    
    
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

        // write to media json
        $this->setMediaList($imageName);
    }
    


    /**
     * prep and write a new json string into the 'media list' json object
     * @param string $imageName
     */
    public function setMediaList($mediaName) {
        $date = date('m-d-Y h:ia');

        $newMediaInfo = array(
            'name' => $mediaName, 
            'uploaded_at' => $date
        );

        $mediaList = $this->getMediaList();
        array_push($mediaList['media'], $newMediaInfo);


        $mediaList_json = json_encode($mediaList, JSON_PRETTY_PRINT);
        file_put_contents($this->pageMediaPath, $mediaList_json);
    }
}