<?php

$router->get('', 'IndexController@get');

$router->get('pages', 'PageController@get');

$router->get('pages/edit', 'EditorController@get');