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
    $data['folders'] = $folderModel->findAll(); // ✅ Added so we can show folder list
    $data['title'] = 'Manage Users';

    return view('superadmin/manage_users', $data);
}

    public function store()
    {
        $userModel = new UserModel();
        $folderModel = new FolderModel();

        $folderBasePath = FCPATH . 'uploads/';

        $username = trim($this->request->getPost('username'));
        $email = trim($this->request->getPost('email'));
        $password = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        $role = $this->request->getPost('role');

        // ✅ Validation
        if (empty($username) || empty($email)) {
            return redirect()->back()->with('error', 'Please fill in all required fields.');
        }

        // ✅ Create user first
        $userId = $userModel->insert([
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'role' => $role,
        ]);

        if (!$userId) {
            return redirect()->back()->with('error', 'Failed to create user.');
        }

        // ✅ Check if folder already exists
        $existingFolder = $folderModel->where('folder_name', strtoupper($username))
                                      ->where('parent_folder_id', null)
                                      ->first();

        // ✅ If folder exists, use it — else create new
        if ($existingFolder) {
            $mainFolderId = $existingFolder['id'];
        } else {
            $folderPath = $folderBasePath . strtoupper($username);

            if (!is_dir($folderPath)) {
                mkdir($folderPath, 0777, true);
            }

            $mainFolderId = $folderModel->insert([
                'folder_name' => strtoupper($username),
                'parent_folder_id' => null,
            ]);
        }

        // ✅ Update user with main folder details
        $userModel->update($userId, [
            'main_folder_id' => $mainFolderId,
            'main_folder'    => strtoupper($username),
        ]);

        return redirect()->to('/superadmin/manage_users')
            ->with('success', "User '$username' added successfully and folder assigned.");
    }


    //edit
public function update($id)
{
    $userModel = new UserModel();
    $folderModel = new FolderModel();

    $data = [
        'username' => $this->request->getPost('username'),
        'email'    => $this->request->getPost('email'),
        'role'     => $this->request->getPost('role'),
    ];

    // ✅ Optional password update
    $password = $this->request->getPost('password');
    if (!empty($password)) {
        $data['password'] = password_hash($password, PASSWORD_DEFAULT);
    }

    // ✅ Handle folder reassignment (optional)
    $newFolderId = $this->request->getPost('main_folder_id');
    if (!empty($newFolderId)) {
        // Explicitly cast to array to avoid IDE/analysis confusion
        $folder = (array) $folderModel->find($newFolderId);

        if (!empty($folder)) {
            $data['main_folder_id'] = $folder['id'] ?? null;
            $data['main_folder']    = $folder['folder_name'] ?? null;
        } else {
            return redirect()->back()->with('error', 'Selected folder not found.');
        }
    }

    // ✅ Update user record
    $userModel->update($id, $data);

    return redirect()->to('/superadmin/manage_users')->with('success', 'User updated successfully.');
}



    public function delete($id)
    {
        $userModel = new UserModel();
        $userModel->delete($id);
        return redirect()->to('/superadmin/manage_users')->with('success', 'User deleted successfully.');
    }
}
