<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\SyncProductWithMeilisearch;
use App\Jobs\RemoveProductFromMeilisearch;

class ShopifyWebhookController extends Controller
{
    /**
     * Handle incoming Shopify webhooks
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function handleWebhook(Request $request)
    {
        // Verify the webhook if needed (HMAC verification)
        // $hmac = $request->header('X-Shopify-Hmac-Sha256');
        // $verified = $this->verifyWebhook($hmac, $request->getContent());
        // if (!$verified) {
        //     return response()->json(['error' => 'Unauthorized'], 401);
        // }


        $topic = $request->header('X-Shopify-Topic');
        $payload = $request->json()->all();

        switch ($topic) {
            case 'products/create':
            case 'products/update':
                SyncProductWithMeilisearch::dispatch($payload)
                    ->onQueue('shopify-sync');
                break;

            case 'products/delete':
                if (isset($payload['id'])) {
                    RemoveProductFromMeilisearch::dispatch($payload['id'])
                        ->onQueue('shopify-sync');
                }
                break;

            // Add more cases for other webhook topics as needed
            
            default:
                // Log unhandled webhook topics
                \Log::info("Unhandled webhook topic: $topic", ['payload' => $payload]);
                break;
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Verify Shopify webhook HMAC
     *
     * @param  string  $hmac
     * @param  string  $data
     * @return bool
     */
    private function verifyWebhook($hmac, $data)
    {
        $calculatedHmac = base64_encode(
            hash_hmac('sha256', $data, config('services.shopify.webhook_secret'), true)
        );

        return hash_equals($hmac, $calculatedHmac);
    }
}
