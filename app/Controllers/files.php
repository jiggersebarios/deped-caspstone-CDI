<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\FolderModel;
use App\Models\FileModel;
use App\Models\GlobalconfigModel;
use App\Models\CategoryModel;

class Files extends BaseController
{
    protected $configModel;
    protected $role;

    public function __construct()
    {
        $this->configModel = new GlobalconfigModel();
        $this->role = session()->get('role'); // 'admin' or 'superadmin'
    }
    

    // ROOT level (main folders)
public function index()
{
    $folderModel = new FolderModel();
    $search      = $this->request->getGet('search');
    $role        = $this->role;
    $session     = session();

    $viewFile = 'shared/files';

    if ($role === 'user') {
        $userFolderId = $session->get('main_folder_id');
        if (!$userFolderId) {
            return redirect()->to('/login')->with('error', 'No main folder assigned to your account.');
        }

        $query = $folderModel->where('parent_folder_id', $userFolderId);
        if ($search) $query = $query->like('folder_name', $search);

        $folders      = $query->findAll();
        $parentFolder = $folderModel->find($userFolderId);
    } else {
        $query = $folderModel
            ->groupStart()
                ->where('parent_folder_id', null)
                ->orWhere('parent_folder_id', 0)
            ->groupEnd();
        if ($search) $query = $query->like('folder_name', $search);

        $folders      = $query->findAll();
        $parentFolder = null;
    }

    // ✅ Load global config toggles
    $configModel = $this->configModel;
    $rawEnableUpload = $configModel->isEnabled('enable_file_uploads');
    $rawEnableEdit   = $configModel->isEnabled('enable_file_edit');
    $rawEnableDelete = $configModel->isEnabled('enable_file_delete');

    // ✅ Make them role-aware
    $enableUpload = ($role === 'user') ? $rawEnableUpload : 1;
    $enableEdit   = ($role === 'user') ? $rawEnableEdit   : 1;
    $enableDelete = ($role === 'user') ? $rawEnableDelete : 1;

    return view($viewFile, [
        'folders'            => $folders,
        'parentFolder'       => $parentFolder,
        'breadcrumb'         => [],
        'activeFiles'        => [],
        'archivedFiles'      => [],
        'expiredFiles'       => [],
        'depth'              => 1,
        'search'             => $search,
        'role'               => $role,
        'categories'         => (new CategoryModel())->findAll(),

        // Admin/Superadmin controls
        'canAddFolder'       => $this->isAllowed('allow_admin_to_add_folder'),
        'canDeleteFolder'    => $this->isAllowed('allow_admin_to_delete_folder'),
        'canAddSubfolder'    => $this->isAllowed('allow_admin_to_add_subfolder'),
        'canDeleteSubfolder' => $this->isAllowed('allow_admin_to_delete_subfolder'),

        // User controls
        'enableUpload'       => $enableUpload,
        'enableEdit'         => $enableEdit,
        'enableDelete'       => $enableDelete,
    ]);
}






    public function add()
    {
        if ($this->role === 'admin' && !$this->isAllowed('allow_admin_to_add_folder')) {
            return redirect()->back()->with('error', 'Adding folders is disabled by Superadmin.');
        }

        $folderName = trim($this->request->getPost('folder_name'));
        if (!$folderName) return redirect()->back()->with('error', 'Folder name is required');

        $model = new FolderModel();
        $exists = $model->where('folder_name', $folderName)->where('parent_folder_id', null)->first();
        if ($exists) return redirect()->back()->with('error', 'A main folder with that name already exists.');

        $model->insert(['folder_name' => $folderName, 'parent_folder_id' => null]);

        return redirect()->to($this->role . '/files')->with('success', 'Folder added successfully');
    }

    public function addSubfolder($parentId)
    {
        if ($this->role === 'admin' && !$this->isAllowed('allow_admin_to_add_subfolder')) {
            return redirect()->back()->with('error', 'Adding subfolders is disabled by Superadmin.');
        }

        $folderName = trim($this->request->getPost('folder_name'));
        if (!$folderName) return redirect()->back()->with('error', 'Subfolder name is required');

        $model = new FolderModel();
        $exists = $model->where('folder_name', $folderName)->where('parent_folder_id', $parentId)->first();
        if ($exists) return redirect()->back()->with('error', 'A subfolder with that name already exists in this folder.');

        $model->insert(['folder_name' => $folderName, 'parent_folder_id' => $parentId]);

        return redirect()->to($this->role . '/files/view/' . $parentId)->with('success', 'Subfolder added successfully');
    }

