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
     * write a new json string into the 'media list' json object
     * @param string $newMediaList
     */
    public function setMediaList($newMediaList) {
        $mediaList_json = json_encode($newMediaList, JSON_PRETTY_PRINT);
        file_put_contents($this->pageMediaPath, $mediaList_json);
    }
}