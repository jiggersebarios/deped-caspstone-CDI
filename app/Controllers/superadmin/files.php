<?php

namespace App\Controllers\Superadmin;

use App\Controllers\BaseController;
use App\Models\FolderModel;
use App\Models\GlobalConfigModel;

class Files extends BaseController
{
    protected $folderModel;
    protected $configModel;

    public function __construct()
    {
        $this->folderModel = new FolderModel();
        $this->configModel = new GlobalConfigModel();
    }

    /**
     * Helper: normalize model result to array or null
     */
    protected function normalize($item)
    {
        if ($item === null) return null;
        return is_array($item) ? $item : (array) $item;
    }

    public function index()
    {
        if (!in_array(session()->get('role'), ['superadmin', 'admin'])) {
            return redirect()->to('/login')->with('error', 'Unauthorized access');
        }

        $search = $this->request->getGet('search');

        if ($search) {
            $folders = $this->folderModel
                ->like('folder_name', $search)
                ->where('parent_folder_id', null)
                ->findAll();
        } else {
            $folders = $this->folderModel
                ->where('parent_folder_id', null)
                ->findAll();
        }

        $folders = array_map([$this, 'normalize'], $folders);

        $data = [
            'title'   => 'Files',
            'folders' => $folders,
            'search'  => $search,
        ];

        return view('superadmin/files', $data);
    }

    public function view($id)
    {
        if (!in_array(session()->get('role'), ['superadmin', 'admin'])) {
            return redirect()->to('/login')->with('error', 'Unauthorized access');
        }

        $parentFolder = $this->normalize($this->folderModel->find($id));
        if (!$parentFolder) {
            return redirect()->to('superadmin/files')->with('error', 'Folder not found');
        }

        $search = $this->request->getGet('search');

        if ($search) {
            $folders = $this->folderModel
                ->like('folder_name', $search)
                ->where('parent_folder_id', $id)
                ->findAll();
        } else {
            $folders = $this->folderModel
                ->where('parent_folder_id', $id)
                ->findAll();
        }

        $folders = array_map([$this, 'normalize'], $folders);
        $breadcrumb = $this->buildBreadcrumb($id);

        $data = [
            'title'        => 'Subfolders',
            'folders'      => $folders,
            'parentFolder' => $parentFolder,
            'breadcrumb'   => $breadcrumb,
            'search'       => $search,
        ];

        return view('superadmin/files', $data);
    }

    public function add()
    {
        // Check global config only if role = admin
        if (session()->get('role') === 'admin' &&
            !$this->configModel->getSetting('allow_admin_add_folder')) {
            return redirect()->back()->with('error', 'Adding folders is disabled by Superadmin.');
        }

        $folderName = $this->request->getPost('folder_name');
        $parentId   = $this->request->getPost('parent_folder_id');

        if ($folderName) {
            $this->folderModel->insert([
                'folder_name'      => $folderName,
                'parent_folder_id' => $parentId ?: null,
            ]);

            return redirect()->back()->with('success', 'Folder added successfully.');
        }

        return redirect()->back()->with('error', 'Folder name is required.');
    }

    public function addSubfolder($parentId)
    {
        if (session()->get('role') === 'admin' &&
            !$this->configModel->getSetting('allow_admin_add_subfolder')) {
            return redirect()->back()->with('error', 'Adding subfolders is disabled by Superadmin.');
        }

        $folderName = $this->request->getPost('folder_name');

        if ($folderName) {
            $this->folderModel->insert([
                'folder_name'      => $folderName,
                'parent_folder_id' => $parentId,
            ]);

            return redirect()->back()->with('success', 'Subfolder added successfully.');
        }

        return redirect()->back()->with('error', 'Subfolder name is required.');
    }

    public function delete()
    {
        if (session()->get('role') === 'admin' &&
            !$this->configModel->getSetting('allow_admin_delete_folder')) {
            return redirect()->back()->with('error', 'Deleting folders is disabled by Superadmin.');
        }

        $folderId = $this->request->getPost('delete_folder_id');

        if ($folderId) {
            $this->deleteRecursive($folderId);
            return redirect()->back()->with('success', 'Folder deleted successfully.');
        }

        return redirect()->back()->with('error', 'Invalid folder selected.');
    }

    public function deleteSubfolder()
    {
        if (session()->get('role') === 'admin' &&
            !$this->configModel->getSetting('allow_admin_delete_subfolder')) {
            return redirect()->back()->with('error', 'Deleting subfolders is disabled by Superadmin.');
        }

        $folderId = $this->request->getPost('delete_folder_id');

        if ($folderId) {
            $this->deleteRecursive($folderId);
            return redirect()->back()->with('success', 'Subfolder deleted successfully.');
        }

        return redirect()->back()->with('error', 'Invalid subfolder selected.');
    }

    private function deleteRecursive($folderId)
    {
        $subfolders = $this->folderModel->where('parent_folder_id', $folderId)->findAll();

        foreach ($subfolders as $sub) {
            $this->deleteRecursive($sub['id'] ?? $sub->id);
        }

        $this->folderModel->delete($folderId);
    }

    private function buildBreadcrumb($folderId)
    {
        $breadcrumb = [];
        $current = $this->normalize($this->folderModel->find($folderId));

        while ($current) {
            array_unshift($breadcrumb, $current);

            $parentId = $current['parent_folder_id'] ?? $current['parent_id'] ?? null;
            if (empty($parentId)) {
                break;
            }

            $current = $this->normalize($this->folderModel->find($parentId));
        }

        return $breadcrumb;
    }
}
