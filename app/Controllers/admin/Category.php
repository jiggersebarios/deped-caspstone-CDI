<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\GlobalConfigModel;

class Category extends BaseController
{
    protected $categoryModel;
    protected $configModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
        $this->configModel = new GlobalConfigModel();
    }

    // Display all categories
    public function index()
    {
        // Check if admin is allowed to access category
        if (! $this->configModel->isEnabled('allow_admin_to_access_category')) {
            return redirect()->to('/admin/dashboard')->with('error', 'Access to categories is disabled.');
        }

        $categories = $this->categoryModel->findAll();
        $adminConfig = [
            'allow_admin_to_access_category' => $this->configModel->isEnabled('allow_admin_to_access_category')
        ];

        return view('admin/category', [
            'categories' => $categories,
            'title' => 'Manage Categories',
            'config' => $adminConfig
        ]);
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

        return redirect()->to(site_url('admin/category'))
            ->with('success', 'Category added successfully!');
    }

    // Edit category
    public function edit($id)
    {
        $category = $this->categoryModel->find($id);

        if (!$category) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Category with ID $id not found.");
        }

        $adminConfig = [
            'allow_admin_to_access_category' => $this->configModel->isEnabled('allow_admin_to_access_category')
        ];

        return view('admin/category', [
            'category' => $category,
            'title' => 'Edit Category',
            'config' => $adminConfig
        ]);
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

        return redirect()->to(site_url('admin/category'))
            ->with('success', 'Category updated successfully!');
    }

    // Delete category
    public function delete($id)
    {
        $this->categoryModel->delete($id);

        return redirect()->to(site_url('admin/category'))
            ->with('success', 'Category deleted successfully!');
    }
}
