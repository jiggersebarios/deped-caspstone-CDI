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

    public function index()
    {
        $data['categories'] = $this->categoryModel->findAll();
        $data['title'] = 'Categories';
        return view('superadmin/category', $data);
    }

    public function add()
    {
        $name = $this->request->getPost('category_name');
        $years = $this->request->getPost('retention_years');

        if ($name && $years) {
            $this->categoryModel->insert([
                'category_name' => $name,
                'retention_years' => $years
            ]);
            return redirect()->back()->with('success', 'Category added successfully!');
        }
        return redirect()->back()->with('error', 'Failed to add category.');
    }

    public function delete($id)
    {
        $this->categoryModel->delete($id);
        return redirect()->back()->with('success', 'Category deleted successfully!');
    }
}
