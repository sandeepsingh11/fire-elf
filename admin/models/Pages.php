<?php

class Pages {

    private $pagesInfo_arr;
    private $pagesInfoPath = './pages_info.json';

    public function __construct()
    {
        $temp = file_get_contents($this->pagesInfoPath);
        $this->pagesInfo_arr = json_decode($temp, true);
    }



    public function getPagesInfo() {
        return $this->pagesInfo_arr;
    }



    /**
     * write a string into the pages info json object
     * @param string $pagesInfo
     */
    public function setPagesInfo($pagesInfo) {
        $pagesInfo_json = json_encode($pagesInfo, JSON_PRETTY_PRINT);
        file_put_contents($this->pagesInfoPath, $pagesInfo_json);
    }
}