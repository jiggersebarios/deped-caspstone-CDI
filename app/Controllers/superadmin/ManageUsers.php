<?php

namespace App\Controllers\Superadmin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\FolderModel;

class ManageUsers extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();
        $folderModel = new FolderModel();

        $data['users'] = $userModel->findAll();
        $data['folders'] = $folderModel->findAll();
        $data['title'] = 'Manage Users';

        return view('superadmin/manage_users', $data);
    }

    public function store()
    {
        $userModel = new UserModel();
        $folderModel = new FolderModel();
        $folderBasePath = FCPATH . 'uploads/';

        $username   = trim($this->request->getPost('username'));
        $email      = trim($this->request->getPost('email'));
        $password   = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        $role       = $this->request->getPost('role');
        $school_id  = trim($this->request->getPost('school_id')); // new field

        if (empty($username) || empty($email)) {
            return redirect()->back()->with('error', 'Please fill in all required fields.');
        }

        $userId = $userModel->insert([
            'username'  => $username,
            'email'     => $email,
            'password'  => $password,
            'role'      => $role,
            'school_id' => $school_id, // save school_id
        ]);

        if (!$userId) {
            return redirect()->back()->with('error', 'Failed to create user.');
        }

        $existingFolder = $folderModel->where('folder_name', strtoupper($username))
                                      ->where('parent_folder_id', null)
                                      ->first();

        if ($existingFolder) {
            $mainFolderId = $existingFolder['id'];
        } else {
            $folderPath = $folderBasePath . strtoupper($username);
            if (!is_dir($folderPath)) mkdir($folderPath, 0777, true);

            $mainFolderId = $folderModel->insert([
                'folder_name'      => strtoupper($username),
                'parent_folder_id' => null,
            ]);
        }

        $userModel->update($userId, [
            'main_folder_id' => $mainFolderId,
            'main_folder'    => strtoupper($username),
        ]);

        return redirect()->to('/superadmin/manage_users')
                         ->with('success', "User '$username' added successfully and folder assigned.");
    }

    public function update($id)
    {
        $userModel = new UserModel();
        $folderModel = new FolderModel();

        $data = [
            'username'   => $this->request->getPost('username'),
            'email'      => $this->request->getPost('email'),
            'role'       => $this->request->getPost('role'),
            'school_id'  => trim($this->request->getPost('school_id')), // update school_id
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $newFolderId = $this->request->getPost('main_folder_id');
        if (!empty($newFolderId)) {
            $folder = (array) $folderModel->find($newFolderId);
            if (!empty($folder)) {
                $data['main_folder_id'] = $folder['id'] ?? null;
                $data['main_folder']    = $folder['folder_name'] ?? null;
            } else {
                return redirect()->back()->with('error', 'Selected folder not found.');
            }
        }

        $userModel->update($id, $data);

        return redirect()->to('/superadmin/manage_users')->with('success', 'User updated successfully.');
    }

    public function delete($id)
    {
        $db = \Config\Database::connect();
        $userModel = new UserModel();

        $db->table('file_requests')->where('user_id', $id)->delete();

        $userModel->delete($id);

        return redirect()->to('/superadmin/manage_users')
                         ->with('success', 'User and all related file requests deleted successfully.');
    }
}