    // Deleting mainfolder 
public function delete()
{
    if ($this->role === 'admin' && !$this->isAllowed('allow_admin_to_delete_folder')) {
        return redirect()->back()->with('error', 'Deleting folders is disabled by Superadmin.');
    }

    $folderId = $this->request->getPost('delete_folder_id');
    $parentId = $this->request->getPost('parent_folder_id');

    if (!$folderId) {
        return redirect()->back()->with('error', 'Invalid folder delete request.');
    }

    $folderModel = new \App\Models\FolderModel();
    $userModel   = new \App\Models\UserModel();

    $folder = $folderModel->find($folderId);

    if (!$folder) {
        return redirect()->back()->with('error', 'Folder not found.');
    }

    // Check if folder is a main folder (no parent)
    $isMainFolder = empty($folder['parent_folder_id']);

    // If main folder, make sure no users are assigned to it
    if ($isMainFolder) {
        $assignedUsers = $userModel->where('main_folder_id', $folderId)->findAll();
        if (!empty($assignedUsers)) {
            return redirect()->back()->with('error', 'This folder is assigned to users. Reassign them first before deletion.');
        }
    }

    // Check if folder has subfolders
    $subfolders = $folderModel->where('parent_folder_id', $folderId)->findAll();
    if (!empty($subfolders)) {
        return redirect()->back()->with('error', 'This folder has subfolders. Delete them first.');
    }

    // Delete folder
    $folderModel->delete($folderId);

    $redirect = $isMainFolder
        ? $this->role . '/files'
        : $this->role . '/files/view/' . $parentId;

    return redirect()->to($redirect)->with('success', 'Folder deleted successfully.');
}


public function deleteSubfolder()
{
    if ($this->role === 'admin' && !$this->isAllowed('allow_admin_to_delete_subfolder')) {
        return redirect()->back()->with('error', 'Deleting subfolders is disabled by Superadmin.');
    }

    $folderId = $this->request->getPost('delete_folder_id');
    $parentId = $this->request->getPost('parent_folder_id');

    $model = new FolderModel();
    $folder = $model->find($folderId);

    // Use array syntax with null coalescing for safety
    if (!$folder || (($folder['parent_folder_id'] ?? null) != $parentId)) {
        return redirect()->back()->with('error', 'Invalid subfolder delete request');
    }

    $subfolders = $model->where('parent_folder_id', $folderId)->findAll();
    if (!empty($subfolders)) {
        return redirect()->back()->with('error', 'This subfolder has child folders. Delete them first.');
    }

    $model->delete($folderId);
    return redirect()->to($this->role . '/files/view/' . $parentId)->with('success', 'Subfolder deleted successfully');
}


public function view($id)
{
    $folderModel   = new FolderModel();
    $fileModel     = new FileModel();
    $categoryModel = new CategoryModel();
    $configModel   = $this->configModel;
    $session       = session();
    $role          = $session->get('role');

    $folder = $folderModel->find($id);
    if (!$folder) {
        return redirect()->to($this->role . '/files')->with('error', 'Folder not found.');
    }

    // Restrict normal users to their own main folder and its subfolders
    if ($role === 'user') {
        $userFolderId = $session->get('main_folder_id');

        if (!$userFolderId) {
            return redirect()->to('/login')->with('error', 'No main folder assigned to your account.');
        }

        if (!$this->isFolderWithinUserScope($id, $userFolderId, $folderModel)) {
            return redirect()->to('/user/files')->with('error', 'You are not authorized to view this folder.');
        }
    }

    // Build breadcrumb
    $breadcrumb = $this->buildBreadcrumb($id);
    $depth      = count($breadcrumb);

    // Auto-update archive and expiry statuses
    $fileModel->autoArchiveAndExpire();

    // Load files and subfolders
    $activeFiles   = $fileModel->getActiveFilesByFolder($id);
    $archivedFiles = $fileModel->getArchivedFilesByFolder($id);
    $expiredFiles  = $fileModel->where('folder_id', $id)->where('status', 'expired')->findAll();
    $subfolders    = $folderModel->where('parent_folder_id', $id)->findAll();

    // Load global config toggles
    $rawEnableUpload = $configModel->isEnabled('enable_file_uploads');
    $rawEnableEdit   = $configModel->isEnabled('enable_file_edit');
    $rawEnableDelete = $configModel->isEnabled('enable_file_delete');

    // Role-aware
    $enableUpload = ($role === 'user') ? $rawEnableUpload : 1;
    $enableEdit   = ($role === 'user') ? $rawEnableEdit   : 1;
    $enableDelete = ($role === 'user') ? $rawEnableDelete : 1;

    return view('shared/files', [
        'folders'            => $subfolders,
        'parentFolder'       => $folder,
        'breadcrumb'         => $breadcrumb,
        'activeFiles'        => $activeFiles,
        'archivedFiles'      => $archivedFiles,
        'expiredFiles'       => $expiredFiles,
        'depth'              => $depth,
        'categories'         => $categoryModel->findAll(),
        'role'               => $role,

        // Admin controls
        'canAddFolder'       => $this->isAllowed('allow_admin_to_add_folder'),
        'canDeleteFolder'    => $this->isAllowed('allow_admin_to_delete_folder'),
        'canAddSubfolder'    => $this->isAllowed('allow_admin_to_add_subfolder'),
        'canDeleteSubfolder' => $this->isAllowed('allow_admin_to_delete_subfolder'),

        // User controls
        'enableUpload'       => $enableUpload,
        'enableEdit'         => $enableEdit,
        'enableDelete'       => $enableDelete,
    ]);
}


public function upload($folderId)
{
    $file = $this->request->getFile('upload_file');
    $categoryId = $this->request->getPost('category_id');
    $session = session();

    if (!$file || !$file->isValid()) {
        return redirect()->back()->with('error', 'Invalid file upload.');
    }
    if (!$categoryId) {
        return redirect()->back()->with('error', 'Please select a category.');
    }

    // Determine initial status (pending by default)
    $status = 'pending'; // You can also allow admin to set it to 'active'

    // Map status to folder path
    $statusFolders = [
        'pending'  => FCPATH . 'uploads/pending/',
        'active'   => FCPATH . 'uploads/active/',
        'archived' => FCPATH . 'uploads/archive/',
        'expired'  => FCPATH . 'uploads/expired/'
    ];

    $destFolder = $statusFolders[$status];

    // Ensure folder exists
    if (!is_dir($destFolder)) {
        mkdir($destFolder, 0777, true);
    }

    // Generate random file name to prevent conflicts
    $newName = $file->getRandomName();
    $file->move($destFolder, $newName);

    $fileSize = $file->getSize();

    // Insert into database
    (new \App\Models\FileModel())->insert([
        'folder_id'   => $folderId,
        'category_id' => $categoryId,
        'file_name'   => $file->getClientName(),
        'file_path'   => str_replace(FCPATH, '', $destFolder . $newName),
        'file_size'   => $fileSize,
        'uploaded_by' => $session->get('id'),
        'uploaded_at' => date('Y-m-d H:i:s'),
        'status'      => $status,
        'is_archived' => 0
    ]);

    return redirect()
        ->to($this->role . '/files/view/' . $folderId)
        ->with('success', 'File uploaded successfully. File is pending for review.');
}


public function viewFile($id)
{
    $fileModel = new \App\Models\FileModel();
    $file = $fileModel->find($id);

    if (!$file) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException("File not found");
    }

