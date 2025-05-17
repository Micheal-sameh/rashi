<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'super_admin',
            'email' => 'super_admin@rashi.com',
            'membership_code' => 'E1C1F1NR1',
            'phone' => '01234567890',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole('admin');
    }
}
