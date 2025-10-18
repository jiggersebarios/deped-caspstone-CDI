<?php

namespace App\Controllers\Superadmin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;

class Category extends BaseController
{
    protected $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
    }

    // Display categories
    public function index()
    {
        $data['categories'] = $this->categoryModel->findAll();
        $data['title'] = 'Categories';
        return view('superadmin/category', $data);
    }

    // Add normal category
    public function add()
    {
        $name = $this->request->getPost('category_name');
        $archiveYears = $this->request->getPost('archive_years');
        $retentionYears = $this->request->getPost('retention_years');
        $archiveSeconds = $this->request->getPost('archive_seconds'); // optional demo
        $retentionSeconds = $this->request->getPost('retention_seconds'); // optional demo

        if ($name && $retentionYears !== null && $archiveYears !== null) {
            $this->categoryModel->insert([
                'category_name' => $name,
                'archive_after_years' => $archiveYears,
                'retention_years' => $retentionYears,
                'archive_after_seconds' => $archiveSeconds ?: 0,
                'retention_seconds' => $retentionSeconds ?: 0
            ]);
            return redirect()->back()->with('success', 'Category added successfully!');
        }

        return redirect()->back()->with('error', 'Failed to add category. Please fill all required fields.');
    }

    // Add demo category
    public function addDemo()
    {
        $name = $this->request->getPost('category_name');
        $archiveSeconds = $this->request->getPost('archive_seconds');
        $retentionSeconds = $this->request->getPost('retention_seconds');

        if ($name && $archiveSeconds !== null && $retentionSeconds !== null) {
            $this->categoryModel->insert([
                'category_name' => $name,
                'archive_after_years' => 0,
                'retention_years' => 0,
                'archive_after_seconds' => $archiveSeconds,
                'retention_seconds' => $retentionSeconds
            ]);

            return redirect()->back()->with('success', 'Demo category added!');
        }

        return redirect()->back()->with('error', 'Please fill all required demo fields.');
    }

    // Edit category form
    public function edit($id)
    {
        $category = $this->categoryModel->find($id);
        if (!$category) {
            return redirect()->back()->with('error', 'Category not found.');
        }

        $data['category'] = $category;
        $data['title'] = 'Edit Category';
        return view('superadmin/edit_category', $data);
    }

    // Update category
    public function update($id)
    {
        $category = $this->categoryModel->find($id);
        if (!$category) {
            return redirect()->back()->with('error', 'Category not found.');
        }

        $name = $this->request->getPost('category_name');
        $archiveYears = $this->request->getPost('archive_years');
        $retentionYears = $this->request->getPost('retention_years');
        $archiveSeconds = $this->request->getPost('archive_seconds') ?: 0;
        $retentionSeconds = $this->request->getPost('retention_seconds') ?: 0;

        if ($name && $archiveYears !== null && $retentionYears !== null) {
            $this->categoryModel->update($id, [
                'category_name' => $name,
                'archive_after_years' => $archiveYears,
                'retention_years' => $retentionYears,
                'archive_after_seconds' => $archiveSeconds,
                'retention_seconds' => $retentionSeconds
            ]);
            return redirect()->to(site_url('superadmin/category'))->with('success', 'Category updated successfully!');
        }

        return redirect()->back()->with('error', 'Failed to update category.');
    }

    // Delete category
    public function delete($id)
    {
        $this->categoryModel->delete($id);
        return redirect()->back()->with('success', 'Category deleted successfully!');
    }
}