    $filePath = FCPATH . $file['file_path'];

    if (!file_exists($filePath)) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException("File does not exist on server");
    }

    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

    // ✅ 1. Show PDFs or Images inline
    if (in_array($extension, ['pdf', 'jpg', 'jpeg', 'png', 'gif'])) {
        return $this->response
            ->setHeader('Content-Type', mime_content_type($filePath))
            ->setHeader('Content-Disposition', 'inline; filename="' . basename($filePath) . '"')
            ->setBody(file_get_contents($filePath));
    }

    // ✅ 2. DOCX → use shared/docx_viewer.php
    if ($extension === 'docx') {
        $data = [
            'fileUrl' => base_url($file['file_path']),
            'fileName' => $file['file_name']
        ];
        return view('shared/docx_viewer', $data);
    }

    // ✅ 3. XLSX → use shared/xlsx_viewer.php
    if ($extension === 'xlsx') {
        $data = [
            'fileUrl' => base_url($file['file_path']),
            'fileName' => $file['file_name']
        ];
        return view('shared/xlsx_viewer', $data);
    }

    // ✅ 4. Other file types → download
    return $this->response
        ->download($filePath, null)
        ->setFileName(basename($filePath));
}


public function deleteFile($fileId)
{
    $fileModel = new FileModel();
    $file = $fileModel->find($fileId);

    if (!$file) {
        return redirect()->back()->with('error', 'File not found in database.');
    }

    // ✅ Correct path - use FCPATH since files are in /public/uploads/
    $filePath = FCPATH . $file['file_path'];

    // ✅ Check and delete the actual file from disk
    if (is_file($filePath)) {
        if (!unlink($filePath)) {
            return redirect()->back()->with('error', 'Failed to delete the file from server.');
        }
    } else {
        // Optional: log if the file is missing from folder
        log_message('warning', 'File missing at delete: ' . $filePath);
    }

    // ✅ Remove from database
    $fileModel->delete($fileId);

    return redirect()
        ->to($this->role . '/files/view/' . $file['folder_id'])
        ->with('success', 'File deleted successfully.');
}



