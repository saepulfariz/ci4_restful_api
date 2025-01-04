<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group("api", function ($routes) {
    $routes->post("register", "Register::index");
    $routes->post("login", "Login::index");

    $routes->resource('posts', ['controller' => 'Posts']);
    $routes->get("users", "Users::index", ['filter' => 'authFilter']);
});
