<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create the Master Super Admin Account
        User::create([
            'name'              => 'System Super Admin',
            'email'             => 'superadmin@deped.gov.ph',
            'password'          => Hash::make('password123'),
            'role'              => 'super_admin',
            'status'            => 1,
            'email_verified_at' => now(),
        ]);
        
        // Removed the deped_planning call from here
    }
}