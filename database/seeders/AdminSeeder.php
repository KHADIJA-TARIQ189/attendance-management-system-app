<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@school.test'], [
            'name' => 'System Admin',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::updateOrCreate(['email' => 'teacher@school.test'], [
            'name' => 'Sample Teacher',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        User::updateOrCreate(['email' => 'student@school.test'], [
            'name' => 'Sample Student',
            'password' => Hash::make('password'),
            'role' => 'student',
            'roll_no' => 'S-2201',
            'lora_tag_id' => 'S-2201',
        ]);
    }
}
