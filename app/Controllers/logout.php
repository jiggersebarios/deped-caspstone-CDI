<?php

namespace App\Controllers;

class Logout extends BaseController
{
    public function index()
    {
        session()->destroy(); // clear all session data
        return redirect()->to('/login'); // send back to login
    }
}