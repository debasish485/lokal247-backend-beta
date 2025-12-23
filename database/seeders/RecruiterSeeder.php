<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recruiter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RecruiterSeeder extends Seeder
{
    public function run(): void
    {
        // Create 10 dummy recruiters
        for ($i = 1; $i <= 10; $i++) {
            Recruiter::create([
                'uuid' => Str::uuid(),
                'name' => "Recruiter $i",
                'email' => "recruiter$i@company.com",
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'phone' => "98765000$i",
                'alternate_phone' => null,
                'company_name' => "Company $i",
                'city' => "City $i",
                'company_address' => "Address line $i",
                'company_logo' => null,
                'industry' => "Industry $i",
                'designation' => "HR Manager",
                'accepted_terms' => true,
                'is_blocked' => false,
                'signup_ip' => "127.0.0.1",
                'last_login_ip' => "127.0.0.1",
            ]);
        }
    }
}
