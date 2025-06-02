<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class ShopifyProduct extends Model
{
    use HasFactory, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'shopify_id',
        'title',
        'body_html',
        'vendor',
        'product_type',
        'handle',
        'published_at',
        'template_suffix',
        'status',
        'published_scope',
        'tags',
        'admin_graphql_api_id',
        'variants',
        'options',
        'images',
        'image',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'variants' => 'array',
        'options' => 'array',
        'images' => 'array',
        'published_at' => 'datetime',
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();

        // Customize the data array to be indexed
        return [
            'id' => $this->id,
            'shopify_id' => $this->shopify_id,
            'title' => $this->title,
            'body_html' => $this->body_html,
            'vendor' => $this->vendor,
            'product_type' => $this->product_type,
            'handle' => $this->handle,
            'status' => $this->status,
            'tags' => $this->tags,
            'created_at' => $this->created_at->timestamp,
            'updated_at' => $this->updated_at->timestamp,
        ];
    }

    /**
     * Get the index name for the model.
     *
     * @return string
     */
    public function searchableAs()
    {
        return 'shopify_products';
    }

    /**
     * Get the settings for Meilisearch.
     *
     * @return array
     */
    public function meilisearchSettings(array $settings): array
    {
        return [
            'filterableAttributes' => [
                'vendor',
                'product_type',
                'status',
                'tags',
            ],
            'sortableAttributes' => [
                'created_at',
                'updated_at',
            ],
            'searchableAttributes' => [
                'title',
                'body_html',
                'vendor',
                'product_type',
                'tags',
            ],
            'rankingRules' => [
                'words',
                'typo',
                'proximity',
                'attribute',
                'sort',
                'exactness',
                'created_at:desc',
            ],
            'stopWords' => null,
            'synonyms' => null,
        ];
    }

    /**
     * Get the index settings for the model.
     *
     * @return array
     */
    public function toSearchableArrayWithSettings()
    {
        return [
            'index-settings' => $this->meilisearchSettings([]),
            'documents' => [$this->toSearchableArray()],
        ];
    }
}
