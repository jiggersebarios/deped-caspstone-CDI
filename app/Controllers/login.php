<?php

namespace App\Controllers;
use App\Models\UserModel;

class Login extends BaseController
{
    public function index()
    {
        return view('login'); // looks for app/Views/login.php
    }
    public function auth()
    {
        $userModel = new UserModel();
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $userModel->where('username', $username)->first();

        if ($user && password_verify($password, $user['password'])) {
            session()->set([
                'username'   => $user['username'],
                'role'       => $user['role'],
                'isLoggedIn' => true,
            ]);

            return $user['role'] === 'admin'
                ? redirect()->to('/admin/dashboard')
                : redirect()->to('/dashboard');
        }

        
        return redirect()->back()->with('error', 'Invalid username or password');

    }
}
