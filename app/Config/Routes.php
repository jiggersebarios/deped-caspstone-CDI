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
    $routes->post('files/deleteSubfolder', 'Files::deleteSubfolder');
    $routes->get('files/view/(:num)', 'Files::view/$1');
    $routes->post('files/addSubfolder/(:num)', 'Files::addSubfolder/$1'); 
   $routes->post('files/upload/(:num)', 'Files::upload/$1');
    $routes->post('files/deleteFile/(:num)', 'Files::deleteFile/$1');
    $routes->get('files/download/(:num)', 'Files::download/$1');

});



// =========================
// SUPERADMIN routes
// =========================
$routes->group('superadmin', ['namespace' => 'App\Controllers\Superadmin', 'filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'Dashboard::index');

    // Files module
    $routes->get('files', 'Files::index');
    $routes->get('files/view/(:num)', 'Files::view/$1');
    $routes->post('files/add', 'Files::add');
    $routes->post('files/addSubfolder/(:num)', 'Files::addSubfolder/$1');
    $routes->post('files/delete', 'Files::delete');
    $routes->post('files/deleteSubfolder', 'Files::deleteSubfolder');

    // 🔹 New for superadmin files
    $routes->post('files/upload/(:num)', 'Files::upload/$1');
    $routes->post('files/deleteFile/(:num)', 'Files::deleteFile/$1');
    $routes->get('files/download/(:num)', 'Files::download/$1');

    // Global Config
    $routes->get('globalconfig', 'Globalconfig::index');
    $routes->post('globalconfig/toggle', 'Globalconfig::toggle');


        $routes->get('category', 'Category::index');
    $routes->post('category/add', 'Category::add');
    $routes->post('category/delete/(:num)', 'Category::delete/$1');
    $routes->get('category/edit/(:num)', 'Category::edit/$1');
    $routes->post('category/update/(:num)', 'Category::update/$1');
});
