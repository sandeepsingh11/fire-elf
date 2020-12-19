<?php

class AddPageController extends Controller {

    private $pages;

    function __construct() {
        $this->pages = new Pages();    
    }



    public function get() {
        include_once __DIR__ . '/../views/addPage.php';
    }



    /**
     * add the new page, from the quilljs wysiwyg
     * and update page_list.json
     */
    public function post() {
        // get values to write the new file
        $title = $_POST['title'];
        $filename = $this->titleToFile($title);
        $pagePath = '../' . CLIENT_PAGES_DIR . $filename . '.php';

        // convert quill delta to html
        $ops = $_POST['ops-1'];
        $ops = json_decode($ops, true);

        $lexer = new nadar\quill\Lexer($ops);
        $htmlContent = $lexer->render();


        // prepare html string to insert
        $pageContent = "<fireelf data-id=\"1\">$htmlContent</fireelf>";



        // create view on the client side
        file_put_contents($pagePath, $pageContent);
        


        // set values for page_list.json
        $date = date('m-d-Y h:ia');

        $pageList = $this->pages->getPageList();
        $newPageInfo = array(
            'name' => $title, 
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