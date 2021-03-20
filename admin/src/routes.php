<?php

$this->get('', 'IndexController@index');



$this->get('register', 'RegisterController@index');
$this->post('register', 'RegisterController@register');



$this->get('login', 'LoginController@index');
$this->post('login', 'LoginController@login');



$this->get('logout', 'LogoutController@logout');



$this->get('pages', 'PageController@index');
$this->post('pages', 'PageController@deletePage');
$this->get('pages/editor', 'PageController@editor');
$this->post('pages/editor', 'PageController@updatePage');



$this->get('media-lib', 'MediaController@index');
$this->post('media-lib', 'MediaController@addMedia');
$this->post('media-lib/delete', 'MediaController@deleteMedia');



$this->get('blogs', 'BlogController@index');
$this->post('blogs', 'BlogController@deleteBlog');
$this->get('blog/editor', 'BlogController@editor');
$this->post('blog/editor', 'BlogController@updateBlog');



$this->get('settings', 'SettingsController@index');
$this->post('settings/update', 'SettingsController@updateSettings');
$this->post('settings/delete', 'SettingsController@deleteSettings');