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


            // get page content
            $filePath = '../' . CLIENT_PAGES_DIR . $filename;
            $pageContent = file_get_contents($filePath);
        }



        include_once __DIR__ . '/../views/editPage.php';
    }


    /**
     * update the page's content from the summernote wysiwyg
     */
    public function post() {
        // get updated content
        $newContent = $_POST['new-content'];
        $pageName = $_POST['page'];
        $pagePath = '../' . CLIENT_PAGES_DIR . $pageName . '.php';

        // update view on the client side
        file_put_contents($pagePath, $newContent);


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