<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function ProfileView(){
        return view('pages.admin.admin-profile');
    }

    public function AccountManagerView(){
        return view('pages.admin.account-manager');
    }
}
