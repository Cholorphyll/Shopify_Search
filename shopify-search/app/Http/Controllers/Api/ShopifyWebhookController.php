<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Jobs\SyncProductWithMeilisearch;
use App\Jobs\RemoveProductFromMeilisearch;

class ShopifyWebhookController extends Controller
{
    public function handleProductCreate(Request $request)
    {
        Log::info('Shopify product create webhook received.');
        $productData = $request->all();
        SyncProductWithMeilisearch::dispatch($productData);
        return response()->json(['message' => 'Product create webhook processed successfully'], 200);
    }

    public function handleProductUpdate(Request $request)
    {
        Log::info('Shopify product update webhook received.');
        $productData = $request->all();
        SyncProductWithMeilisearch::dispatch($productData);
        return response()->json(['message' => 'Product update webhook processed successfully'], 200);
    }

    public function handleProductDelete(Request $request)
    {
        Log::info('Shopify product delete webhook received.');
        // Assuming Shopify sends the product ID as 'id' in the payload
        // Log the entire payload to verify structure if issues arise: Log::info('Delete payload:', $request->all());
        $productId = $request->input('id');
        if ($productId) {
            RemoveProductFromMeilisearch::dispatch($productId);
            return response()->json(['message' => 'Product delete webhook processed successfully'], 200);
        }
        Log::error('Product ID not found in delete webhook payload.');
        return response()->json(['message' => 'Product ID not found in payload'], 400);
    }
}
