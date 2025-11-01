<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CategoryModel;

class Category extends BaseController
{
    protected $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
    }

    // List categories
    public function index()
    {
        $session = session();
        $role = $session->get('role'); // 'admin' or 'superadmin'

        $data['categories'] = $this->categoryModel->findAll();
        $data['title'] = 'Manage Categories';
        $data['base_url'] = $role;

        return view('shared/category', $data);
    }

    // Store new category
    public function store()
    {
        $postData = $this->request->getPost();

        $this->categoryModel->insert([
            'category_name'       => $postData['category_name'],
            'description'         => $postData['description'],
            'archive_after_value' => $postData['archive_after_value'],
            'archive_after_unit'  => $postData['archive_after_unit'],
            'retention_value'     => $postData['retention_value'],
            'retention_unit'      => $postData['retention_unit'],
        ]);

        $role = session()->get('role');
        return redirect()->to(site_url("$role/category"))
                         ->with('success', 'Category added successfully!');
    }

    // Update category
    public function update($id)
    {
        $postData = $this->request->getPost();

        $this->categoryModel->update($id, [
            'category_name'       => $postData['category_name'],
            'description'         => $postData['description'],
            'archive_after_value' => $postData['archive_after_value'],
            'archive_after_unit'  => $postData['archive_after_unit'],
            'retention_value'     => $postData['retention_value'],
            'retention_unit'      => $postData['retention_unit'],
        ]);

        $role = session()->get('role');
        return redirect()->to(site_url("$role/category"))
                         ->with('success', 'Category updated successfully!');
    }

    // Delete category
    public function delete($id)
    {
        $this->categoryModel->delete($id);

        $role = session()->get('role');
        return redirect()->to(site_url("$role/category"))
                         ->with('success', 'Category deleted successfully!');
    }
}
