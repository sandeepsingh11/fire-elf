<?php

class Page extends Model {

    private $pageList;
    private $pageListPath = __DIR__ . '/../page_list.json';
    private $Session;

    public function __construct()
    {
        $temp = file_get_contents($this->pageListPath);
        $this->pageList = json_decode($temp, true);

        $this->Session = new Session();
    }



    public function getPageList() {
        return $this->pageList;
    }



    /**
     * Get all pages from page_list.json
     * @return array
     */
    public function getAllPages() {
        return $this->pageList['pages'];
    }



    /**
     * set / update page content
     * @param integer $id
     * @param string $title
     * @param string $dir
     * @param array $imgNames
     * @param string $ops
     */
    public function setPage($id, $title, $dir, $imgNames, $ops) {
        // set 'create' or 'update' mode
        if ($id == -1) {
            $create = true;
        }
        else {
            $create = false;
        }



        //
        // ─── BUILD PAGE FILEPATH ─────────────────────────────────────────
        //

        
        // prepare filename
        if ($create) {
            $filename = $this->prepFilename($title);
            $filename .= '.php';
        }
        else {
            $pageList = $this->getPageList();
            foreach ($pageList['pages'] as $page) {
                if ($page['name'] == $title) {
                    $filename = $page['file'];
                    break;
                }
            }
        }


        // prep filepath vals
        $dirLevels = 0;
        $dirPath = "";

        // set file path
        if ($dir == '/') {
            // if root dir

            // build path
            $pagePath = __DIR__ . '/../../../' . CLIENT_PAGES_DIR . $filename;

            // add page_list.json dir val
            $dirPath .= "/";
        }
        else {
            // if not root dir

            // build path
            $pagePath = __DIR__ . '/../../../' . CLIENT_PAGES_DIR;
            

            // get dir segments / levels
            $dirSeg_arr = explode("/", $dir);

            // loop through each dir level
            for ($i = 0; $i < sizeof($dirSeg_arr); $i++) {
                if ($dirSeg_arr[$i] != "") {
                    // add dir for new page file
                    $pagePath .= $dirSeg_arr[$i] . "/";

                    // add page_list.json dir val
                    $dirPath .= $dirSeg_arr[$i] . "/";

                    // count dir level
                    $dirLevels++;
                }
            }
            

            // make new folder if nonexistant
            if ($create) {
                if (!is_dir($pagePath)) {
                    // create new folder
                    mkdir($pagePath);
                }
            }
            
            // add filename to path
            $pagePath .= $filename;
        }

        // ─────────────────────────────────────────────────────────────────






        // prep quilljs ops
        $ops = json_decode($ops, true);

        
        // convert quilljs base64 imgs into html img tags and store locally
        $Media = new Media();
        $ops = $Media->base64ToImage($ops, $imgNames);


        // convert quill delta to html
        $lexer = new nadar\quill\Lexer($ops);
        $htmlContent = $lexer->render();
        

        // populate empty image alt attributes
        $htmlContent = $Media->populateImgAlt($htmlContent);





        // write html content
        if ($create) {
            // prepare html body string to insert
            $dirRelPath = '__dir__ . \'/../';
            for ($i = 1; $i <= $dirLevels; $i++) {
                $dirRelPath .= '../';
            }
    
            $headContent = '<?php
            $pageTitle = "' . $title . ' - ' . WEBSITE_NAME . '";
            require ' . $dirRelPath . 'comp/head.php\';
            require ' . $dirRelPath . 'comp/nav.php\';
            ?>';
            $bodyContent = '<fireelf data-id="1">' . $htmlContent . '</fireelf>';
            $footerContent = '<?php require ' . $dirRelPath . 'comp/footer.php\';';
    
            $pageContent = $headContent . $bodyContent . $footerContent;
    
    
            // create / write file on the client side
            file_put_contents($pagePath, $pageContent);
        }
        else {
            // update client side view with new content
            $this->setFireelfContent($pagePath, $htmlContent);
        }





        // prep to write to page_list.json
        if ($create) {
            // set values for page_list.json
            $date = date('m-d-Y h:ia');

            $id = $this->nextPageId();
    
            $pageList = $this->getPageList();
            $newPageInfo = array(
                'id' => $id,
                'name' => $title, 
                'parent_dir' => $dirPath,
                'file' => $filename, 
                'created_at' => $date,
                'updated_at' => ''
            );

            array_push($pageList['pages'], $newPageInfo);
        }
        else {
            // update 'updated_at' value
            $pageList = $this->getPageList();
            for ($i = 0; $i < sizeof($pageList['pages']); $i++) {
                if ($pageList['pages'][$i]['id'] == $id) {
                    
                    // created date
                    $creationDate = $pageList['pages'][$i]['created_at'];

                    // updated (current) date
                    $date = date('m-d-Y h:ia');
                    
                    $newPageInfo = array(
                        'id' => $id,
                        'name' => $title, 
                        'parent_dir' => $dirPath, /////////////////// change client side
                        'file' => $filename, 
                        'created_at' => $creationDate,
                        'updated_at' => $date
                    );
                    

                    $pageList['pages'][$i] = $newPageInfo;
                    break;
                }
            }
        }

        // write to page_list
        $this->setPageList($pageList);


        // set success message
        if ($create) {
            $successMessage = 'New page created!';
        }
        else {
            $successMessage = 'Page updated!';
        }
        $this->Session->setSuccess($successMessage);




        header('Location: /pages');
    }



    
    
    
    
    
    
    
    /**
     * extract < fireelf > contents from an html file
     * @param string $pageContent
     * @return string
     */
    public function getFireelfContent($pageContent) {
        // extract content between <fireelf> tags
        // use for multiple block editing
        $quillBlock_arr = [];
        $indexBegin = 0;
        $indexEnd = 0;
        $i = 1;

        // ***NOTE*** starting at index 0 of $pageContent will not work; this method
        // has funky behaviour for the 0th index for some reason. This is mentioned below:
        // https://www.php.net/manual/en/function.strpos.php
        while ($indexBegin = strpos($pageContent, '<fireelf data-id="' . $i . '">', $indexEnd)) {
            $indexEnd = strpos($pageContent, '</fireelf>', $indexBegin);
            $len = $indexEnd - $indexBegin;

            // extract string
            $blockContent = substr($pageContent, $indexBegin, $len);

            array_push($quillBlock_arr, $blockContent);

            $i++;
        }


        return $quillBlock_arr[0];
    }



