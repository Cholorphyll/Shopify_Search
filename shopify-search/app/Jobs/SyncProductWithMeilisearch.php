<?php

namespace App\Jobs;

use App\Models\ShopifyProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncProductWithMeilisearch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $productData;

    /**
     * Create a new job instance.
     *
     * @param array $productData
     * @return void
     */
    public function __construct(array $productData)
    {
        $this->productData = $productData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $productData = $this->productData;
        
        // Map Shopify product data to your database fields
        $attributesToUpdate = [
            'title' => $productData['title'] ?? null,
            'body_html' => $productData['body_html'] ?? null,
            'vendor' => $productData['vendor'] ?? null,
            'product_type' => $productData['product_type'] ?? null,
            'handle' => $productData['handle'] ?? null,
            'published_at' => $productData['published_at'] ?? null,
            'template_suffix' => $productData['template_suffix'] ?? null,
            'status' => $productData['status'] ?? null,
            'published_scope' => $productData['published_scope'] ?? null,
            'tags' => $productData['tags'] ?? null,
            'admin_graphql_api_id' => $productData['admin_graphql_api_id'] ?? null,
            'variants' => json_encode($productData['variants'] ?? []),
            'options' => json_encode($productData['options'] ?? []),
            'images' => json_encode($productData['images'] ?? []),
            'image' => $productData['image'] ?? null,
        ];

        // Update or create the product in your local database
        // Scout will automatically sync with Meilisearch
        ShopifyProduct::updateOrCreate(
            ['shopify_id' => $productData['id']],
            $attributesToUpdate
        );
    }
}
