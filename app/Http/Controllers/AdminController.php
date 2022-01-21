<?php

namespace App\Http\Controllers;

class AdminController extends Controller
{
    public function approvalAdminView()
    {
        return view('admin.approvalAdmin');
    }
}
