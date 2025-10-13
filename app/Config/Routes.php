<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// -------------------- Authentication --------------------
$routes->get('/login', 'Login::index');
$routes->post('/login/auth', 'Login::auth');
$routes->get('/logout', 'Login::logout'); // ✅ match controller method


//dashboard
$routes->get('/dashboard', 'Dashboard::index'); // user
$routes->get('/admin/dashboard', 'Admin::index'); // admin


// -------------------- Admin Routes --------------------
$routes->group('admin', ['namespace' => 'App\Controllers', 'filter' => 'auth'], function($routes) {
    // Unified Files controller
    $routes->get('files', 'Files::index');
    $routes->post('files/add', 'Files::add');
    $routes->post('files/delete', 'Files::delete');
    $routes->post('files/deleteSubfolder', 'Files::deleteSubfolder');
    $routes->get('files/view/(:num)', 'Files::view/$1');
    $routes->post('files/addSubfolder/(:num)', 'Files::addSubfolder/$1'); 
    $routes->post('files/upload/(:num)', 'Files::upload/$1');
    $routes->get('files/viewFile/(:num)', 'Files::viewFile/$1');

    $routes->post('files/deleteFile/(:num)', 'Files::deleteFile/$1');
    $routes->get('files/download/(:num)', 'Files::download/$1');
});


// -------------------- Superadmin Routes --------------------
$routes->group('superadmin', ['namespace' => 'App\Controllers\Superadmin', 'filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'Dashboard::index'); // points to Superadmin dashboard

    // For the shared Files controller, specify full namespace WITHOUT the group namespace being applied
    $routes->get('files', '\App\Controllers\Files::index');
    $routes->get('files/view/(:num)', '\App\Controllers\Files::view/$1');
    $routes->post('files/add', '\App\Controllers\Files::add');
    $routes->post('files/addSubfolder/(:num)', '\App\Controllers\Files::addSubfolder/$1');
    $routes->post('files/delete', '\App\Controllers\Files::delete');
    $routes->post('files/deleteSubfolder', '\App\Controllers\Files::deleteSubfolder');
    $routes->post('files/upload/(:num)', '\App\Controllers\Files::upload/$1');
$routes->get('files/viewFile/(:num)', '\App\Controllers\Files::viewFile/$1');

    $routes->post('files/deleteFile/(:num)', '\App\Controllers\Files::deleteFile/$1');
    $routes->get('files/download/(:num)', '\App\Controllers\Files::download/$1');

    // Global Config
    $routes->get('globalconfig', 'Globalconfig::index');
    $routes->post('globalconfig/toggle', 'Globalconfig::toggle');

    // Category management
    $routes->get('category', 'Category::index');
    $routes->post('category/add', 'Category::add');
    $routes->post('category/delete/(:num)', 'Category::delete/$1');
    $routes->get('category/edit/(:num)', 'Category::edit/$1');
    $routes->post('category/update/(:num)', 'Category::update/$1');

     
// Manage Users
$routes->get('manage_users', 'ManageUsers::index');
$routes->post('manage_users/store', 'ManageUsers::store');
$routes->post('manage_users/update/(:num)', 'ManageUsers::update/$1');
$routes->get('manage_users/delete/(:num)', 'ManageUsers::delete/$1');

});

//user
// -------------------- User Routes --------------------
$routes->group('user', ['namespace' => 'App\Controllers', 'filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'User\Dashboard::index');

    // ✅ Allow users to access the shared Files controller
    $routes->get('files', 'Files::index');
    $routes->get('files/view/(:num)', 'Files::view/$1');
    $routes->post('files/addSubfolder/(:num)', 'Files::addSubfolder/$1');
    $routes->post('files/upload/(:num)', 'Files::upload/$1');
    $routes->get('files/viewFile/(:num)', 'Files::viewFile/$1');
    $routes->get('files/download/(:num)', 'Files::download/$1');
});
