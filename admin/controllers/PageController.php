<?php

class PageController extends Controller {

    private $pages;
    private $Session;

    function __construct() {
        $this->pages = new Pages();    
        $this->Session = new Session();    
    }



    /**
     * get all pages
     */
    public function getAll() {
        $pageList = $this->pages->getPageList();
        $pageList = $pageList['pages'];

        $messages_arr = $this->Session->getAllMessages();
        Controller::prettyPrint($messages_arr);
        
        include_once __DIR__ . '/../views/pages.php';
    }



    /**
     * display the editor for adding a new page,
     * or editing an existing page (?id=title)
     */
    public function get() {
        // page values
        $pageId = -1; // -1 if creating a new page (no existing page id / name)
        $pageName = '';
        $pageDir = '';
        $quillBlock = '';


        // if page exists, get contents
        if (isset($_GET['id'])) {
            $pageId = $_GET['id'];

            // get the according page json info
            $pageList = $this->pages->getPageList();
            $filename = '';
            foreach ($pageList['pages'] as $page) {
                if ($_GET['id'] == $page['id']) {
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
            $quillBlock = $this->pages->getFireelfContent($pageContent);
        }



        // get all pages' info (for the 'parent directory' dropdown in the editor)

        $pageList = $this->pages->getPageList();
        $pages_arr = [];


        // set default (first) select val
        $page = array(
            "dir" => '/',
            "file" => ''
        );

        array_push($pages_arr, $page);


        // loop to add each page's info
        foreach($pageList['pages'] as $page) {
            $parentDir = $page['parent_dir'];
            $file = rtrim($page['file'], '.php');
            
            $pageInfo = array(
                "dir" => $parentDir, 
                "file" => $file
            );


            // if existing page, override default (first) select val
            if ($page['id'] == $pageId) {
                $pageInfo = array( 
                    "dir" => '', 
                    "file" => $parentDir
                );

                array_unshift($pages_arr, $pageInfo);
            }
            else {
                array_push($pages_arr, $pageInfo);
            }
        }


        $messages_arr = $this->Session->getAllMessages();
        Controller::prettyPrint($messages_arr);

        include_once __DIR__ . '/../views/pageEditor.php';
    }



    /**
     * add the new page, from the quilljs wysiwyg
     * and update page_list.json
     */
    public function post() {
        // get values to write the new file
        $id = intval($_POST['id']);
        $title = $_POST['title'];
        $dir = $_POST['dir'];
        $imgNames_str = $_POST["image-names"];
        $ops = $_POST['ops'];

        // write page values
        $this->pages->setPage($id, $title, $dir, $imgNames_str, $ops);
    }



    /**
     * delete a page
     */
    public function delete() {
        if (isset($_POST['delete'])) {
            $pageId = $_POST['delete-id'];

            $this->pages->deletePage($pageId);
        }
        

        header('Location: /pages');
    }
}