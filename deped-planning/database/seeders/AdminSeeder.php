<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void {
    \App\Models\User::create([
        'name' => 'Super Admin',
        'email' => 'superadmin@deped.gov.ph',
        'password' => bcrypt('password123'),
        'role' => 'super_admin', // Use 'admin' or 'super_admin'
        'status' => 'approved',   // Ensure status is approved
        'email_verified_at' => now(),
    ]);
}
}
