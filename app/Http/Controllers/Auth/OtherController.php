<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class OtherController extends Controller
{
    public function index($slug)
    {
        return view("other.$slug");
    }
}
