<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function profile()
    {
        return view('auth.profile');
    }

    public function showMypage()
    {
        return view('auth.mypage');
    }
}
