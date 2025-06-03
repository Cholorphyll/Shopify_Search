<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductSearchController;
use App\Http\Controllers\Api\ShopifyWebhookController;

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

    // Shopify Webhook Endpoints
    Route::middleware('shopify.webhook')->group(function () {
        Route::post('/webhooks/products/create', [ShopifyWebhookController::class, 'handleProductCreate']);
        Route::post('/webhooks/products/update', [ShopifyWebhookController::class, 'handleProductUpdate']);
        Route::post('/webhooks/products/delete', [ShopifyWebhookController::class, 'handleProductDelete']);
    });
});
