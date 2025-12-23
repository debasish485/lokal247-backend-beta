<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    //use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([

            //CategorySeeder::class,     // Category seeding call
            //RecruiterSeeder::class,    // Recruiter seeder
            //JobPostSeeder::class,
            //SkillSeeder::class,
            WorkerSeeder::class

        ]);
    }
}
