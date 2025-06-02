<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shopify_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shopify_id')->unique();
            $table->string('title');
            $table->text('body_html')->nullable();
            $table->string('vendor')->nullable();
            $table->string('product_type')->nullable();
            $table->string('handle')->unique();
            $table->timestamp('published_at')->nullable();
            $table->string('template_suffix')->nullable();
            $table->string('status')->default('draft');
            $table->string('published_scope')->nullable();
            $table->text('tags')->nullable();
            $table->string('admin_graphql_api_id')->nullable();
            $table->json('variants')->nullable();
            $table->json('options')->nullable();
            $table->json('images')->nullable();
            $table->json('image')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('shopify_id');
            $table->index('title');
            $table->index('vendor');
            $table->index('product_type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopify_products');
    }
};
