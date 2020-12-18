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
                    $filename = $page['file'];
                    break;
                }
            }


            // get entire page
            $filePath = '../' . CLIENT_PAGES_DIR . $filename;
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
        
        

        // get file path to update file
        // get according page file
        $pageList = $this->pages->getPageList();
        $pagePath = '';
        foreach ($pageList['pages'] as $page) {
            if ($page['name'] == $pageName) {
                $filename = $page['file'];
                break;
            }
        }
        $pagePath = '../' . CLIENT_PAGES_DIR . $filename;


        // convert json ops to html string
        $htmlContent = '';
        $newHtml_arr = [];
        foreach ($ops_arrJson as $ops) {
            $lexer = new nadar\quill\Lexer($ops);
            $htmlContent .= $lexer->render();

            array_push($newHtml_arr, $lexer->render());
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