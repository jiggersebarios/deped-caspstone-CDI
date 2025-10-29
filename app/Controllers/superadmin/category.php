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

    // Display all categories
    public function index()
    {
        $data['categories'] = $this->categoryModel->findAll();
        $data['title'] = 'Manage Categories';
        return view('superadmin/category', $data);
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

        return redirect()->to(site_url('superadmin/category'))
            ->with('success', 'Category added successfully!');
    }

    // Edit category (load data into form)
    public function edit($id)
    {
        $data['category'] = $this->categoryModel->find($id);

        if (!$data['category']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Category with ID $id not found.");
        }

        $data['title'] = 'Edit Category';
        return view('superadmin/edit_category', $data);
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

        return redirect()->to(site_url('superadmin/category'))
            ->with('success', 'Category updated successfully!');
    }

        // Delete category
        public function delete($id)
        {
            $this->categoryModel->delete($id);

            return redirect()->to(site_url('superadmin/category'))
                ->with('success', 'Category deleted successfully!');
        }
}
