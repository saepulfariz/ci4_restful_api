<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group("api", ['filter' => 'cors:api'], function ($routes) {
    $routes->post("register", "Register::index");
    $routes->post("login", "Login::index");

    $routes->options("posts", "Posts:options");
    $routes->options("posts/(:any)", "Posts:options");
    $routes->resource('posts', ['controller' => 'Posts', 'filter' => 'cors:api']);
    
    $routes->get("users", "Users::index", ['filter' => 'authFilter']);
});
