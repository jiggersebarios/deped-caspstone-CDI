<?php

namespace App\Controllers;

use App\Models\UserModel;

class Login extends BaseController
{
    public function index()
    {
        // Always show login page (no auto-redirect)
        return view('login'); // app/Views/login.php
    }

public function auth()
{
    $userModel = new \App\Models\UserModel();
    $username = trim($this->request->getPost('username'));
    $password = $this->request->getPost('password');

    // Fetch user
    $user = $userModel->where('username', $username)->first();

    if ($user && password_verify($password, $user['password'])) {

        // ✅ Query ensures main_folder info is loaded from DB
        $mainFolder = $user['main_folder'] ?? null;
        $mainFolderId = $user['main_folder_id'] ?? null;

        // ✅ Set session
        session()->set([
            'id'             => $user['id'],
            'username'       => $user['username'],
            'role'           => $user['role'],
            'main_folder'    => $mainFolder,     // Folder name (e.g. OCNHS)
            'main_folder_id' => $mainFolderId,   // Optional, if linked to folders table
            'isLoggedIn'     => true,
        ]);

        // Redirect based on role
        if ($user['role'] === 'superadmin') {
            return redirect()->to('/superadmin/dashboard');
        } elseif ($user['role'] === 'admin') {
            return redirect()->to('/admin/dashboard');
        } else {
            return redirect()->to('/user/dashboard');
        }
    }

    // Invalid credentials
    return redirect()->back()->with('error', 'Invalid username or password');
}


    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'You have been logged out.');
    }
}
