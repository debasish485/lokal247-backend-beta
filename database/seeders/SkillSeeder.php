<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Skill;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            // Construction & Trade
            'Electrician',
            'Plumber',
            'Carpenter',
            'Painter',
            'Mason',
            'Welder',
            'Construction Worker',
            'Helper / Labour',
            'Scaffolding Worker',

            // Mechanical & Technical
            'Mechanic',
            'Auto Electrician',
            'AC Technician',
            'Refrigerator Technician',
            'Machine Operator',
            'CNC Operator',

            // Factory & Warehouse
            'Factory Worker',
            'Production Line Worker',
            'Warehouse Worker',
            'Loader / Unloader',
            'Packing Staff',
            'Quality Checker',

            // Driving & Transport
            'Driver',
            'Delivery Boy',
            'Bike Rider',
            'Truck Driver',
            'Auto Driver',

            // Hospitality & Services
            'Cook',
            'Kitchen Helper',
            'Waiter',
            'Housekeeping Staff',
            'Cleaner / Janitor',
            'Hotel Boy',

            // Security & Maintenance
            'Security Guard',
            'Watchman',
            'Maintenance Staff',

            // Domestic & Care
            'House Maid',
            'Nanny / Babysitter',
            'Elder Care Attendant',
            'Care Taker',

            // Salon & Personal Care
            'Beautician',
            'Salon Assistant',
            'Hair Stylist',

            // Office Support (Non-IT)
            'Office Boy',
            'Peon',
            'Data Entry Operator',

            // Agriculture & Outdoor
            'Gardener',
            'Farm Worker',
            'Field Worker',
        ];

        foreach ($skills as $skill) {
            Skill::updateOrCreate(
                ['title' => $skill]
            );
        }
    }
}
