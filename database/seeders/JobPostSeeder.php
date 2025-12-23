<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JobPost;
use App\Models\Category;
use App\Models\Recruiter;

class JobPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryIds  = Category::pluck('id')->toArray();
        $recruiterIds = Recruiter::pluck('id')->toArray();

        if (empty($categoryIds) || empty($recruiterIds)) {
            return; // stop if dependencies not ready
        }

        $jobs = [
            [
                'title' => 'Housekeeping Staff',
                'description' => 'Full-time residential housekeeping required.',
                'city' => 'Mumbai',
                'locality' => 'Andheri',
                'start_time' => '09:00',
                'end_time' => '18:00',
                'pay_amount' => 15000,
            ],
            [
                'title' => 'Delivery Driver',
                'description' => 'Driver needed for delivery van.',
                'city' => 'Delhi',
                'locality' => 'Rohini',
                'start_time' => '07:00',
                'end_time' => '20:00',
                'pay_amount' => 20000,
            ],
            [
                'title' => 'Restaurant Cook',
                'description' => 'Cook for North Indian dishes needed.',
                'city' => 'Bangalore',
                'locality' => 'Whitefield',
                'start_time' => '10:00',
                'end_time' => '22:00',
                'pay_amount' => 22000,
            ],
            [
                'title' => 'Security Guard',
                'description' => 'Night shift security guard for commercial building.',
                'city' => 'Pune',
                'locality' => 'Baner',
                'start_time' => '20:00',
                'end_time' => '08:00',
                'pay_amount' => 17000,
            ],
            [
                'title' => 'Factory Worker',
                'description' => 'Assembly line worker for packaging unit.',
                'city' => 'Hyderabad',
                'locality' => 'Kondapur',
                'start_time' => '09:00',
                'end_time' => '18:00',
                'pay_amount' => 16000,
            ],
            [
                'title' => 'Retail Sales Staff',
                'description' => 'Sales staff required for clothing shop.',
                'city' => 'Mumbai',
                'locality' => 'Dadar',
                'start_time' => '11:00',
                'end_time' => '21:00',
                'pay_amount' => 18000,
            ],
            [
                'title' => 'Baby Care Helper',
                'description' => 'Nanny required for newborn baby.',
                'city' => 'Delhi',
                'locality' => 'Dwarka',
                'start_time' => '08:00',
                'end_time' => '18:00',
                'pay_amount' => 20000,
            ],
            [
                'title' => 'Home Electrician',
                'description' => 'Part-time electrician for home repair work.',
                'city' => 'Bangalore',
                'locality' => 'BTM Layout',
                'start_time' => '09:00',
                'end_time' => '17:00',
                'pay_amount' => 1200, // daily
            ],
            [
                'title' => 'Commercial Plumber',
                'description' => 'Experienced plumber needed for plumbing installation.',
                'city' => 'Pune',
                'locality' => 'Wakad',
                'start_time' => '09:00',
                'end_time' => '18:00',
                'pay_amount' => 19000,
            ],
            [
                'title' => 'Furniture Carpenter',
                'description' => 'Carpenter required for furniture fitting.',
                'city' => 'Hyderabad',
                'locality' => 'Madhapur',
                'start_time' => '10:00',
                'end_time' => '19:00',
                'pay_amount' => 20000,
            ],
        ];

        foreach ($jobs as $index => $job) {
            JobPost::create([
                'recruiter_id'      => $recruiterIds[$index % count($recruiterIds)],
                'category_id'       => $categoryIds[$index % count($categoryIds)],
                'title'             => $job['title'],
                'description'       => $job['description'],
                'work_type'         => 'wfo',
                'city'              => $job['city'],
                'locality'          => $job['locality'],
                'shift_timing'      => 'day',
                'start_time'        => $job['start_time'],
                'end_time'          => $job['end_time'],
                'pay_type'          => 'monthly',
                'pay_amount'        => $job['pay_amount'],
                'number_of_workers' => 1,
                'gender_preference' => 'any',
                'start_date'        => now()->addDays(2)->toDateString(),
                'is_active'         => true,
            ]);
        }
    }
}
