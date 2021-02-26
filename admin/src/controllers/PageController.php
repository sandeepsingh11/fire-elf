<?php

class PageController extends Controller {

    private $pages;

    public $pageList;
    public $messages;
    public $pageId;
    public $pageName;
    public $pageDir;
    public $page_arr;
    public $quillBlock;



    function __construct($session) {
        parent::__construct($session);
        $this->pages = new Pages();    
    }



    /**
     * Display all pages
     */
    public function getAll() {
        // get all pages
        $this->page_arr = $this->pages->getAllPages();
        
        // inject css
        $css_arr = array(
            $this->getStylesheet('normalize'),
            $this->getStylesheet('main')

        );
        $this->css = $css_arr;
        
        // display the view
        $this->page_title = 'Pages';
        $this->messages = $this->Session->getAllMessages();
        $this->view('pages');
    }



    /**
     * Display the editor for adding a new page,
     * or editing an existing page (?id=title)
     */
    public function get() {
        // page values
        $this->pageId = -1; // -1 if creating a new page (no existing page id / name)
        $this->pageName = '';
        $this->pageDir = '';
        $this->quillBlock = '';


        // if page exists, get contents
        if (isset($_GET['id'])) {
            $this->pageId = $_GET['id'];

            // get the according page json info
            $this->pageList = $this->pages->getPageList();
            $filename = '';
            foreach ($this->pageList['pages'] as $page) {
                if ($_GET['id'] == $page['id']) {
                    $this->pageName = $page['name'];
                    $this->pageDir = $page['parent_dir'];
                    $filename = $page['file'];
                    break;
                }
            }


            
            // get entire page
            if ($this->pageDir == '/') {
                // if located in root dir
                $filePath = '../../' . CLIENT_PAGES_DIR . $filename;
            }
            else {
                // if located in non-root dir
                $filePath = '../../' . CLIENT_PAGES_DIR . $this->pageDir . $filename;
            }
            
            $pageContent = file_get_contents($filePath);



            // get <fireelf> content to edit
            $this->quillBlock = $this->pages->getFireelfContent($pageContent);
        }



        // get all pages' info (for the 'parent directory' dropdown in the editor)

        $this->pageList = $this->pages->getPageList();
        $this->pages_arr = [];


        // set default (first) select val
        $page = array(
            "dir" => '/',
            "file" => ''
        );

        array_push($this->pages_arr, $page);


        // loop to add each page's info
        foreach($this->pageList['pages'] as $page) {
            $parentDir = $page['parent_dir'];
            $file = rtrim($page['file'], '.php');
            
            $pageInfo = array(
                "dir" => $parentDir, 
                "file" => $file
            );


            // if existing page, override default (first) select val
            if ($page['id'] == $this->pageId) {
                $pageInfo = array( 
                    "dir" => '', 
                    "file" => $parentDir
                );

                array_unshift($this->pages_arr, $pageInfo);
            }
            else {
                array_push($this->pages_arr, $pageInfo);
            }
        }

        
        
        
        // inject css
        $css_arr = array(
            $this->getStylesheet('normalize'),
            'https://cdn.quilljs.com/1.3.6/quill.snow.css',
            $this->getStylesheet('main')

        );
        $this->css = $css_arr;
        
        
        
        // inject js
        $js_arr = array(
            'https://code.jquery.com/jquery-3.5.1.min.js',
            'https://cdn.quilljs.com/1.3.6/quill.min.js',
            'https://unpkg.com/quill-image-uploader@1.2.2/dist/quill.imageUploader.min.js',
            $this->getScript('quilljs-handler')
        );
        $this->js = $js_arr;
        
        
        
        // get view
        $this->page_title = 'Page Editor';
        $this->messages = $this->Session->getAllMessages();
        $this->view('page-editor');
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
            $this->pageId = $_POST['delete-id'];

            $this->pages->deletePage($this->pageId);
        }
        

        header('Location: /pages');
    }
}