<?php

$this->get('', 'IndexController@get');



$this->get('register', 'RegisterController@get');
$this->post('register', 'RegisterController@post');



$this->get('login', 'LoginController@get');
$this->post('login', 'LoginController@post');



$this->get('pages', 'PageController@getAll');
$this->post('pages', 'PageController@delete');
$this->get('pages/editor', 'PageController@get');
$this->post('pages/editor', 'PageController@post');



$this->get('media-lib', 'MediaController@get');
$this->post('media-lib', 'MediaController@post');
$this->post('media-lib/delete', 'MediaController@delete');



$this->get('blogs', 'BlogController@getAll');
$this->post('blogs', 'BlogController@delete');
$this->get('blog/editor', 'BlogController@get');
$this->post('blog/editor', 'BlogController@post');