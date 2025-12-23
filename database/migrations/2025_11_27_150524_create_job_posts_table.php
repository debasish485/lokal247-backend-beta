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
Schema::create('job_posts', function (Blueprint $table) {
    $table->id();

    // UUID (public reference ID instead of numeric)
    $table->uuid('uuid')->unique();

    // URL-friendly identifier
    $table->string('slug')->unique();   

    // Recruiter relation
    $table->foreignId('recruiter_id')
          ->constrained()
          ->onDelete('cascade');

    // Category relation
    $table->foreignId('category_id')   
          ->nullable()
          ->constrained('categories')
          ->nullOnDelete();

    $table->string('title');
    $table->text('description')->nullable();

    $table->enum('work_type', ['wfh', 'wfo', 'hybrid']);
    $table->string('city');
    $table->string('locality')->nullable();

    $table->enum('shift_timing', ['day', 'night', 'rotational']);
    $table->time('start_time')->nullable();
    $table->time('end_time')->nullable();

    $table->enum('pay_type', ['daily', 'monthly']);
    $table->unsignedInteger('pay_amount');

    $table->unsignedInteger('number_of_workers')->default(1);

    $table->enum('gender_preference', ['any', 'male', 'female', 'other'])->default('any');

    $table->date('start_date')->nullable();

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
        Schema::dropIfExists('job_posts');
    }
};
