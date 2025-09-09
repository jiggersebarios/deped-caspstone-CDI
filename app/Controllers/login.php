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

        // Redirect based on role
        if ($user['role'] === 'superadmin') {
            return redirect()->to('/superadmin/dashboard');
        } elseif ($user['role'] === 'admin') {
            return redirect()->to('/admin/dashboard');
        } else {
            return redirect()->to('/dashboard');
        }
    }

    return redirect()->back()->with('error', 'Invalid username or password');
}

}
