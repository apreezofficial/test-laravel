<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin2@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Standard User
        User::create([
            'name' => 'Standard User',
            'email' => 'user2@test.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);
    }
}
