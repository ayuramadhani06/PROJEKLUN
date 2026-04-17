<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin Lunanet',
            'username' => 'admin_lunanet', // Ini buat login
            'email' => 'admin@lunanet.id',
            'password' => Hash::make('admin123'), // Ini passwordnya
            'role' => 'admin',
            'permission' => 'manage'
        ]);
    }
}
