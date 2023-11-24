<?php

$routes->group('member', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->post('login', 'AuthController::memberLogin');
    $routes->post('register', 'AuthController::memberRegister');
});

$routes->group('admin', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->post('login', 'AuthController::adminLogin');
});

$routes->group('books', ['namespace' => 'App\Controllers', 'filter' => 'auth'], function ($routes) {
    $routes->get('/', 'BookController::index');
    $routes->get('(:num)', 'BookController::show/$1');
});
