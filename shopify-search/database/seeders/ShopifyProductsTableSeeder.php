<?php

namespace Database\Seeders;

use App\Models\ShopifyProduct;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ShopifyProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'title' => 'Classic T-Shirt',
                'body_html' => '<p>Comfortable and stylish classic t-shirt</p>',
                'vendor' => 'Fashion Store',
                'product_type' => 'Clothing',
                'status' => 'active',
                'tags' => 'men, tshirt, summer',
            ],
            [
                'title' => 'Slim Fit Jeans',
                'body_html' => '<p>Perfect fit jeans for any occasion</p>',
                'vendor' => 'Denim Co',
                'product_type' => 'Pants',
                'status' => 'active',
                'tags' => 'men, jeans, denim',
            ],
            [
                'title' => 'Running Shoes',
                'body_html' => '<p>Lightweight running shoes for maximum comfort</p>',
                'vendor' => 'Sport Gear',
                'product_type' => 'Footwear',
                'status' => 'active',
                'tags' => 'men, women, running, shoes',
            ],
        ];

        foreach ($products as $productData) {
            $handle = Str::slug($productData['title']);
            
            ShopifyProduct::create([
                'shopify_id' => rand(1000000000, 9999999999),
                'title' => $productData['title'],
                'body_html' => $productData['body_html'],
                'vendor' => $productData['vendor'],
                'product_type' => $productData['product_type'],
                'handle' => $handle,
                'published_at' => now(),
                'status' => $productData['status'],
                'published_scope' => 'web',
                'tags' => $productData['tags'],
                'admin_graphql_api_id' => 'gid://shopify/Product/' . rand(1000000000, 9999999999),
                'variants' => json_encode([
                    [
                        'title' => 'Default Title',
                        'price' => rand(1999, 9999) / 100,
                        'sku' => 'SKU-' . strtoupper(Str::random(8)),
                        'inventory_quantity' => rand(5, 50),
                    ]
                ]),
                'options' => json_encode([
                    [
                        'name' => 'Title',
                        'position' => 1,
                        'values' => ['Default Title']
                    ]
                ]),
                'images' => json_encode([
                    [
                        'src' => 'https://via.placeholder.com/800x800?text=' . urlencode($productData['title']),
                        'position' => 1,
                    ]
                ]),
                'image' => json_encode([
                    'src' => 'https://via.placeholder.com/400x400?text=' . urlencode($productData['title']),
                ]),
            ]);
        }
    }
}
