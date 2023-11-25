<?php

$routes->group('member', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->post('login', 'AuthController::memberLogin');
    $routes->post('register', 'AuthController::memberRegister');
});

$routes->group('admin', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->post('login', 'AuthController::adminLogin');
    $routes->post('approve/(:num)', 'AdminController::approvePeminjaman/$1', ['filter' => 'adminauth']);
    $routes->post('reject/(:num)', 'AdminController::rejectPeminjaman/$1', ['filter' => 'adminauth']);
    $routes->post('reject/clear', 'AdminController::clearRejectedPeminjaman', ['filter' => 'adminauth']);
    $routes->get('members', 'AdminController::getAllMembers', ['filter' => 'adminauth']);
});

$routes->group('books', ['namespace' => 'App\Controllers', 'filter' => 'auth'], function ($routes) {
    $routes->get('/', 'BookController::index');
    $routes->get('(:num)', 'BookController::show/$1');
    $routes->post('borrow/(:num)/(:num)', 'BookController::borrowBook/$1/$2');
});
