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


        // set file path
        // if root dir
        if ($dir == '/') {
            $pagePath = '../' . CLIENT_PAGES_DIR . $filename . '.php';
        }
        // if not root dir
        else { 
            $dirSeg_arr = explode('/', $dir);

            // build filepath
            $pagePath = '../' . CLIENT_PAGES_DIR;
            for ($i = 1; $i < sizeof($dirSeg_arr); $i++) {
                $pagePath .= $dirSeg_arr[$i] . '/';

                $dirLevels++;
            }

            // create new folder if non-existant
            mkdir($pagePath);

            $pagePath .= $filename . '.php';
        }



        // convert quill delta to html
        $ops = $_POST['ops-1'];
        $ops = json_decode($ops, true);

        $lexer = new nadar\quill\Lexer($ops);
        $htmlContent = $lexer->render();


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
            'parent_dir' => $dir . '/',
            'file' => $filename . '.php', 
            'updated_at' => $date
        );
        array_push($pageList['pages'], $newPageInfo);

        // call Model to write to page_list
        $this->pages->setPageList($pageList);


        header('Location: /pages');
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