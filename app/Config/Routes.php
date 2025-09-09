<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// DepEd login
$routes->get('/login', 'Login::index');
$routes->post('/login/auth', 'Login::auth');

$routes->get('/dashboard', 'Dashboard::index'); // user
$routes->get('/admin/dashboard', 'Admin::index'); // admin

$routes->post('/logout', 'Logout::index');


// Admin routes with auth filter
$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => 'auth'], function($routes) {
    $routes->get('files', 'Files::index');
    $routes->post('files/add', 'Files::add');
    $routes->post('files/delete', 'Files::delete');
    $routes->post('files/deleteSubfolder', 'Files::deleteSubfolder'); // fixed
    $routes->get('files/view/(:num)', 'Files::view/$1');
});

$routes->post('admin/files/addSubfolder/(:num)', 'Admin\Files::addSubfolder/$1');

//superadmin routes with auth filter
// Superadmin routes
$routes->group('superadmin', ['namespace' => 'App\Controllers\Superadmin'], function($routes) {
    $routes->get('dashboard', 'Dashboard::index');

    // Files module
    $routes->get('files', 'Files::index');
    $routes->get('files/view/(:num)', 'Files::view/$1');
    $routes->post('files/add', 'Files::add');
    $routes->post('files/addSubfolder/(:num)', 'Files::addSubfolder/$1');
    $routes->post('files/delete', 'Files::delete');
    $routes->post('files/deleteSubfolder', 'Files::deleteSubfolder');
});





