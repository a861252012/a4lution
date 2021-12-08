<?php

namespace App\Http\Controllers;

class CustomerController extends Controller
{
    public function index()
    {
        return view('customer.index');
    }

    public function edit()
    {
        return view('customer.edit');
    }
}
