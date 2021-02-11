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
                    $this->addMedia($mediaPath, $imgData);
    
    
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
     * add media
     * @param string $mediaPath
     * @param mixed $mediaData
     */
    private function addMedia($mediaPath, $mediaData) {
        // write img data to new file
        file_put_contents($mediaPath, $mediaData);
    }
    


    /**
     * write a new json string into the 'media list' json object
     * @param string $newMediaList
     */
    public function setMediaList($newMediaList) {
        $mediaList_json = json_encode($newMediaList, JSON_PRETTY_PRINT);
        file_put_contents($this->pageMediaPath, $mediaList_json);
    }
}