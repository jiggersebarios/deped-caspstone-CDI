<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// =============================================================
// AUTHENTICATION
// =============================================================
$routes->get('/login', 'Login::index');
$routes->post('/login/auth', 'Login::auth');
$routes->get('/logout', 'Login::logout');

// =============================================================
// DASHBOARDS
// =============================================================
$routes->get('/dashboard', 'Dashboard::index');
$routes->get('/admin/dashboard', 'Admin\Dashboard::index');

// =============================================================
// ADMIN ROUTES
// =============================================================
$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => 'auth'], function ($routes) {

    // Dashboard
    $routes->get('dashboard', 'Dashboard::index');

// FILE MANAGEMENT
$routes->get('files', '\App\Controllers\Files::index');
$routes->get('files/view/(:num)', '\App\Controllers\Files::view/$1');
$routes->get('files/viewFile/(:num)', '\App\Controllers\Files::viewFile/$1');
$routes->post('files/add', '\App\Controllers\Files::add');
$routes->post('files/addSubfolder/(:num)', '\App\Controllers\Files::addSubfolder/$1');
$routes->post('files/delete', '\App\Controllers\Files::delete');
$routes->post('files/renameMainFolder', '\App\Controllers\Files::renameMainFolder');
$routes->post('files/deleteSubfolder', '\App\Controllers\Files::deleteSubfolder');
$routes->post('files/renameSubfolder', '\App\Controllers\Files::renameSubfolder');
$routes->post('files/upload/(:num)', '\App\Controllers\Files::upload/$1');
$routes->post('files/deleteFile/(:num)', '\App\Controllers\Files::deleteFile/$1');
$routes->get('files/download/(:num)', '\App\Controllers\Files::download/$1');
$routes->post('files/renameFile', '\App\Controllers\Files::renameFile');
//  MANAGE UPLOADS (Admin)
$routes->get('manage_uploads', '\App\Controllers\ManageUploads::index');
$routes->get('manage_uploads/accept/(:num)', '\App\Controllers\ManageUploads::accept/$1');
$routes->get('manage_uploads/reject/(:num)', '\App\Controllers\ManageUploads::reject/$1');

//  FILE REQUESTS (Admin)
$routes->get('manage_request', '\App\Controllers\Request::manage');  // Manage list page
$routes->post('request/submit', '\App\Controllers\Request::submit'); // File request submission
$routes->get('manage_request/approve/(:num)', '\App\Controllers\Request::approve/$1');
$routes->get('manage_request/deny/(:num)', '\App\Controllers\Request::deny/$1');

//manual
// Manual Page
$routes->get('manual', '\App\Controllers\Manual::index');

});

// =============================================================
// SUPERADMIN 
// =============================================================
$routes->group('superadmin', ['namespace' => 'App\Controllers\Superadmin', 'filter' => 'auth'], function ($routes) {

    // Dashboard
    $routes->get('dashboard', 'Dashboard::index');

    // FILE MANAGEMENT
// SUPERADMIN FILE MANAGEMENT (shared Files controller)
$routes->get('files', '\App\Controllers\Files::index');
$routes->get('files/view/(:num)', '\App\Controllers\Files::view/$1');
$routes->post('files/add', '\App\Controllers\Files::add');
$routes->post('files/addSubfolder/(:num)', '\App\Controllers\Files::addSubfolder/$1');
$routes->post('files/renameMainFolder', '\App\Controllers\Files::renameMainFolder');
$routes->post('files/delete', '\App\Controllers\Files::delete');
$routes->post('files/deleteSubfolder', '\App\Controllers\Files::deleteSubfolder');
$routes->post('files/renameSubfolder', '\App\Controllers\Files::renameSubfolder');
$routes->post('files/upload/(:num)', '\App\Controllers\Files::upload/$1');
$routes->get('files/viewFile/(:num)', '\App\Controllers\Files::viewFile/$1'); 
$routes->post('files/deleteFile/(:num)', '\App\Controllers\Files::deleteFile/$1');
$routes->get('files/download/(:num)', '\App\Controllers\Files::download/$1');
$routes->post('files/renameFile', '\App\Controllers\Files::renameFile');
$routes->get('files/restore/(:num)', '\App\Controllers\Files::restore/$1');


 // MANAGE USERS
$routes->get('manage_users', 'ManageUsers::index');
$routes->post('manage_users/store', 'ManageUsers::store');
$routes->post('manage_users/update/(:num)', 'ManageUsers::update/$1');
$routes->get('manage_users/delete/(:num)', 'ManageUsers::delete/$1');

// MANAGE UPLOADS 
$routes->get('manage_uploads', '\App\Controllers\ManageUploads::index');
$routes->get('manage_uploads/accept/(:num)', '\App\Controllers\ManageUploads::accept/$1');
$routes->get('manage_uploads/reject/(:num)', '\App\Controllers\ManageUploads::reject/$1');

 // FILE REQUESTS 
$routes->get('manage_request', '\App\Controllers\Request::manage');
$routes->post('request/submit', '\App\Controllers\Request::submit');
$routes->get('manage_request/approve/(:num)', '\App\Controllers\Request::approve/$1');
$routes->get('manage_request/deny/(:num)', '\App\Controllers\Request::deny/$1');
    

 // Global Configuration
$routes->get('globalconfig', 'Globalconfig::index');       
$routes->post('globalconfig/toggle', 'Globalconfig::toggle');
});

