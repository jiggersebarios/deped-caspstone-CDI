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
        $search = $this->request->getGet('search');

        if ($search) {
            $folders = $folderModel->like('folder_name', $search)
                                   ->where('parent_folder_id', null)
                                   ->orWhere('parent_folder_id', 0)
                                   ->findAll();
        } else {
            $folders = $folderModel->where('parent_folder_id', null)
                                   ->orWhere('parent_folder_id', 0)
                                   ->findAll();
        }

        $viewMode = $this->request->getGet('view') ?? 'grid';
        $viewFile = 'shared/files'; // unified shared view

        return view($viewFile, [
            'folders'            => $folders,
            'parentFolder'       => null,
            'breadcrumb'         => [],
            'files'              => [],
            'depth'              => 1,
            'search'             => $search,
            'role'               => $this->role,
            'canAddFolder'       => $this->isAllowed('allow_admin_to_add_folder'),
            'canDeleteFolder'    => $this->isAllowed('allow_admin_to_delete_folder'),
            'canAddSubfolder'    => $this->isAllowed('allow_admin_to_add_subfolder'),
            'canDeleteSubfolder' => $this->isAllowed('allow_admin_to_delete_subfolder'),
            'categories'         => (new CategoryModel())->findAll(),
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

    public function delete()
    {
        if ($this->role === 'admin' && !$this->isAllowed('allow_admin_to_delete_folder')) {
            return redirect()->back()->with('error', 'Deleting folders is disabled by Superadmin.');
        }

        $folderId = $this->request->getPost('delete_folder_id');
        $parentId = $this->request->getPost('parent_folder_id');

        if ($folderId) {
            (new FolderModel())->delete($folderId);
            $redirect = $parentId ? $this->role . '/files/view/' . $parentId : $this->role . '/files';
            return redirect()->to($redirect)->with('success', 'Folder deleted successfully');
        }

        return redirect()->back()->with('error', 'Invalid request');
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

        if (!$folder || $folder['parent_folder_id'] != $parentId) {
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

        $folder = $folderModel->find($id);
        if (!$folder) return redirect()->to($this->role . '/files')->with('error', 'Folder not found');

        $breadcrumb = $this->buildBreadcrumb($id);
        $depth      = count($breadcrumb);
        $files      = $depth >= 3 ? $fileModel->getFilesWithDetails($id) : [];

        return view('shared/files', [
            'folders'            => $folderModel->where('parent_folder_id', $id)->findAll(),
            'parentFolder'       => $folder,
            'breadcrumb'         => $breadcrumb,
            'files'              => $files,
            'depth'              => $depth,
            'categories'         => $categoryModel->findAll(),
            'role'               => $this->role,
            'canAddFolder'       => $this->isAllowed('allow_admin_to_add_folder'),
            'canDeleteFolder'    => $this->isAllowed('allow_admin_to_delete_folder'),
            'canAddSubfolder'    => $this->isAllowed('allow_admin_to_add_subfolder'),
            'canDeleteSubfolder' => $this->isAllowed('allow_admin_to_delete_subfolder'),
        ]);
    }

    public function upload($folderId)
    {
        $file = $this->request->getFile('upload_file');
        $categoryId = $this->request->getPost('category_id');
        $session = session();

        if (!$file || !$file->isValid()) return redirect()->back()->with('error', 'Invalid file upload.');
        if (!$categoryId) return redirect()->back()->with('error', 'Please select a category.');

        $newName = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads/files', $newName);

        (new FileModel())->insert([
            'folder_id'   => $folderId,
            'category_id' => $categoryId,
            'file_name'   => $file->getClientName(),
            'file_path'   => $newName,
            'uploaded_by' => $session->get('id'),
            'uploaded_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to($this->role . '/files/view/' . $folderId)->with('success', 'File uploaded successfully.');
    }

    public function deleteFile($fileId)
    {
        $fileModel = new FileModel();
        $file      = $fileModel->find($fileId);
        if (!$file) return redirect()->back()->with('error', 'File not found.');

        $filePath = WRITEPATH . 'uploads/files/' . $file['file_path'];
        if (file_exists($filePath)) unlink($filePath);

        $fileModel->delete($fileId);
        return redirect()->to($this->role . '/files/view/' . $file['folder_id'])->with('success', 'File deleted successfully.');
    }

    public function download($fileId)
    {
        $fileModel = new FileModel();
        $file = $fileModel->find($fileId);

        if ($file) {
            $filePath = WRITEPATH . 'uploads/files/' . $file['file_path'];
            if (is_file($filePath)) return $this->response->download($filePath, null)->setFileName($file['file_name']);
        }

        return redirect()->back()->with('error', 'File not found.');
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
}
