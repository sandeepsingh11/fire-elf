<?php

$router->get('', 'IndexController@get');



$router->get('pages', 'PageController@getAll');
$router->post('pages', 'PageController@delete');
$router->get('pages/editor', 'PageController@get');
$router->post('pages/editor', 'PageController@post');



$router->get('media-lib', 'MediaController@get');
$router->post('media-lib', 'MediaController@post');



$router->get('blogs', 'BlogController@getAll');
$router->get('blog/editor', 'BlogController@get');
$router->post('blog/editor', 'BlogController@post');