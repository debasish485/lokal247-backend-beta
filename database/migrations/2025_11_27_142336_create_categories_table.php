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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            // UUID for public reference
            $table->uuid('uuid')->unique();

            // Blue-collar category name
            $table->string('name')->unique(); 

            // Slug for URLs / SEO / filters
            $table->string('slug')->unique();

            // Optional short description
            $table->string('description')->nullable();

            // Icon name or path (e.g. 'fa-user' or 'icons/cook.svg')
            $table->string('category_icon')->nullable();

            // Image path (banner / thumbnail for category)
            $table->string('image')->nullable();

            // Status toggle
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
