<?php
// Home page
$routes['/'] = 'site/home';
$routes['<language:[a-z]+>/?'] = 'site/home';
$routes['<language:[a-z]+>/search/?'] = 'site/search';

// Customer
$routes['<language:\w+>/customer/<slug:.*>/?'] = 'customer/init';

// Account
$routes['<language:\w+>/account/<action:[-\w]+>/?'] = 'account/<action>';

// Profile
$routes['<language:\w+>/profile/?'] = 'profile/index';
$routes['<language:\w+>/profile/<action:[-\w]+>/?'] = 'profile/<action>';

// Content (must be as last rule)
$routes['<language:\w+>/<content:.*>/?'] = 'site/content';

return $routes;
