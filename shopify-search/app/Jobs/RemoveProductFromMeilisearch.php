<?php

namespace App\Jobs;

use App\Models\ShopifyProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveProductFromMeilisearch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $productId;

    /**
     * Create a new job instance.
     *
     * @param int $productId
     * @return void
     */
    public function __construct($productId)
    {
        $this->productId = $productId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $product = ShopifyProduct::where('shopify_id', $this->productId)->first();
        
        if ($product) {
            // This will remove the product from Meilisearch
            $product->unsearchable();
            
            // Optional: Uncomment the line below to also delete from local database
            // $product->delete();
        }
    }
}
