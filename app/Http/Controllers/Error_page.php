<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class error_page extends Controller
{
    public function index() {
        return view('pages.error_page');
    }
}
