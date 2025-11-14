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
    protected $storagePath;

    public function __construct()
    {
        $this->configModel = new GlobalconfigModel();
        $this->role = session()->get('role'); // 'admin' or 'superadmin'
         // safer storage outside public
        $this->storagePath = 'C:\\xampp\\depedfiles\\'; // Windows path example
        // Load upload settings from global config
        $this->uploadSettings = $this->configModel->getSystemUploadSettings();

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

        $folders = $query->orderBy('folder_name', 'ASC')->findAll();

        $parentFolder = $folderModel->find($userFolderId);
    } else {
        $query = $folderModel
            ->groupStart()
                ->where('parent_folder_id', null)
                ->orWhere('parent_folder_id', 0)
            ->groupEnd();
        if ($search) $query = $query->like('folder_name', $search);

       $folders = $query->orderBy('folder_name', 'ASC')->findAll();

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
        'canAddFolder'       => $this->isAllowed('allow_add_folder'),
        'canEditFolder'      => $this->isAllowed('allow_edit_folder'),
        'canDeleteFolder'    => $this->isAllowed('allow_delete_folder'),
        'canAddSubfolder'    => $this->isAllowed('allow_add_subfolder'),
        'canEditSubfolder'   => $this->isAllowed('allow_edit_subfolder'),
        'canDeleteSubfolder' => $this->isAllowed('allow_delete_subfolder'),

        // User controls
        'enableUpload'       => $enableUpload,
        'enableEdit'         => $enableEdit,
        'enableDelete'       => $enableDelete,
    ]);
}

