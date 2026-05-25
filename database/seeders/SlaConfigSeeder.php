<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SlaConfig;
use Illuminate\Database\Seeder;

class SlaConfigSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();

        $slaMatrix = [
            'critical' => 4,
            'high' => 24,
            'medium' => 72,
            'low' => 120,
        ];

        foreach ($categories as $category) {
            foreach ($slaMatrix as $priority => $hours) {
                SlaConfig::updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'priority' => $priority,
                    ],
                    [
                        'resolution_hours' => $hours,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
