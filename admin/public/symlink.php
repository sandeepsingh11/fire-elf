<?php

// symlink will link / point the src dir to a new dir within the client dir
$src = $_SERVER['DOCUMENT_ROOT'] . '/media/'; // do not edit
$dest = $_SERVER['DOCUMENT_ROOT'] . '/../../client/public/assets/fireelf/'; // change to desired location

symlink($src, $dest);


// CHANGE PUBLIC ENTRY POINT TO GET IMAGES