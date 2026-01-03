<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            // Job application relation
            $table->foreignId('job_application_id')
                ->constrained('job_applications')
                ->cascadeOnDelete();

            // Reviewer (worker or recruiter)
            $table->unsignedBigInteger('reviewer_id');
            $table->string('reviewer_type'); 

            // Reviewee (worker or recruiter)
            $table->unsignedBigInteger('reviewee_id');
            $table->string('reviewee_type');

            // Rating & comment
            $table->tinyInteger('rating')->unsigned(); // 1–5
            $table->text('comment')->nullable();

            $table->timestamps();

            // Prevent duplicate reviews per application per reviewer
            $table->unique(
                ['job_application_id', 'reviewer_id', 'reviewer_type'],
                'unique_review_per_application'
            );

            // Helpful indexes
            $table->index(['reviewee_id', 'reviewee_type']);
            $table->index('rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
