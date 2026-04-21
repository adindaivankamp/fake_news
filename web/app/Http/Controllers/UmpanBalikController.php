<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UmpanBalikController extends Controller
{
     public function index()
    {
        return view('admin.umpanbalik');
    }
}
