<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// =============================================================
// 🔐 AUTHENTICATION
// =============================================================
$routes->get('/login', 'Login::index');
$routes->post('/login/auth', 'Login::auth');
$routes->get('/logout', 'Login::logout');

// =============================================================
// 🧭 DASHBOARDS
// =============================================================
$routes->get('/dashboard', 'Dashboard::index');
$routes->get('/admin/dashboard', 'Admin\Dashboard::index');

// =============================================================
// 📂 ADMIN ROUTES
// =============================================================
$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => 'auth'], function ($routes) {

    // Dashboard
    $routes->get('dashboard', 'Dashboard::index');

    // 🔹 FILE MANAGEMENT
    $routes->get('files', '\App\Controllers\Files::index');
    $routes->get('files/view/(:num)', '\App\Controllers\Files::view/$1');
    $routes->post('files/add', '\App\Controllers\Files::add');
    $routes->post('files/addSubfolder/(:num)', '\App\Controllers\Files::addSubfolder/$1');
    $routes->post('files/delete', '\App\Controllers\Files::delete');
    $routes->post('files/deleteSubfolder', '\App\Controllers\Files::deleteSubfolder');
    $routes->post('files/upload/(:num)', '\App\Controllers\Files::upload/$1');
    $routes->post('files/deleteFile/(:num)', '\App\Controllers\Files::deleteFile/$1');
    $routes->get('files/download/(:num)', '\App\Controllers\Files::download/$1');
    $routes->post('files/renameFile', '\App\Controllers\Files::renameFile');

    // 🔹 CATEGORY
    $routes->get('category', 'Category::index');

        // 🔹 MANAGE UPLOADS (Admin)
$routes->get('manage_uploads', '\App\Controllers\ManageUploads::index');
$routes->get('manage_uploads/accept/(:num)', '\App\Controllers\ManageUploads::accept/$1');
$routes->get('manage_uploads/reject/(:num)', '\App\Controllers\ManageUploads::reject/$1');

    // 🔹 FILE REQUESTS (Admin)
    $routes->get('manage_request', '\App\Controllers\Request::manage');  // Manage list page
    $routes->post('request/submit', '\App\Controllers\Request::submit'); // File request submission
    $routes->get('manage_request/approve/(:num)', '\App\Controllers\Request::approve/$1');
    $routes->get('manage_request/deny/(:num)', '\App\Controllers\Request::deny/$1');
});

// =============================================================
// 📂 SUPERADMIN ROUTES
// =============================================================
$routes->group('superadmin', ['namespace' => 'App\Controllers\Superadmin', 'filter' => 'auth'], function ($routes) {

    // Dashboard
    $routes->get('dashboard', 'Dashboard::index');

    // 🔹 FILE MANAGEMENT
    $routes->get('files', '\App\Controllers\Files::index');
    $routes->get('files/view/(:num)', '\App\Controllers\Files::view/$1');
    $routes->post('files/add', '\App\Controllers\Files::add');
    $routes->post('files/addSubfolder/(:num)', '\App\Controllers\Files::addSubfolder/$1');
    $routes->post('files/delete', '\App\Controllers\Files::delete');
    $routes->post('files/deleteSubfolder', '\App\Controllers\Files::deleteSubfolder');
    $routes->post('files/upload/(:num)', '\App\Controllers\Files::upload/$1');
    $routes->post('files/deleteFile/(:num)', '\App\Controllers\Files::deleteFile/$1');
    $routes->get('files/download/(:num)', '\App\Controllers\Files::download/$1');
    $routes->post('files/renameFile', '\App\Controllers\Files::renameFile');

    // 🔹 CATEGORY
    $routes->get('category', 'Category::index');

    // 🔹 MANAGE USERS
    $routes->get('manage_users', 'ManageUsers::index');
    $routes->post('manage_users/store', 'ManageUsers::store');
    $routes->post('manage_users/update/(:num)', 'ManageUsers::update/$1');
    $routes->get('manage_users/delete/(:num)', 'ManageUsers::delete/$1');

// 🔹 MANAGE UPLOADS (Superadmin)
$routes->get('manage_uploads', '\App\Controllers\ManageUploads::index');
$routes->get('manage_uploads/accept/(:num)', '\App\Controllers\ManageUploads::accept/$1');
$routes->get('manage_uploads/reject/(:num)', '\App\Controllers\ManageUploads::reject/$1');

    // 🔹 FILE REQUESTS (Superadmin)
    $routes->get('manage_request', '\App\Controllers\Request::manage');
    $routes->post('request/submit', '\App\Controllers\Request::submit');
    $routes->get('manage_request/approve/(:num)', '\App\Controllers\Request::approve/$1');
    $routes->get('manage_request/deny/(:num)', '\App\Controllers\Request::deny/$1');

        // Global Configuration
    $routes->get('globalconfig', 'Globalconfig::index');       // View settings
    $routes->post('globalconfig/toggle', 'Globalconfig::toggle'); // Toggle a setting via AJAX
});

// =============================================================
// 👤 USER ROUTES
// =============================================================
$routes->group('user', ['namespace' => 'App\Controllers\User', 'filter' => 'auth'], function ($routes) {

    // Dashboard
    $routes->get('dashboard', 'Dashboard::index');

    // 🔹 FILE MANAGEMENT
    $routes->get('files', '\App\Controllers\Files::index');
    $routes->get('files/view/(:num)', '\App\Controllers\Files::view/$1');
    $routes->post('files/addSubfolder/(:num)', '\App\Controllers\Files::addSubfolder/$1');
    $routes->post('files/upload/(:num)', '\App\Controllers\Files::upload/$1');
    $routes->get('files/viewFile/(:num)', '\App\Controllers\Files::viewFile/$1');
    $routes->get('files/download/(:num)', '\App\Controllers\Files::download/$1');
    $routes->post('files/renameFile', '\App\Controllers\Files::renameFile');

    // 🔹 FILE REQUESTS (User)
    $routes->get('request', '\App\Controllers\Request::userRequests');
    $routes->post('request/submit', '\App\Controllers\Request::submit');
});

// =============================================================
// 🧩 ONE-TIME DOWNLOAD HANDLER
// =============================================================
$routes->get('request/download/(:segment)', 'Request::download/$1');
