<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FolderModel;
use App\Models\GlobalconfigModel;

class Files extends BaseController
{
    protected $configModel;

    public function __construct()
    {
        $this->configModel = new GlobalconfigModel();
    }

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

        // Pass configs to view
        $data = [
            'folders' => $folders,
            'canAddFolder'       => $this->isAllowed('allow_add_folder'),
            'canDeleteFolder'    => $this->isAllowed('allow_delete_folder'),
            'canAddSubfolder'    => $this->isAllowed('allow_admin_add_subfolder'),
            'canDeleteSubfolder' => $this->isAllowed('allow_admin_delete_subfolder'),
        ];

        return view('admin/files', $data);
    }

    public function add()
    {
        if (!$this->isAllowed('allow_add_folder')) {
            return redirect()->back()->with('error', 'Adding folders is disabled by Superadmin.');
        }

        $model = new FolderModel();
        $folderName = trim($this->request->getPost('folder_name'));

        if (!$folderName) {
            return redirect()->back()->with('error', 'Folder name is required');
        }

        $exists = $model->where('folder_name', $folderName)
                        ->where('parent_folder_id', null)
                        ->first();

        if ($exists) {
            return redirect()->back()->with('error', 'A main folder with that name already exists.');
        }

        $model->insert([
            'folder_name'      => $folderName,
            'parent_folder_id' => null
        ]);

        return redirect()->to('/admin/files')->with('success', 'Folder added successfully');
    }

    public function addSubfolder($parentId)
    {
        if (!$this->isAllowed('allow_admin_add_subfolder')) {
            return redirect()->back()->with('error', 'Adding subfolders is disabled by Superadmin.');
        }

        $model = new FolderModel();
        $folderName = trim($this->request->getPost('folder_name'));

        if (!$folderName) {
            return redirect()->back()->with('error', 'Subfolder name is required');
        }

        $exists = $model->where('folder_name', $folderName)
                        ->where('parent_folder_id', $parentId)
                        ->first();

        if ($exists) {
            return redirect()->back()->with('error', 'A subfolder with that name already exists in this folder.');
        }

        $model->insert([
            'folder_name'      => $folderName,
            'parent_folder_id' => $parentId
        ]);

        return redirect()->to('/admin/files/view/' . $parentId)
                         ->with('success', 'Subfolder added successfully');
    }

    public function delete()
    {
        if (!$this->isAllowed('allow_delete_folder')) {
            return redirect()->back()->with('error', 'Deleting folders is disabled by Superadmin.');
        }

        $model = new FolderModel();
        $folderId = $this->request->getPost('delete_folder_id');
        $parentId = $this->request->getPost('parent_folder_id');

        if ($folderId) {
            $model->delete($folderId);

            if ($parentId) {
                return redirect()->to('/admin/files/view/' . $parentId)
                                 ->with('success', 'Subfolder deleted successfully');
            }

            return redirect()->to('/admin/files')->with('success', 'Folder deleted successfully');
        }

        return redirect()->back()->with('error', 'Invalid request');
    }

  public function deleteSubfolder()
{
    if (!$this->isAllowed('allow_admin_delete_subfolder')) {
        return redirect()->back()->with('error', 'Deleting subfolders is disabled by Superadmin.');
    }

    $model = new FolderModel();
    $folderId = $this->request->getPost('delete_folder_id');
    $parentId = $this->request->getPost('parent_folder_id');

    if (!$folderId) {
        return redirect()->back()->with('error', 'No subfolder selected.');
    }

    // Cast the result of find() to array
    $folder = (array) $model->find($folderId);

    if (empty($folder)) {
        return redirect()->back()->with('error', 'Subfolder not found.');
    }

    if ($folder['parent_folder_id'] != $parentId) {
        return redirect()->back()->with('error', 'Invalid delete request.');
    }

    $subfolders = $model->where('parent_folder_id', $folderId)->findAll();
    if (!empty($subfolders)) {
        return redirect()->back()->with('error', 'This subfolder has child folders. Delete them first.');
    }

    $model->delete($folderId);

    return redirect()->to('/admin/files/view/' . $parentId)
                     ->with('success', 'Subfolder deleted successfully.');
}

    public function view($id)
    {
        $model = new FolderModel();
        $folder = $model->find($id);

        if (!$folder) {
            return redirect()->to('/admin/files')->with('error', 'Folder not found');
        }

        $search = $this->request->getGet('search');

        if ($search) {
            $subfolders = $model->like('folder_name', $search)
                                ->where('parent_folder_id', $id)
                                ->findAll();
        } else {
            $subfolders = $model->where('parent_folder_id', $id)->findAll();
        }

        $breadcrumb = $this->buildBreadcrumb($id);

        return view('admin/files', [
            'folders'      => $subfolders,
            'parentFolder' => $folder,
            'breadcrumb'   => $breadcrumb,
            'search'       => $search,
            'canAddFolder'       => $this->isAllowed('allow_add_folder'),
            'canDeleteFolder'    => $this->isAllowed('allow_delete_folder'),
            'canAddSubfolder'    => $this->isAllowed('allow_admin_add_subfolder'),
            'canDeleteSubfolder' => $this->isAllowed('allow_admin_delete_subfolder'),
        ]);
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
