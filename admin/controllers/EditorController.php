<?php

class EditorController extends Controller {

    function __construct() {
        
    }

    public function get() {
        // get page contents
        if (isset($_GET['id'])) {
            $pageName = '../' . CLIENT_PAGES_DIR . $_GET['id'];
            $pageContent = file_get_contents($pageName, true);
        }



        include_once __DIR__ . '/../views/editor.php';
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