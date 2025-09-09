<?php

namespace App\Controllers\Superadmin;

use App\Controllers\BaseController;
use App\Models\FolderModel;

class Files extends BaseController
{
    protected $folderModel;

    public function __construct()
    {
        $this->folderModel = new FolderModel();
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
        if (session()->get('role') !== 'superadmin') {
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

        // normalize each folder to array (safety)
        $folders = array_map([$this, 'normalize'], $folders);

        $data = [
            'title'   => 'SuperAdmin - Files',
            'folders' => $folders,
            'search'  => $search,
        ];

        return view('superadmin/files', $data);
    }

    public function view($id)
    {
        if (session()->get('role') !== 'superadmin') {
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
            'title'        => 'SuperAdmin - Subfolders',
            'folders'      => $folders,
            'parentFolder' => $parentFolder,
            'breadcrumb'   => $breadcrumb,
            'search'       => $search,
        ];

        return view('superadmin/files', $data);
    }

    public function add()
    {
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
        $folderId = $this->request->getPost('delete_folder_id');

        if ($folderId) {
            $this->deleteRecursive($folderId);
            return redirect()->back()->with('success', 'Folder deleted successfully.');
        }

        return redirect()->back()->with('error', 'Invalid folder selected.');
    }

    public function deleteSubfolder()
    {
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

    /**
     * Build breadcrumb safely.
     * Supports DB column 'parent_folder_id' or 'parent_id'.
     */
    private function buildBreadcrumb($folderId)
    {
        $breadcrumb = [];
        $current = $this->normalize($this->folderModel->find($folderId));

        while ($current) {
            array_unshift($breadcrumb, $current);

            // try both possible parent keys
            $parentId = $current['parent_folder_id'] ?? $current['parent_id'] ?? null;

            // stop when there is no parent
            if (empty($parentId)) {
                break;
            }

            $current = $this->normalize($this->folderModel->find($parentId));
        }

        return $breadcrumb;
    }
}