public function download($fileId)
{
    $fileModel = new FileModel();
    $file = $fileModel->find($fileId);

    if ($file) {
        // ✅ Use FCPATH since files are in /public/uploads/
        $filePath = FCPATH . $file['file_path']; 

        if (is_file($filePath)) {
            return $this->response->download($filePath, null)
                                  ->setFileName($file['file_name']);
        }

        // Optional debug line to trace path issues
        // log_message('error', 'Download failed. File not found: ' . $filePath);
    }

    return redirect()->back()->with('error', 'File not found.');
}

public function renameFile()
{
    $fileId  = $this->request->getPost('file_id');
    $newName = trim($this->request->getPost('new_name'));

    if (empty($fileId) || empty($newName)) {
        return redirect()->back()->with('error', 'Missing parameters.');
    }

    $fileModel = new \App\Models\FileModel();
    $file = $fileModel->find($fileId);

    if (!$file) {
        return redirect()->back()->with('error', 'File not found.');
    }

    // Normalize access for model results that may be returned as array or object
    $status = is_array($file) ? ($file['status'] ?? null) : ($file->status ?? null);
    $fileName = is_array($file) ? ($file['file_name'] ?? '') : ($file->file_name ?? '');

    // Prevent renaming archived or expired files
    if (in_array($status, ['archived', 'expired'])) {
        return redirect()->back()->with('error', 'Archived or expired files are immutable and cannot be renamed.');
    }

    $extension = pathinfo($fileName, PATHINFO_EXTENSION);
    $newBaseName = pathinfo($newName, PATHINFO_FILENAME);
    $newBaseName = preg_replace('/[^A-Za-z0-9_\- ]/', '', $newBaseName);

    if (empty($newBaseName)) {
        return redirect()->back()->with('error', 'Invalid file name.');
    }

    $finalName = $newBaseName . '.' . $extension;

    try {
        $fileModel->update($fileId, ['file_name' => $finalName]);
        return redirect()->back()->with('success', 'File renamed successfully.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to rename file.');
    }
}





    private function buildBreadcrumb($id)
    {
        $model = new FolderModel();
        $path  = [];

        while ($id) {
            $folder = $model->find($id);
            if ($folder) {
                $path[] = $folder;
                $id = $folder['parent_folder_id'];
            } else {
                $id = null;
            }
        }

        return array_reverse($path);
    }

    private function isAllowed($settingKey)
    {
        $config = $this->configModel->where('setting_key', $settingKey)->first();
        return $config && $config['setting_value'] == 1;
    }

    // Check if a folder is within the user's allowed folder hierarchy
private function isFolderWithinUserScope($targetFolderId, $userFolderId, $folderModel)
{
    // ✅ Direct access to their main folder
    if ($targetFolderId == $userFolderId) {
        return true;
    }

    // ✅ Check if target folder is a subfolder (or deeper descendant)
    $subfolders = $folderModel->where('parent_folder_id', $userFolderId)->findAll();

    foreach ($subfolders as $subfolder) {
        if ($subfolder['id'] == $targetFolderId) {
            return true;
        }

        // 🔁 Recursively check deeper levels
        if ($this->isFolderWithinUserScope($targetFolderId, $subfolder['id'], $folderModel)) {
            return true;
        }
    }

    return false;
}



}