    /**
     * update < fireelf > contents for an html file
     * @param string $filepath
     * @param string $fireelfContent
     * @return void
     */
    public function setFireelfContent($filepath, $fireelfContent) {
        // get file content
        $pageContent = file_get_contents($filepath);

        // set content between <fireelf> tags
        $indexBegin = 0;
        $indexEnd = 0;

        
        $indexBegin = strpos($pageContent, '<fireelf data-id="1">');
        $indexEnd = strpos($pageContent, '</fireelf>', $indexBegin);
        $len = $indexEnd - $indexBegin;

        
        // prepare string for insert
        $newContent = '<fireelf data-id="1">' . $fireelfContent;
        
        // replace old <fireelf> content with new content
        $pageContent = substr_replace($pageContent, $newContent, $indexBegin, $len);
        

        
        // write to file
        file_put_contents($filepath, $pageContent);
    }



    /**
     * delete a page
     * @param int $pageId page id
     * @return bool true if successful, false if fails
     */
    public function deletePage($pageId) {
        if ($this->pageExists($pageId)) {
            // if page does exist

            $pageList = $this->getPageList();
    

            //
            // ─── DELETE ENTRY FROM PAGE_LIST.json ─────────────────────────────────
            //
    
            // loop through each page
            for ($i = 0; $i < sizeof($pageList['pages']); $i++) {
                if ($pageList['pages'][$i]['id'] == $pageId) {
                    
                    // get dir level
                    $dirLevel = $pageList['pages'][$i]['parent_dir'];
    
                    // get file name
                    $filename = $pageList['pages'][$i]['file'];
    
                    // delete entry from array
                    array_splice($pageList['pages'], $i, 1);

                    // update page_list.json
                    $this->setPageList($pageList);

                    break;
                }
            }
            
            // ─────────────────────────────────────────────────────────────────
    
    
    
    
    
            //
            // ─── DELETE FILE ON CLIENT SIDE ──────────────────────────────────
            //
    
            // if file is at root, dont add it to the file path
            if ($dirLevel == '/') {
                $dirLevel = '';
            }
    
            // delete file
            unlink(__DIR__ . '/../../../' . CLIENT_PAGES_DIR . $dirLevel . $filename);

            // ─────────────────────────────────────────────────────────────────
    


            $this->Session->setSuccess('Page deletion successful');

            return true;
        }
        else {
            // if page does not exist

            $this->Session->setError('Page not found...');

            return false;
        }
    }



    /**
     * find the next page id to use from page_list.json
     * @return integer
     */
    private function nextPageId() {
        $pageLen = sizeof($this->pageList['pages']);

        $lastId = $this->pageList['pages'][$pageLen - 1]['id'];

        return ($lastId + 1);
    }



    /**
     * check if page exists
     * @param int $pageId
     * @return bool
     */
    public function pageExists($pageId) {
        $pageList = $this->getPageList();

        // loop through each page
        foreach ($pageList['pages'] as $page) {
            if ($page['id'] == $pageId) {
                return true;
            }
        }

        return false;
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