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
     * and update meta file (pages_info.json)
     */
    public function post() {
        // get values to write a new file
        $pageContent = $_POST['content'];
        $title = $_POST['title'];
        $filename = $this->titleToFile($title);
        $pagePath = '../' . CLIENT_PAGES_DIR . $filename . '.php';

        // create view on the client side
        file_put_contents($pagePath, $pageContent);
        


        // get values for the meta file (pages_info.json)
        date_default_timezone_set(TIMEZONE);
        $date = date('m-d-Y h:ia');

        $pagesInfo = $this->pages->getPagesInfo();
        $newPageInfo = array('name' => $title, 'file' => $filename . '.php', 'updated_at' => $date);
        array_push($pagesInfo['pages'], $newPageInfo);

        // call Model to write to file
        $this->pages->setPagesInfo($pagesInfo);


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