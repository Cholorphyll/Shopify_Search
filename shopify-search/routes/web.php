<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopifyWebhookController;
Route::post('/webhooks/shopify/product-events', [ShopifyWebhookController::class, 'handleProductEvents'])->middleware('shopify.webhook')->name('webhooks.shopify.products');

Route::get('/', function () {
    return view('welcome');
});
