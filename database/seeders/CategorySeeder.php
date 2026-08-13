<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Request',
                'slug' => 'request',
                'base_score' => 2,
                'subcategories' => [
                    'New Account',
                    'Access Request',
                    'Software Installation',
                    'Hardware Request',
                    'Information Request',
                    'Internet Access',
                    'Other Request',
                ],
            ],
            [
                'name' => 'Incident',
                'slug' => 'incident',
                'base_score' => 3,
                'subcategories' => [
                    'Network Issue',
                    'Hardware Failure',
                    'Software Error',
                    'Email Issue',
                    'Printer Issue',
                    'Security Incident',
                    'Performance Issue',
                    'Other Incident',
                ],
            ],
            [
                'name' => 'Change',
                'slug' => 'change',
                'base_score' => 5,
                'subcategories' => [
                    'Configuration Change',
                    'System Upgrade',
                    'Infrastructure Change',
                    'Application Change',
                    'Policy Change',
                    'Other Change',
                ],
            ],
        ];

        foreach ($categories as $catData) {
            $subcategories = $catData['subcategories'];
            unset($catData['subcategories']);

            $category = Category::updateOrCreate(['slug' => $catData['slug']], $catData);

            foreach ($subcategories as $subName) {
                Subcategory::updateOrCreate(
                    ['category_id' => $category->id, 'name' => $subName],
                    ['is_active' => true]
                );
            }
        }
    }
}
