<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $view_all_orders_permission = Permission::firstOrCreate(['name' => 'view_all_orders']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo([
            $view_all_orders_permission,
        ]);

        $user = Role::firstOrCreate(['name' => 'user']);
        $user->givePermissionTo([

        ]);

    }
}
