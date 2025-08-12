<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::firstOrCreate(
            ['email' => 'superadmin@mishkatq.com'],
            [
                'name' => 'Super Admin',
                'password' => 'password',
            ]
        );

        // Assign Super Admin role if available
        if (method_exists($admin, 'assignRole')) {
            $admin->assignRole('Super Admin');
        }
    }
}
