<?php

class EditPageController extends Controller {

    private $pages;

    function __construct() {
        $this->pages = new Pages();    
    }



    public function get() {
        // get page contents
        if (isset($_GET['id'])) {
            // get according page file
            $pageList = $this->pages->getPageList();
            $filename = '';
            foreach ($pageList['pages'] as $page) {
                if ($page['name'] == $_GET['id']) {
                    $pageName = $page['name'];
                    $pageDir = $page['parent_dir'];
                    $filename = $page['file'];
                    break;
                }
            }

            
            // get entire page
            if ($pageDir == '/') {
                // if located in root dir
                $filePath = '../' . CLIENT_PAGES_DIR . $filename;
            }
            else {
                // if located in non-root dir
                $filePath = '../' . CLIENT_PAGES_DIR . $pageDir . $filename;
            }
            
            $pageContent = file_get_contents($filePath);


            // get <fireelf> content to edit
            $quillBlock_arr = [];
            $quillBlock_arr = $this->getFireelfContent($pageContent);
        }



        include_once __DIR__ . '/../views/editPage.php';
    }


    /**
     * update the page's content from the quilljs wysiwyg
     */
    public function post() {
        // get form values
        $pageName = $_POST['page'];
        
        // get all deltas and convert to json
        $ops_arrJson = [];
        $i = 1;
        while (isset($_POST["ops-$i"])) {
            $ops = $_POST["ops-$i"];
            
            $ops = json_decode($ops, true);
            array_push($ops_arrJson, $ops);
            
            $i++;
        }
        


        // get base64 image names
        $imgNames_str = "";
        if (isset($_POST["image-names"])) {
            $imgNames_str = $_POST["image-names"];
        }
        
        // convert quilljs base64 imgs into html img tags and store locally
        $ops_arrJson = $this->base64ToImage($ops_arrJson, $imgNames_str);


        // get file path and dir to update file
        $pageList = $this->pages->getPageList();
        $pagePath = '';
        foreach ($pageList['pages'] as $page) {
            if ($page['name'] == $pageName) {
                $filename = $page['file'];
                $dir = $page['parent_dir'];
                break;
            }
        }
        
        if ($dir == '/') {
            $pagePath = '../' . CLIENT_PAGES_DIR . $filename;
        }
        else {
            $pagePath = '../' . CLIENT_PAGES_DIR . $dir . $filename;
        }



        // convert json ops to html string
        $htmlContent = '';
        $newHtml_arr = [];
        foreach ($ops_arrJson as $ops) {
            $lexer = new nadar\quill\Lexer($ops);
            $htmlContent .= $lexer->render();

            array_push($newHtml_arr, $lexer->render());
        }

        // populate empty image alt attributes
        for ($i = 0; $i < sizeof($newHtml_arr); $i++) {
            $newHtml_arr[$i] = $this->populateImgAlt($newHtml_arr[$i]);
        }



        // update client side view with new content
        $this->setFireelfContent($pagePath, $newHtml_arr);



        // update 'updated_at' value
        $pageList = $this->pages->getPageList();
        for ($i = 0; $i < sizeof($pageList['pages']); $i++) {
            if ($pageList['pages'][$i]['name'] == $pageName) {
                
                $date = date('m-d-Y h:ia');
                $pageList['pages'][$i]['updated_at'] = $date;

                break;
            }
        }

        // call Model to write to page_list.json
        $this->pages->setPageList($pageList);


        header('Location: /pages');
    }


    
    /**
     * extract < fireelf > contents from an html file
     * @param string $pageContent
     * @return array
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


        return $quillBlock_arr;
    }


    /**
     * convert base64 image data into image data
     * to store locally and replace base64 with
     * html img tags
     * @param array $opsObj
     * @param string $imageNames
     * @return array
     */
    public function base64ToImage($opsObj, $imageNames) {
        $ops = $opsObj[0]["ops"];
        $imageNames_arr = explode(",", $imageNames);
        $j = 0;


        for ($i = sizeof($ops) - 1; $i >= 0; $i--) {

            // loop through each base64 image
            if ( gettype($ops[$i]["insert"]) == "array") {
                $base64_str = $ops[$i]["insert"]["image"];

                // [0] = "data:image/png;base64"
                // [1] = "iVBORw0KGgoAAAANSU..."
                $base64_arr = explode(",", $base64_str);


                // could be an existing html image or a base64 image
                // if base64, continue
                if (strpos($base64_arr[0], ";base64")) {
                    // get img extention. Trim out ";base64" for easy substr()
                    $base64_arr[0] = rtrim($base64_arr[0], ";base64");
                    $imgExt = substr($base64_arr[0], 11);
                    $imgData = base64_decode($base64_arr[1]);
    
    
                    // get image name
                    $imageName = $imageNames_arr[$j];
                    $imageName = explode(".", $imageName)[0];
                    $j++;
                    
    
                    // write img data to new file
                    file_put_contents('../' . MEDIA_DIR . $imageName . "." . $imgExt, $imgData);
    
    
                    // replace base64 with html img tag
                    $ops[$i]["insert"]["image"] = MEDIA_URL . $imageName . "." . $imgExt;
                    
                    // delete corresponding nadar\quill\Lexer imageBlob data
                    // (used for base64 images)
                    unset($ops[$i + 1]);
                }
            }
        }
        
        // re-index array from unsetting
        $ops = array_values($ops);
        
        $opsObj[0]["ops"] = $ops;
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
     * set < fireelf > contents for an html file
     * @param string $filepath
     * @param array $fireelfContents
     * @return void
     */
    public function setFireelfContent($filepath, $fireelfContents) {
        // get file content
        $pageContent = file_get_contents($filepath);

        // set content between <fireelf> tags
        $indexBegin = 0;
        $indexEnd = 0;

        // loop through each <fireelf> tag
        for ($i = 0; $i < sizeof($fireelfContents); $i++) {
            $indexBegin = strpos($pageContent, '<fireelf data-id="' . ($i + 1) . '">');
            $indexEnd = strpos($pageContent, '</fireelf>', $indexBegin);
            $len = $indexEnd - $indexBegin;

            
            // prepare string for insert
            $newContent = "<fireelf data-id=\"" . ($i + 1) . "\">" . $fireelfContents[$i];
            
            // replace old <fireelf> content with new content
            $pageContent = substr_replace($pageContent, $newContent, $indexBegin, $len);
        }

        
        // write to file
        file_put_contents($filepath, $pageContent);
    }
}