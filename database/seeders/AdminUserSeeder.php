<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // تأكد أن role موجود (guard admin)
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'admin']
        );

        // أنشئ/حدّث المستخدم
        $user = User::updateOrCreate(
            ['email' => 'admin@site.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );

        // اسناد الدور
        if (! $user->hasRole('super_admin')) {
            $user->assignRole($superAdminRole);
        }
    }
}
