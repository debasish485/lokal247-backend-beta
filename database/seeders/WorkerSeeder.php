<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class WorkerSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Debasish Patra',
            'email' => 'codlyt2025@gmail.com',
            'password' => Hash::make('password123'),
            'gender' => 'male',
            'age' => 30,
            'mobile_number' => '9073742826', // test number
            'work_preference' => 'wfh',
            'work_duration_type' => 'monthly',
            'profile_photo' => null,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Sample Worker',
            'email' => 'sampleworker@example.com',
            'password' => Hash::make('password'),
            'gender' => 'female',
            'age' => 28,
            'mobile_number' => '9876543210', // another test number
            'work_preference' => 'hybrid',
            'work_duration_type' => 'daily',
            'profile_photo' => null,
            'is_active' => true,
        ]);
    }
}
