<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FolderModel;

class Files extends BaseController
{
    public function index()
    {
        $folderModel = new \App\Models\FolderModel();
        $search = $this->request->getGet('search'); // get ?search= query

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

        return view('admin/files', [
            'folders' => $folders
        ]);
    }
public function add()
{
    $model = new FolderModel();

    $folderName = trim($this->request->getPost('folder_name'));
    if (!$folderName) {
        return redirect()->back()->with('error', 'Folder name is required');
    }

    // Check duplicates among MAIN folders
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
    $model = new FolderModel();

    $folderName = trim($this->request->getPost('folder_name'));
    if (!$folderName) {
        return redirect()->back()->with('error', 'Subfolder name is required');
    }

    // Check duplicates inside SAME parent
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
    $model = new FolderModel();
    $folderId = $this->request->getPost('delete_folder_id');
    $parentId = $this->request->getPost('parent_folder_id'); // hidden input to know where we came from

    if ($folderId) {
        $model->delete($folderId);

        // redirect back to correct context
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
    $model     = new FolderModel();
    $folderId  = $this->request->getPost('delete_folder_id');
    $parentId  = $this->request->getPost('parent_folder_id');

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


public function view($id)
{
    $model = new FolderModel();
    $folder = $model->find($id);

    if (!$folder) {
        return redirect()->to('/admin/files')->with('error', 'Folder not found');
    }

    // Get search keyword if provided
    $search = $this->request->getGet('search');

    if ($search) {
        // Search only within subfolders of this folder
        $subfolders = $model->like('folder_name', $search)
                            ->where('parent_folder_id', $id)
                            ->findAll();
    } else {
        // Normal load: all subfolders
        $subfolders = $model->where('parent_folder_id', $id)->findAll();
    }

    // Build breadcrumb path (recursive)
    $breadcrumb = $this->buildBreadcrumb($id);

    return view('admin/files', [
        'folders'      => $subfolders,
        'parentFolder' => $folder,
        'breadcrumb'   => $breadcrumb,
        'search'       => $search // pass to view so input can persist
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
            $id = $folder['parent_folder_id']; // move up
        } else {
            $id = null;
        }
    }

    return array_reverse($path); // so it starts from root → child
}




}
