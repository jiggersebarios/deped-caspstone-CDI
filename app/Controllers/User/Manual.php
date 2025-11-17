<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;

class Manual extends BaseController
{
    /**
     * Displays the user manual page.
     */
    public function index()
    {
        // This will load the view file located at:
        // app/Views/user/manual.php
        return view('user/manual');
    }
}