<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesAndPermissionSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo([

        ]);

        $user = Role::firstOrCreate(['name' => 'user']);
        $user->givePermissionTo([

        ]);

    }
}
