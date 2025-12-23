<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Driver',
                'description' => 'Car, delivery and commercial vehicle drivers',
                'category_icon' => 'icons/driver.png',
                'image' => 'categories/driver.jpg',
            ],
            [
                'name' => 'Cook',
                'description' => 'Home cook, restaurant cook and kitchen helpers',
                'category_icon' => 'icons/cook.png',
                'image' => 'categories/cook.jpg',
            ],
            [
                'name' => 'Housekeeping',
                'description' => 'Home maid, cleaner, dusting and mopping',
                'category_icon' => 'icons/housekeeping.png',
                'image' => 'categories/housekeeping.jpg',
            ],
            [
                'name' => 'Electrician',
                'description' => 'Wiring, repair and electrical maintenance',
                'category_icon' => 'icons/electrician.png',
                'image' => 'categories/electrician.jpg',
            ],
            [
                'name' => 'Plumber',
                'description' => 'Pipe fitting, water connection and repair work',
                'category_icon' => 'icons/plumber.png',
                'image' => 'categories/plumber.jpg',
            ],
            [
                'name' => 'Carpenter',
                'description' => 'Furniture fitting, wood work and repairs',
                'category_icon' => 'icons/carpenter.png',
                'image' => 'categories/carpenter.jpg',
            ],
            [
                'name' => 'Security Guard',
                'description' => 'Residential, commercial and event security',
                'category_icon' => 'icons/security.png',
                'image' => 'categories/security.jpg',
            ],
            [
                'name' => 'Factory Worker',
                'description' => 'Labour helpers and machine operators',
                'category_icon' => 'icons/factory.png',
                'image' => 'categories/factory.jpg',
            ],
            [
                'name' => 'Retail Staff',
                'description' => 'Sales boys/girls and billing staff',
                'category_icon' => 'icons/retail.png',
                'image' => 'categories/retail.jpg',
            ],
            [
                'name' => 'Babysitter / Nanny',
                'description' => 'Child care and baby care helpers',
                'category_icon' => 'icons/nanny.png',
                'image' => 'categories/nanny.jpg',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                [
                    ...$category,
                    'uuid' => Str::uuid()->toString(),
                    'slug' => Str::slug($category['name']),
                ]
            );
        }
    }
}
