<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create parent categories (Food, Drink, Set-Meal)
        $parentCategories = [
            [
                'name' => 'Food',
                'type' => 'food',
                'parent_id' => null,
                'sort_order' => 1
            ],
            [
                'name' => 'Drink',
                'type' => 'drink',
                'parent_id' => null,
                'sort_order' => 2
            ],
            [
                'name' => 'Set Meal',
                'type' => 'set-meal',
                'parent_id' => null,
                'sort_order' => 3
            ]
        ];

        foreach ($parentCategories as $categoryData) {
            // Only create if it doesn't exist
            Category::firstOrCreate(
                ['name' => $categoryData['name'], 'parent_id' => $categoryData['parent_id']],
                $categoryData
            );
        }
    }
}