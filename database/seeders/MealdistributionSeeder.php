<?php

namespace Database\Seeders;

use App\Models\MealDistribution;
use App\Models\School;
use Illuminate\Database\Seeder;

class MealDistributionSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            'Nasi, ayam goreng, tumis sayur, buah pisang',
            'Nasi, telur balado, sayur sop, jeruk',
            'Nasi, ikan goreng, capcay, semangka',
        ];

        foreach (School::all() as $school) {
            foreach ([2, 5, 9] as $i => $daysAgo) {
                MealDistribution::updateOrCreate(
                    [
                        'id_school' => $school->id_school,
                        'distribution_date' => now()->subDays($daysAgo)->toDateString(),
                    ],
                    [
                        'menu_description' => $menus[$i],
                        'portion_count' => 200 + ($i * 25),
                    ]
                );
            }
        }
    }
}