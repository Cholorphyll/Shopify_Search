<?php

namespace App\Console\Commands\Meilisearch;

use App\Models\ShopifyProduct;
use Illuminate\Console\Command;
use Laravel\Scout\EngineManager;
use Meilisearch\Client as MeilisearchClient;

class SetupMeilisearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meilisearch:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configure Meilisearch settings and reindex models';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Configuring Meilisearch...');
        
        // Get the model instance to access its settings
        $model = new ShopifyProduct();
        $indexName = $model->searchableAs();
        
        try {
            // Initialize Meilisearch client directly
            $client = new MeilisearchClient(
                env('MEILISEARCH_HOST', 'http://localhost:7700'),
                env('MEILISEARCH_KEY')
            );
            
            // Get or create the index
            $index = $client->index($indexName);
            
            // Get the settings from the model
            $settings = $model->meilisearchSettings([]);
            
            // Update the settings
            $this->info('Updating Meilisearch settings...');
            
            // Update settings one by one to avoid conflicts
            $index->updateFilterableAttributes($settings['filterableAttributes']);
            $index->updateSortableAttributes($settings['sortableAttributes']);
            $index->updateSearchableAttributes($settings['searchableAttributes']);
            $index->updateRankingRules($settings['rankingRules']);
            
            // Optional settings
            if (!empty($settings['stopWords'])) {
                $index->updateStopWords($settings['stopWords']);
            }
            
            if (!empty($settings['synonyms'])) {
                $index->updateSynonyms($settings['synonyms']);
            }
            
            $this->info('Meilisearch settings updated successfully!');
            
            // Reindex the models
            $this->info('Reindexing products...');
            
            // First, flush the index
            $this->call('scout:flush', ['model' => ShopifyProduct::class]);
            
            // Then reimport the data
            $this->call('scout:import', ['model' => ShopifyProduct::class]);
            
            $this->info('Meilisearch setup completed successfully!');
            
        } catch (\Exception $e) {
            $this->error('Error configuring Meilisearch: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
        
        return 0;
    }
}
