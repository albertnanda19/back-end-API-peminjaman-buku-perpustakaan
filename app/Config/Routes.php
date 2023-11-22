<?php

$routes->group('member', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->post('register', 'AuthController::register');
    $routes->post('login', 'AuthController::login');
});


