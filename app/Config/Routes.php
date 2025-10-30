<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// -------------------- Authentication --------------------
$routes->get('/login', 'Login::index');
$routes->post('/login/auth', 'Login::auth');
$routes->get('/logout', 'Login::logout');

// -------------------- USER DASHBOARD --------------------
$routes->get('/dashboard', 'Dashboard::index');

// -------------------- ADMIN DASHBOARD --------------------
// Use correct namespaced controller
$routes->get('/admin/dashboard', 'Admin\Dashboard::index');

// -------------------- ADMIN ROUTES --------------------
$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');

    // Shared Files Controller (from App\Controllers)
    $routes->get('files', '\App\Controllers\Files::index');
    $routes->post('files/add', '\App\Controllers\Files::add');
    $routes->post('files/delete', '\App\Controllers\Files::delete');
    $routes->post('files/deleteSubfolder', '\App\Controllers\Files::deleteSubfolder');
    $routes->get('files/view/(:num)', '\App\Controllers\Files::view/$1');
    $routes->post('files/addSubfolder/(:num)', '\App\Controllers\Files::addSubfolder/$1');
    $routes->post('files/upload/(:num)', '\App\Controllers\Files::upload/$1');
    $routes->get('files/viewFile/(:num)', '\App\Controllers\Files::viewFile/$1');
    $routes->post('files/deleteFile/(:num)', '\App\Controllers\Files::deleteFile/$1');
    $routes->get('files/download/(:num)', '\App\Controllers\Files::download/$1');
    $routes->post('files/renameFile', '\App\Controllers\Files::renameFile');

     $routes->get('category', 'Category::index');
 


    
});

// -------------------- SUPERADMIN ROUTES --------------------
$routes->group('superadmin', ['namespace' => 'App\Controllers\Superadmin', 'filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');

    // Shared Files Controller
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

    $routes->post('files/renameFile', '\App\Controllers\Files::renameFile');



    // Global Config
    $routes->get('globalconfig', 'Globalconfig::index');
    $routes->post('globalconfig/toggle', 'Globalconfig::toggle');

    // Category Management
    $routes->get('category', 'Category::index');
    $routes->get('category/create', 'Category::create');
    $routes->post('category/store', 'Category::store');
    $routes->get('category/edit/(:num)', 'Category::edit/$1');
    $routes->post('category/update/(:num)', 'Category::update/$1');
    $routes->post('category/delete/(:num)', 'Category::delete/$1');

    // Manage Users
    $routes->get('manage_users', 'ManageUsers::index');
    $routes->post('manage_users/store', 'ManageUsers::store');
    $routes->post('manage_users/update/(:num)', 'ManageUsers::update/$1');
    $routes->get('manage_users/delete/(:num)', 'ManageUsers::delete/$1');
});

// -------------------- FILE MANAGEMENT --------------------
$routes->post('superadmin/files/deleteMainFolder', 'Superadmin\Files::deleteMainFolder');
$routes->post('admin/files/deleteMainFolder', 'Admin\Files::deleteMainFolder');

// -------------------- MANAGE UPLOADS (shared) --------------------
$routes->get('manage-uploads', 'ManageUploads::index');
$routes->get('manage-uploads/accept/(:num)', 'ManageUploads::accept/$1');
$routes->get('manage-uploads/reject/(:num)', 'ManageUploads::reject/$1');

$routes->get('admin/manage-uploads', 'ManageUploads::index');
$routes->get('admin/manage-uploads/accept/(:num)', 'ManageUploads::accept/$1');
$routes->get('admin/manage-uploads/reject/(:num)', 'ManageUploads::reject/$1');

$routes->get('superadmin/manage-uploads', 'ManageUploads::index');
$routes->get('superadmin/manage-uploads/accept/(:num)', 'ManageUploads::accept/$1');
$routes->get('superadmin/manage-uploads/reject/(:num)', 'ManageUploads::reject/$1');

// -------------------- USER ROUTES --------------------
$routes->group('user', ['namespace' => 'App\Controllers\User', 'filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');

    // Shared Files Controller
    $routes->get('files', '\App\Controllers\Files::index');
    $routes->get('files/view/(:num)', '\App\Controllers\Files::view/$1');
    $routes->post('files/addSubfolder/(:num)', '\App\Controllers\Files::addSubfolder/$1');
    $routes->post('files/upload/(:num)', '\App\Controllers\Files::upload/$1');
    $routes->get('files/viewFile/(:num)', '\App\Controllers\Files::viewFile/$1');
    $routes->get('files/download/(:num)', '\App\Controllers\Files::download/$1');
     $routes->post('files/renameFile', '\App\Controllers\Files::renameFile');
});
