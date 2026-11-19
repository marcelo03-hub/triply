<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        User::updateOrCreate(
            ['email' => 'admin@triply.id'],
            [
                'name' => 'Administrator Triply',
                'password' => Hash::make('password123'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        // Regular demo users
        User::updateOrCreate(
            ['email' => 'traveler@triply.id'],
            [
                'name' => 'Budi Petualang',
                'password' => Hash::make('password123'),
                'is_admin' => false,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'demo@triply.id'],
            [
                'name' => 'Siti Traveler',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'email_verified_at' => now(),
            ]
        );
    }
}
