<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        $schools = [
            ['name' => 'SMP Harapan Bakti', 'npsn' => '10000001', 'address' => 'Jl. Coblong No. 199', 'kecamatan' => 'Coblong'],
            ['name' => 'SDN Cicendo 02', 'npsn' => '10000002', 'address' => 'Jl. Cicendo No. 212', 'kecamatan' => 'Cicendo'],
            ['name' => 'SMP Kopo Indah', 'npsn' => '10000003', 'address' => 'Jl. Kopo No. 321', 'kecamatan' => 'Kopo'],
            ['name' => 'SDN Lebak Jaya 05', 'npsn' => '10000004', 'address' => 'Jl. Lengkong No. 4', 'kecamatan' => 'Lengkong'],
        ];

        foreach ($schools as $s) {
            School::updateOrCreate(['npsn' => $s['npsn']], $s);
        }
    }
}