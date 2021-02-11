<?php

abstract class Model {

    /**
     * prepare any string ($title) into a file name
     * @param string $title
     * @return string
     */
    protected function prepFilename($title) {
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