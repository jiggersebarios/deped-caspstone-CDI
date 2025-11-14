<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Manual extends BaseController
{
    public function index()
    {
        return view('shared/manual');
    }
}
