<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductSearchController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    // Search products
    Route::get('/products/search', [ProductSearchController::class, 'search'])->name('api.products.search');
    
    // Add more API endpoints here as needed
});
