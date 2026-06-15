<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@vms.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
            ]
        );
        $admin->assignRole('Admin');

        $staff = User::firstOrCreate(
            ['email' => 'staff@vms.com'],
            [
                'name' => 'Staff User',
                'password' => bcrypt('password'),
            ]
        );
        $staff->assignRole('Staff');
        
        $driver = User::firstOrCreate(
            ['email' => 'driver@vms.com'],
            [
                'name' => 'Driver User',
                'password' => bcrypt('password'),
            ]
        );
        $driver->assignRole('Driver');
    }
}
