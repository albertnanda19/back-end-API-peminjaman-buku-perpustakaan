<?php

$routes->group('member', ['namespace' => 'App\Controllers', 'filter' => 'cors'], function ($routes) {
    $routes->post('login', 'AuthController::memberLogin');
    $routes->post('register', 'AuthController::memberRegister');
    $routes->patch('username/(:num)', 'MemberController::updateUsername/$1', ['filter' => 'auth']);
    // $routes->get('peminjaman/(:any)', 'MemberController::getPeminjamanUser/$1', ['filter' => 'auth']);
    $routes->get('peminjaman/(:segment)', 'MemberController::getPeminjamanByUserId/$1', ['filter' => 'auth']);
    $routes->get('history/(:segment)', 'MemberController::getHistoryPeminjaman/$1', ['filter' => 'auth']);
});

$routes->group('admin', ['namespace' => 'App\Controllers', 'filter' => 'cors'], function ($routes) {
    $routes->post('login', 'AuthController::adminLogin');
    $routes->post('approve/(:num)', 'AdminController::approvePeminjaman/$1', ['filter' => 'adminauth']);
    $routes->post('reject/(:num)', 'AdminController::rejectPeminjaman/$1', ['filter' => 'adminauth']);
    $routes->post('reject/clear', 'AdminController::clearRejectedPeminjaman', ['filter' => 'adminauth']);
    $routes->get('members', 'AdminController::getAllMembers', ['filter' => 'adminauth']);
    $routes->delete('delete-member/(:num)', 'AdminController::deleteMember/$1', ['filter' => 'adminauth']);
    $routes->put('add/book', 'AdminController::addBook', ['filter' => 'adminauth']);
    $routes->patch('edit-book/(:num)', 'AdminController::editBook/$1', ['filter' => 'adminauth']);
    $routes->delete('delete-book/(:num)', 'AdminController::deleteBook/$1', ['filter' => 'adminauth']);
    $routes->put('return/(:num)', 'AdminController::returnBook/$1', ['filter' => 'adminauth']);
    $routes->get('all-peminjaman', 'AdminController::getAllPeminjaman', ['filter' => 'adminauth']);
});

$routes->group('books', ['namespace' => 'App\Controllers', ['filter' => ['auth', 'cors']]], function ($routes) {
    $routes->get('/', 'BookController::index');
    $routes->get('(:num)', 'BookController::show/$1');
    $routes->post('borrow/(:num)/(:num)/(:num)', 'BookController::borrowBook/$1/$2/$3');
});
