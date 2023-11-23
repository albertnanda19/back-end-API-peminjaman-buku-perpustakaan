<?php

$routes->group('member', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->post('register', 'AuthController::register');
    $routes->post('login', 'AuthController::login');
    $routes->get('books', 'BooksController::index',  ['filter' => 'auth']);
});

$routes->group('admin', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->post('login', 'AuthController::adminLogin');
});

$routes->group('books', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->get('/', 'BooksController::index', ['filter' => 'auth']); // Menambahkan filter auth
});


