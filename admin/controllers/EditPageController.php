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
            $pagesInfo = $this->pages->getPagesInfo();
            $targetFile = '';
            foreach ($pagesInfo['pages'] as $page) {
                if ($page['name'] == $_GET['id']) {
                    $targetFile = $page['file'];
                    break;
                }
            }


            // get page content
            $pageName = '../' . CLIENT_PAGES_DIR . $targetFile;
            $pageContent = file_get_contents($pageName, true);
        }



        include_once __DIR__ . '/../views/editPage.php';
    }


    /**
     * update the page's content from the summernote wysiwyg
     */
    public function post() {
        $newContent = $_POST['new-content'];
        $pagePath = $_POST['page'];

        file_put_contents($pagePath, $newContent);
    }
}