public function view($id)
{
    // ==== Initialize Models ====
    $folderModel   = new FolderModel();
    $fileModel     = new FileModel();
    $categoryModel = new CategoryModel();
    $configModel   = $this->configModel;
    $session       = session();
    $role          = $session->get('role');

    // ==== Get search query from GET ====
    $search = $this->request->getGet('search');

    // ==== Check if Folder Exists ====
    $folder = $folderModel->find($id);
    if (!$folder) {
        return redirect()->to($this->role . '/files')
            ->with('error', 'Folder not found.');
    }

    // ==== Restrict Normal Users to Their Own Main Folder/Subfolders ====
    if ($role === 'user') {
        $userFolderId = $session->get('main_folder_id');

        if (!$userFolderId) {
            return redirect()->to('/login')
                ->with('error', 'No main folder assigned to your account.');
        }

        // Ensure the user can only access folders under their main folder
        if (!$this->isFolderWithinUserScope($id, $userFolderId, $folderModel)) {
            return redirect()->to('/user/files')
                ->with('error', 'You are not authorized to view this folder.');
        }
    }

    // ==== Breadcrumb (for Navigation Path) ====
    $breadcrumb = $this->buildBreadcrumb($id);
    $depth      = count($breadcrumb);

    // ==== Auto Update Archive and Expiry Statuses ====
    $fileModel->autoArchiveAndExpire();

    // ==== Load Files (Depth 3 Only) ====
    $activeFiles = $archivedFiles = $expiredFiles = [];
    if ($depth === 3) {
        // Pass search term to each method
        $activeFiles   = $fileModel->getActiveFilesByFolder($id, $search);
        $archivedFiles = $fileModel->getArchivedFilesByFolder($id, $search);
        $expiredFiles  = $fileModel->getExpiredFilesByFolder($id, $search);

    }

    // ==== Load Subfolders with Search Applied ====
    $subfoldersQuery = $folderModel->where('parent_folder_id', $id);
    if ($search) {
        $subfoldersQuery->like('folder_name', $search);
    }
    $subfolders = $subfoldersQuery->findAll();

    // ==== Custom Sort Subfolders (Year-Range First, Then Alphabetical) ====
    usort($subfolders, function($a, $b) {
        $pattern = '/(\d{4})-(\d{4})/'; // Matches year ranges like 2023-2025

        $aMatch = [];
        $bMatch = [];

        preg_match($pattern, $a['folder_name'], $aMatch);
        preg_match($pattern, $b['folder_name'], $bMatch);

        if ($aMatch && $bMatch) {
            return (int)$bMatch[2] <=> (int)$aMatch[2]; // latest year first
        } elseif ($aMatch) {
            return -1; // a comes first
        } elseif ($bMatch) {
            return 1; // b comes first
        } else {
            return strcasecmp($a['folder_name'], $b['folder_name']); // alphabetical
        }
    });

    // ==== Load Global Config Toggles ====
    $rawEnableUpload = $configModel->isEnabled('enable_file_uploads');
    $rawEnableEdit   = $configModel->isEnabled('enable_file_edit');
    $rawEnableDelete = $configModel->isEnabled('enable_file_delete');

    // ==== Apply Role-Based Controls ====
    $enableUpload = ($role === 'user') ? $rawEnableUpload : 1;
    $enableEdit   = ($role === 'user') ? $rawEnableEdit   : 1;
    $enableDelete = ($role === 'user') ? $rawEnableDelete : 1;

    // ==== Render View ====
    return view('shared/files', [
        'folders'            => $subfolders,
        'parentFolder'       => $folder,
        'breadcrumb'         => $breadcrumb,
        'depth'              => $depth,
        'search'             => $search,
        'activeFiles'        => $activeFiles,
        'archivedFiles'      => $archivedFiles,
        'expiredFiles'       => $expiredFiles,
        'categories'         => $categoryModel->findAll(),
        'role'               => $role,

        // Admin Permissions
        'canAddFolder'       => $this->isAllowed('allow_add_folder'),
        'canEditFolder'      => $this->isAllowed('allow_edit_folder'),
        'canDeleteFolder'    => $this->isAllowed('allow_delete_folder'),
        'canAddSubfolder'    => $this->isAllowed('allow_add_subfolder'),
        'canEditSubfolder'   => $this->isAllowed('allow_edit_subfolder'),
        'canDeleteSubfolder' => $this->isAllowed('allow_delete_subfolder'),

        // User Permissions
        'enableUpload'       => $enableUpload,
        'enableEdit'         => $enableEdit,
        'enableDelete'       => $enableDelete,
    ]);
}



    public function add()
    {
        if ($this->role === 'admin' && !$this->isAllowed('allow_add_folder')) {
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
        if ($this->role === 'admin' && !$this->isAllowed('allow_add_subfolder')) {
            return redirect()->back()->with('error', 'Adding subfolders is disabled by Superadmin.');
        }

        $folderName = trim($this->request->getPost('folder_name'));
        if (!$folderName) return redirect()->back()->with('error', 'Subfolder name is required');

        $model = new FolderModel();
        $exists = $model->where('folder_name', $folderName)->where('parent_folder_id', $parentId)->first();
        if ($exists) return redirect()->back()->with('error', 'A subfolder with that name already exists in this folder.');

        $model->insert(['folder_name' => $folderName, 'parent_folder_id' => $parentId]);

        return redirect()->to($this->role . '/files/view/' . $parentId);
    }

    // Deleting mainfolder 
public function delete()
{
    if ($this->role === 'admin' && !$this->isAllowed('allow_delete_folder')) {
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

    return redirect()->to($redirect);
}


// Deleting subfolder
public function deleteSubfolder()
{
    if ($this->role === 'admin' && !$this->isAllowed('allow_delete_subfolder')) {
        return redirect()->back()->with('error', 'Deleting subfolders is disabled by Superadmin.');
    }

    $folderId = $this->request->getPost('delete_folder_id');
    $parentId = $this->request->getPost('parent_folder_id');

    $folderModel = new FolderModel();
    $fileModel   = new \App\Models\FileModel();

    $folder = $folderModel->find($folderId);

    if (!$folder || (($folder['parent_folder_id'] ?? null) != $parentId)) {
        return redirect()->back()->with('error', 'Invalid subfolder delete request.');
    }

    // ---- Check if it has child subfolders (still not allowed) ----
    $subfolders = $folderModel->where('parent_folder_id', $folderId)->findAll();
    if (!empty($subfolders)) {
        return redirect()->back()->with('error', 'This subfolder contains child folders. Delete them first.');
    }

    // ---- NEW: Automatically delete all files inside this subfolder ----
    $files = $fileModel->where('folder_id', $folderId)->findAll();

    foreach ($files as $file) {

        $absolutePath = $this->storagePath . $file['file_path'];

        // Delete physical file if exists
        if (is_file($absolutePath)) {
            @unlink($absolutePath);
        }

        // Remove DB record
        $fileModel->delete($file['id']);
    }

    // ---- Finally delete the subfolder ----
    $folderModel->delete($folderId);

    return redirect()
        ->to($this->role . '/files/view/' . $parentId)
        ->with('success', 'Subfolder and all files inside were deleted successfully.');
}

public function renameMainFolder()
{
    $folderModel = new \App\Models\FolderModel();
    $userModel   = new \App\Models\UserModel();
    $configModel = $this->configModel;

    // Check permission
    if ($this->role === 'admin' && !$configModel->isEnabled('allow_edit_folder')) {
        return redirect()->back()->with('error', 'Editing main folders is disabled by Superadmin.');
    }

    $folderId   = $this->request->getPost('folder_id');
    $newName    = trim($this->request->getPost('new_name'));

    if (empty($folderId) || empty($newName)) {
        return redirect()->back()->with('error', 'Folder ID and new name are required.');
    }

    $folder = $folderModel->find($folderId);
    if (!$folder || !empty($folder['parent_folder_id'])) {
        return redirect()->back()->with('error', 'Invalid main folder.');
    }

    // Check for name conflict
    $existing = $folderModel
        ->where('parent_folder_id', null)
        ->where('folder_name', $newName)
        ->where('id !=', $folderId)
        ->first();

    if ($existing) {
        return redirect()->back()->with('error', 'A main folder with this name already exists.');
    }

    // Update DB folder name
    $folderModel->update($folderId, ['folder_name' => $newName]);

    // Update all users who have this main folder
    $users = $userModel->where('main_folder_id', $folderId)->findAll();
    foreach ($users as $user) {
        $userModel->update($user['id'], ['main_folder' => $newName]);
    }

    return redirect()->back()->with('success', 'Main folder renamed successfully.');
}

public function renameSubfolder()
{
    // Check permission using your global config
    if ($this->role === 'admin' && !$this->isAllowed('allow_edit_subfolder')) {
        return redirect()->back()->with('error', 'Renaming subfolders is disabled by Superadmin.');
    }

    $folderModel = new \App\Models\FolderModel();

    $folderId   = $this->request->getPost('folder_id');
    $newName    = trim($this->request->getPost('new_name'));
    $parentId   = $this->request->getPost('parent_folder_id');

    // Validate
    if (empty($folderId) || empty($newName)) {
        return redirect()->back()->with('error', 'Folder name cannot be empty.');
    }

    // Get the folder
    $folder = $folderModel->find($folderId);
    if (!$folder) {
        return redirect()->back()->with('error', 'Folder not found.');
    }

    // Prevent naming conflicts in the same parent
    $existing = $folderModel
        ->where('parent_folder_id', $folder['parent_folder_id'])
        ->where('folder_name', $newName)
        ->where('id !=', $folderId)
        ->first();

    if ($existing) {
        return redirect()->back()->with('error', 'A subfolder with this name already exists.');
    }

    // Update the folder name
    $folderModel->update($folderId, [
        'folder_name' => $newName
    ]);

    return redirect()
        ->to($this->role . '/files/view/' . $parentId)
        ->with('success', 'Subfolder renamed successfully.');
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

    // ✅ Load upload settings from global config
    $uploadSettings = $this->configModel->getSystemUploadSettings();

    // ✅ Check file type
    $extension = strtolower($file->getClientExtension());
    $allowedExtensions = [];
    foreach ($uploadSettings as $key => $setting) {
        if (strpos($key, 'allow_') === 0 && $setting['enabled'] == 1) {
            $allowedExtensions[] = str_replace('allow_', '', $key);
        }
    }

    if (!in_array($extension, $allowedExtensions)) {
        return redirect()->back()->with('error', "File type .$extension is not allowed.");
    }

    // ✅ Check file size
    $maxSizeMB = $uploadSettings['max_file_size_mb']['value'] ?? 5;
    $maxSizeBytes = $maxSizeMB * 1024 * 1024;
    if ($file->getSize() > $maxSizeBytes) {
        return redirect()->back()->with('error', "File exceeds max size of {$maxSizeMB} MB.");
    }

    // ✅ Determine initial status (pending by default)
    $status = 'pending'; // You can also allow admin to set it to 'active'

    // Map status to folder path
    $statusFolders = [
        'pending'  => $this->storagePath . 'pending\\',
        'active'   => $this->storagePath . 'active\\',
        'archived' => $this->storagePath . 'archive\\',
        'expired'  => $this->storagePath . 'expired\\'
    ];

    $destFolder = $statusFolders[$status];

    // Ensure folder exists
    if (!is_dir($destFolder)) {
        mkdir($destFolder, 0777, true);
    }

    $newName = $file->getRandomName();
    $file->move($destFolder, $newName);

    // Save relative path in DB for portability
    $filePathRelative = str_replace($this->storagePath, '', $destFolder . $newName);

    (new \App\Models\FileModel())->insert([
        'folder_id'   => $folderId,
        'category_id' => $categoryId,
        'file_name'   => $file->getClientName(),
        'file_path'   => $filePathRelative,
        'file_size'   => $file->getSize(),
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

   $filePath = $this->storagePath . $file['file_path'];

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
    $deletedFileModel = new \App\Models\DeletedFileModel();
    $categoryModel = new \App\Models\CategoryModel();

    // 🔹 Find the file first
    $file = $fileModel->find($fileId);
    if (!$file) {
        return redirect()->back()->with('error', 'File not found in database.');
    }

    // 🔹 Get category info (optional)
    $category = $categoryModel->find($file['category_id']);

    // 🔹 Delete physical file from server
    $filePath = FCPATH . $file['file_path'];
    if (is_file($filePath)) {
        if (!unlink($filePath)) {
            log_message('error', '❌ Failed to delete file: ' . $filePath);
            return redirect()->back()->with('error', 'Failed to delete the file from server.');
        }
    } else {
        log_message('warning', '⚠️ File missing when deleting: ' . $filePath);
    }

    // 🔹 Delete from all dependent tables first (to avoid FK errors)
    $db = \Config\Database::connect();
    $db->table('request_tokens')->whereIn('request_id', function($builder) use ($fileId) {
        $builder->select('id')->from('file_requests')->where('file_id', $fileId);
    })->delete();

    $db->table('file_requests')->where('file_id', $fileId)->delete();

    // 🔹 Log deletion details into deleted_files
    $deletedFileModel->insert([
        'file_id'        => $file['id'],
        'file_name'      => $file['file_name'],
        'category_name'  => $category['category_name'] ?? 'Uncategorized',
        'expired_at'     => $file['expired_at'] ?? null,
        'deleted_by'     => session()->get('id') ?? null,
        'deleted_at'     => date('Y-m-d H:i:s'),
        'reason'         => 'Manual deletion by user',
    ]);

    // 🔹 Delete the file record from the main table
    $fileModel->delete($fileId);

    return redirect()
        ->to($this->role . '/files/view/' . $file['folder_id'])
        ->with('success', 'File deleted successfully and logged in deleted files.');
}



public function getDeletedFiles($role = null)
{
    $deletedFileModel = new \App\Models\DeletedFileModel();

    $deletedFiles = $deletedFileModel
        ->select('deleted_files.*, users.username AS deleted_by_name')
        ->join('users', 'users.id = deleted_files.deleted_by', 'left')
        ->orderBy('deleted_files.deleted_at', 'DESC')
        ->findAll();

    return $this->response->setJSON($deletedFiles);
}




public function download($fileId)
{
    $fileModel = new \App\Models\FileModel();
    $file = $fileModel->find($fileId);

    if (!$file) {
        return redirect()->back()->with('error', 'File not found in database.');
    }

    // Absolute path to the storage folder (outside public)
    $storagePath = 'C:\\xampp\\depedfiles\\'; // adjust according to your setup
    $filePath = $storagePath . $file['file_path']; // file_path is relative in DB

    if (!is_file($filePath)) {
        log_message('error', 'Download failed. File missing on server: ' . $filePath);
        return redirect()->back()->with('error', 'File not found on the server.');
    }

    // Serve the file for download
    return $this->response
        ->download($filePath, null, true)
        ->setFileName($file['file_name']);
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

public function restore($id)
{
    $fileModel = new \App\Models\FileModel();

    if ($fileModel->restoreFile($id)) {
        return redirect()->back()->with('success', 'File has been successfully restored and reactivated.');
    }

    return redirect()->back()->with('error', 'Failed to restore the file.');
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
