<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function productManagerView(){
        return view('pages.admin.product-manager.product-manager');
    }

    public function addProductForm(){
        return view('pages.admin.product-manager.add-product');
    }

    public function editProductForm(){
        return view('pages.admin.product-manager.edit-product');
    }
}
