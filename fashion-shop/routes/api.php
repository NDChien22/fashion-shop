<?php

use App\Http\Controllers\CollectionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth')->group(function () {
    Route::get('/products-not-in-collection/{collectionId}', [CollectionController::class, 'getProductsNotInCollection']);
});
