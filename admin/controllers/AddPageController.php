<?php

class AddPageController extends Controller {

    private $pages;

    function __construct() {
        $this->pages = new Pages();    
    }



    public function get() {
        // get pages' url parent dir
        $pageList = $this->pages->getPageList();
        $pageUrl_arr = [];

        foreach($pageList['pages'] as $page) {
            $pageName = $page['name'];
            $pageParentDir = $page['parent_dir'];
            $pageFile = rtrim($page['file'], '.php');
            
            $page = array(
                "name" => $pageName, 
                "dir" => $pageParentDir, 
                "file" => $pageFile
            );

            array_push($pageUrl_arr, $page);
        }

        include_once __DIR__ . '/../views/addPage.php';
    }



    /**
     * add the new page, from the quilljs wysiwyg
     * and update page_list.json
     */
    public function post() {
        // get values to write the new file
        $title = $_POST['title'];
        $dir = $_POST['dir'];
        $filename = $this->titleToFile($title);

        $dirLevels = 0;
        $dirPath = "";


        // set file path
        if ($dir == '/') {
            // if root dir
            $pagePath = '../' . CLIENT_PAGES_DIR . $filename . '.php';

            // add dir for page_list.json
            $dirPath .= "/";
        }
        else {
            // if not root dir

            // build path
            $pagePath = '../' . CLIENT_PAGES_DIR;
            

            // get dir segments / levels
            $dirSeg_arr = explode("/", $dir);

            // loop through each dir level
            for ($i = 0; $i < sizeof($dirSeg_arr); $i++) {
                if ($dirSeg_arr[$i] != "") {
                    // add dir for new page file
                    $pagePath .= $dirSeg_arr[$i] . "/";

                    // add dir for page_list.json
                    $dirPath .= $dirSeg_arr[$i] . "/";

                    // count dir level
                    $dirLevels++;
                }
            }
            

            // create new folder if non-existant
            mkdir($pagePath);
            
            // add file
            $pagePath .= $filename . '.php';
        }



        // get quilljs ops
        $ops = $_POST['ops-1'];
        $ops = json_decode($ops, true);
        
        $ops_arrJson = [];
        array_push($ops_arrJson, $ops);
        
        // get base64 image names
        $imgNames_str = "";
        if (isset($_POST["image-names"])) {
            $imgNames_str = $_POST["image-names"];
        }
        
        // convert quilljs base64 imgs into html img tags and store locally
        $ops_arrJson = $this->base64ToImage($ops_arrJson, $imgNames_str);
        


        // convert quill delta to html
        $lexer = new nadar\quill\Lexer($ops_arrJson[0]);
        $htmlContent = $lexer->render();
        
        // populate empty image alt attributes
        $htmlContent = $this->populateImgAlt($htmlContent);


        // prepare html body string to insert
        $dirRelPath = '\'../';
        for ($i = 1; $i <= $dirLevels; $i++) {
            $dirRelPath .= '../';
        }

        $headContent = '<?php
        $pageTitle = "' . $title . ' - ' . WEBSITE_NAME . '";
        require ' . $dirRelPath . 'comp/head.php\';
        require ' . $dirRelPath . 'comp/nav.php\';
        ?>';
        $bodyContent = '<fireelf data-id="1">' . $htmlContent . '</fireelf>';
        $footerContent = '<?php ' . $dirRelPath . 'comp/footer.php\';';

        $pageContent = $headContent . $bodyContent . $footerContent;



        // create view on the client side
        file_put_contents($pagePath, $pageContent);
        


        // set values for page_list.json
        $date = date('m-d-Y h:ia');

        $pageList = $this->pages->getPageList();
        $newPageInfo = array(
            'name' => $title, 
            'parent_dir' => $dirPath,
            'file' => $filename . '.php', 
            'updated_at' => $date
        );
        array_push($pageList['pages'], $newPageInfo);

        // call Model to write to page_list
        $this->pages->setPageList($pageList);


        header('Location: /pages');
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
     * make the title name into a file name
     * @param string $title
     * @return string
     */
    public function titleToFile($title) {
        $filename = '';

        // lowercase title
        $title = strtolower($title);

        // only pass alphanumeric and spaces
        // delete other chars
        $title = preg_replace('/[^a-z0-9 ]/', '', $title);

        // substitute " " into "-"
        $title_arr = explode(' ', $title);
        for ($i = 0; $i < sizeof($title_arr); $i++) {
            ($i != (sizeof($title_arr) - 1) ) 
                ? $filename .= "$title_arr[$i]-" 
                : $filename .= "$title_arr[$i]" ;
        }


        return $filename;
    }
}