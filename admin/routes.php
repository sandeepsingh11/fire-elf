<?php

$router->get('', 'IndexController@get');

$router->get('pages', 'PageController@get');

$router->get('pages/add', 'AddPageController@get');
$router->post('pages/add', 'AddPageController@post');

$router->get('pages/edit', 'EditPageController@get');
$router->post('pages/edit', 'EditPageController@post');