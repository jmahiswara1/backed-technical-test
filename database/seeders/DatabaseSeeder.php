<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Gunakan firstOrCreate agar aman dijalankan berulang kali (idempotent)
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => 'password', // Password akan di-hash otomatis oleh model cast
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'employee@example.com'],
            [
                'name' => 'Employee',
                'password' => 'password',
                'role' => 'employee',
            ]
        );
    }
}
