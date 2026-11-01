<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Bu Sari (Koordinator)', 'email' => 'koordinator@mbg.test', 'role' => 'koordinator'],
            ['name' => 'Pak Dedi (Pemantau)', 'email' => 'pemantau@mbg.test', 'role' => 'pemantau'],
            ['name' => 'Pak Budi (Pemantau 2)', 'email' => 'pemantau2@mbg.test', 'role' => 'pemantau'],
            ['name' => 'Bu Rina (Pelapor)', 'email' => 'pelapor@mbg.test', 'role' => 'pelapor'],
            ['name' => 'Bu Ani (Pelapor 2)', 'email' => 'pelapor2@mbg.test', 'role' => 'pelapor'],
        ];

        foreach ($users as $u) {
            User::unguarded(fn() => User::updateOrCreate(
                ['email' => $u['email']], 
                $u + ['password' => 'password']
            ));
        }
    }
}
