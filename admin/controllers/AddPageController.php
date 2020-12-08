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
     * add the new page, from the summernote wysiwyg
     * and update page_list.json
     */
    public function post() {
        // get values to write a new file
        $pageContent = $_POST['content'];
        $title = $_POST['title'];
        $filename = $this->titleToFile($title);
        $pagePath = '../' . CLIENT_PAGES_DIR . $filename . '.php';

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