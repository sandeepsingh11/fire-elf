<?php

class Pages {

    private $pageList;
    private $pageListPath = './page_list.json';

    public function __construct()
    {
        $temp = file_get_contents($this->pageListPath);
        $this->pageList = json_decode($temp, true);
    }



    public function getPageList() {
        return $this->pageList;
    }



    /**
     * write a new json string into the 'page list' json object
     * @param string $newPageList
     */
    public function setPageList($newPageList) {
        $pageList_json = json_encode($newPageList, JSON_PRETTY_PRINT);
        file_put_contents($this->pageListPath, $pageList_json);
    }
}