// =============================================================
//  USER ROUTES
// =============================================================
$routes->group('user', ['namespace' => 'App\Controllers\User', 'filter' => 'auth'], function ($routes) {

    // Dashboard
    $routes->get('dashboard', 'Dashboard::index');

    // FILE MANAGEMENT
    $routes->get('files', '\App\Controllers\Files::index');
    $routes->get('files/view/(:num)', '\App\Controllers\Files::view/$1');
    $routes->post('files/addSubfolder/(:num)', '\App\Controllers\Files::addSubfolder/$1');
    $routes->post('files/upload/(:num)', '\App\Controllers\Files::upload/$1');
    $routes->get('files/viewFile/(:num)', '\App\Controllers\Files::viewFile/$1');
    $routes->get('files/download/(:num)', '\App\Controllers\Files::download/$1');
    $routes->post('files/renameFile', '\App\Controllers\Files::renameFile');
    //  FILE REQUESTS (User)
    $routes->get('request', '\App\Controllers\Request::userRequests');
    $routes->post('request/submit', '\App\Controllers\Request::submit');



});

// =============================================================
//  ONE-TIME DOWNLOAD HANDLER
// =============================================================
$routes->get('request/download/(:segment)', 'Request::download/$1');

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

// 🔹 CATEGORY ROUTES (Shared Controller)

$routes->group('admin', ['filter' => 'auth'], function($routes) {
    $routes->get('category', 'Category::index');
    $routes->post('category/store', 'Category::store');
    $routes->post('category/update/(:num)', 'Category::update/$1');
    $routes->post('category/delete/(:num)', 'Category::delete/$1');
});

$routes->group('superadmin', ['filter' => 'auth'], function($routes) {
    $routes->get('category', 'Category::index');
    $routes->post('category/store', 'Category::store');
    $routes->post('category/update/(:num)', 'Category::update/$1');
    $routes->post('category/delete/(:num)', 'Category::delete/$1');
});

$routes->get('superadmin/manage_request/download/(:num)', 'Request::directDownload/$1');
$routes->get('admin/manage_request/download/(:num)', 'Request::directDownload/$1');
    // Request downloads (direct)
$routes->get('request/directDownload/(:num)', 'Request::directDownload/$1');
$routes->get('superadmin/manage_request/directDownload/(:num)', 'Request::directDownload/$1');
$routes->get('admin/manage_request/directDownload/(:num)', 'Request::directDownload/$1');


// =============================================================
// SHARED FILES ROUTES (All Roles)
// =============================================================
$routes->get('sharedfiles', '\App\Controllers\Sharedfiles::index');
$routes->get('sharedfiles/download/(:num)', '\App\Controllers\Sharedfiles::download/$1');
$routes->get('sharedfiles/unshare/(:num)', 'Sharedfiles::unshare/$1');
$routes->get('sharedfiles/generateToken/(:num)', 'Sharedfiles::generateToken/$1');
$routes->get('sharedfiles/access/(:segment)', 'Sharedfiles::access/$1');


// Admin
$routes->group('admin', ['filter' => 'auth'], function($routes) {
    $routes->get('sharedfiles', '\App\Controllers\Sharedfiles::index');
    $routes->post('sharedfiles/share', '\App\Controllers\Sharedfiles::share');
    $routes->get('sharedfiles/unshare/(:num)', 'Sharedfiles::unshare/$1');
   
});

// Superadmin
$routes->group('superadmin', ['filter' => 'auth'], function($routes) {
    $routes->get('sharedfiles', '\App\Controllers\Sharedfiles::index');
    $routes->post('sharedfiles/share', '\App\Controllers\Sharedfiles::share');
    $routes->get('sharedfiles/unshare/(:num)', 'Sharedfiles::unshare/$1');
   
});

// User
$routes->group('user', ['filter' => 'auth'], function($routes) {
    $routes->get('sharedfiles', '\App\Controllers\Sharedfiles::index');
    $routes->post('sharedfiles/share', '\App\Controllers\Sharedfiles::share');
    $routes->get('sharedfiles/unshare/(:num)', '\App\Controllers\Sharedfiles::unshare/$1'); // ✅ fixed
});


// =============================================================
// 🗑️ Deleted Files (Universal Route for All Roles)
// =============================================================
$routes->get('(:any)/files/getDeletedFiles', 'Files::getDeletedFiles');
// app/Config/Routes.php
$routes->get('(:segment)/files/getDeletedFiles', 'Files::getDeletedFiles/$1');

// =============================================================
// Notification
// =============================================================
$routes->get('(:segment)/get-notifications', 'NotificationController::getNotifications');
// Universal Notifications route for all roles
$routes->get('(:segment)/get-notifications', '\App\Controllers\NotificationController::getNotifications/$1');

//email route
$routes->get('contact', 'Send_message::index');
$routes->post('contact/send', 'Send_message::send');
