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

            // extract content between <fireelf> tags
            // use for multiple block editing
            $quillBlock_arr = [];
            $indexBegin = 0;
            $indexEnd = 0;

            while ($indexBegin = strpos($pageContent, '<fireelf>', $indexEnd)) {
                $indexEnd = strpos($pageContent, '</fireelf>', $indexBegin);
                $len = $indexEnd - $indexBegin;

                // extract string
                $blockContent = substr($pageContent, $indexBegin, $len);

                array_push($quillBlock_arr, $blockContent);
            }
        }



        include_once __DIR__ . '/../views/editPage.php';
    }


    /**
     * update the page's content from the summernote wysiwyg
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
        $pagePath = '../' . CLIENT_PAGES_DIR . $pageName . '.php';


        // convert json ops to html string
        $htmlContent = '';
        foreach ($ops_arrJson as $ops) {
            $lexer = new nadar\quill\Lexer($ops);
            $htmlContent .= $lexer->render();
        }



        // update client side view with new content
        file_put_contents($pagePath, $htmlContent);



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
        // $this->pages->setPageList($pageList);


        // header('Location: /pages');

    }
}