<?php

namespace App\Controllers\Superadmin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class ManageUsers extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();
        $data['users'] = $userModel->findAll();
        $data['title'] = 'Manage Users';

        // Make sure this matches the folder + filename
        return view('superadmin/manage_users', $data);
    }


    public function create()
    {
        if ($this->request->getMethod() === 'post') {
            $userModel = new UserModel();
            $data = [
                'username' => $this->request->getPost('username'),
                'email'    => $this->request->getPost('email'),
                'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                'role'     => $this->request->getPost('role'),
            ];
            $userModel->insert($data);
            return redirect()->to('/superadmin/manage_users')->with('success', 'User created successfully');
        }
    }

    public function edit($id)
    {
        $userModel = new UserModel();
        $data['user'] = $userModel->find($id);

        if ($this->request->getMethod() === 'post') {
            $updateData = [
                'username' => $this->request->getPost('username'),
                'email'    => $this->request->getPost('email'),
                'role'     => $this->request->getPost('role'),
            ];
            if ($this->request->getPost('password')) {
                $updateData['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
            }
            $userModel->update($id, $updateData);
            return redirect()->to('/superadmin/manage_users')->with('success', 'User updated successfully');
        }

        // ✅ reuse the same file or separate (your choice)
        return view('superadmin/manage_users_edit', $data);
    }

    public function delete($id)
    {
        $userModel = new UserModel();
        $userModel->delete($id);
        return redirect()->to('/superadmin/manage_users')->with('success', 'User deleted successfully');
    }
}
