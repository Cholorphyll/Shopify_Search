<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Optional: for logging

class VerifyShopifyWebhook
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $shopifyHmac = $request->header('X-Shopify-Hmac-Sha256');
        
        // Get RAW body content. DO NOT use $request->all() or $request->input() here,
        // as that can change the order or content of the data used for HMAC calculation.
        $payload = $request->getContent(); 

        if (!$shopifyHmac) {
            Log::warning('Shopify webhook call missing X-Shopify-Hmac-Sha256 header.'); // Optional logging
            abort(401, 'Webhook signature missing.');
        }

        // IMPORTANT: You MUST set SHOPIFY_APP_SECRET in your .env file
        // This is the "Shared Secret" from your Shopify App's settings.
        $sharedSecret = env('SHOPIFY_APP_SECRET'); 
        if (!$sharedSecret) {
            Log::error('SHOPIFY_APP_SECRET is not set in the .env file. Webhook verification cannot proceed.'); // Optional logging
            abort(500, 'Application secret not configured for webhook verification.');
        }

        $calculatedHmac = base64_encode(hash_hmac('sha256', $payload, $sharedSecret, true));

        if (!hash_equals($shopifyHmac, $calculatedHmac)) {
            Log::warning('Shopify webhook signature verification failed. Signatures do not match.', [ // Optional logging
                'received_hmac' => $shopifyHmac,
                'calculated_hmac' => $calculatedHmac,
                // 'payload_for_debug' => $payload // Be very careful logging raw payload in production
            ]);
            abort(403, 'Webhook signature verification failed.');
        }

        // If execution reaches here, the signature is valid.
        return $next($request);
    }
}