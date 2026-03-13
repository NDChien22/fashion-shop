<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function ProfileView(){
        return view('pages.admin.account-manager.admin-profile');
    }

    public function AccountManagerView(){
        return view('pages.admin.account-manager.account-manager');
    }
}
