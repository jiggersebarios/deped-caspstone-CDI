<?php

namespace App\Controllers;

class Admin extends BaseController
{
    public function index()
    {
        return view('admin/dashboard'); // loads app/Views/admin/dashboard.php
    }
}
