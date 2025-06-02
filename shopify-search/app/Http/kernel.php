<?php
namespace App\Http;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
class Kernel extends HttpKernel
{
   
protected $routeMiddleware = [
    // ... existing middleware
    'shopify.webhook' => \App\Http\Middleware\VerifyShopifyWebhook::class
,
];
}