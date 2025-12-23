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
        Schema::create('recruiters', function (Blueprint $table) {
            $table->id();

            // Public UUID for API exposure
            $table->uuid('uuid')->unique();

            // Authentication
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();

            // Contact
            $table->string('phone', 12);
            $table->string('alternate_phone', 12)->nullable();

            // Company Info
            $table->string('company_name');
            $table->string('city');
            $table->string('company_address')->nullable();
            $table->string('company_logo')->nullable();

            // Additional Business Info
            $table->string('industry')->nullable();
            $table->string('designation')->nullable();

            // Consent
            $table->boolean('accepted_terms')->default(false);

            // Account status
            $table->boolean('is_blocked')->default(false);

            // IP Tracking
            $table->string('signup_ip', 45)->nullable();     
            $table->string('last_login_ip', 45)->nullable(); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recruiters');
    }
};
