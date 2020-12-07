<?php

class AddPageController extends Controller {

    function __construct() {
        
    }

    public function get() {
        include_once __DIR__ . '/../views/addPage.php';
    }



    /**
     * add the new page, from the summernote wysiwyg
     */
    public function post() {
        $pageContent = $_POST['content'];
        $title = $_POST['title'];
        $filename = $this->titleToFile($title);
        $pagePath = '../' . CLIENT_PAGES_DIR . $filename . '.php';

        file_put_contents($pagePath, $pageContent);
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