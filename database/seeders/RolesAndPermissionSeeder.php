<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
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
        $canGenerateQrCode = Permission::firstOrCreate(['name' => 'can_generate_qr_code']);

        $super_admin = Role::firstOrCreate(['name' => 'super_admin']);
        $super_admin->givePermissionTo([
            $view_all_orders_permission,
            $canGenerateQrCode,
        ]);

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo([
            $view_all_orders_permission,
        ]);

        $user = Role::firstOrCreate(['name' => 'user']);
        $user->givePermissionTo([

        ]);

        $userRole = User::find(1);
        if ($userRole) {
            $userRole->syncRoles($super_admin);
        }
    }
}
