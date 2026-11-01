<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\SchoolSeeder;
use Database\Seeders\MealDistributionSeeder;   
use Database\Seeders\ReportSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SchoolSeeder::class,
            MealDistributionSeeder::class,
            ReportSeeder::class,
        ]);
    }
}