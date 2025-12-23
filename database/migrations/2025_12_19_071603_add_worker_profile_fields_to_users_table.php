<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {


            // Basic Profile
            $table->enum('gender', ['male', 'female', 'other'])
                  ->nullable()
                  ->after('password');

            $table->unsignedTinyInteger('age')
                  ->nullable()
                  ->after('gender');

            // Contact (Future OTP)
            $table->string('mobile_number', 15)
                  ->nullable()
                  ->unique()
                  ->after('age');

            // Work Preference
            $table->enum('work_preference', ['wfh', 'wfo','hybrid'])
                  ->nullable()
                  ->after('mobile_number');

// Work Duration Type
$table->enum('work_duration_type', ['hourly', 'daily', 'weekly', 'monthly'])
      ->nullable()
      ->after('work_preference');




            // Profile Photo
            $table->string('profile_photo')
                  ->nullable()
                  ->after('work_duration_type');


            // Status
            $table->boolean('is_active')
                  ->default(true)
                  ->after('profile_photo');
        });
    }

    /**
     * Reverse the migrations.
     */
public function down(): void
{
    Schema::table('users', function (Blueprint $table) {

        $table->dropColumn([
            'gender',
            'age',
            'mobile_number',
            'work_preference',
            'work_duration_type',
            'profile_photo',
            'is_active',
        ]);
    });
}

};
