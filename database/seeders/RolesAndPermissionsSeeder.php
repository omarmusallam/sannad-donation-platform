<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cached permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guard = 'web';

        /**
         * Permissions list
         * - Keep names EXACTLY as used in routes middleware & @can in blade
         */
        $perms = [
            // Dashboard
            'dashboard.view',

            // Campaigns
            'campaigns.view',
            'campaigns.create',
            'campaigns.edit',
            'campaigns.delete',

            // Campaign Updates
            'campaign_updates.view',
            'campaign_updates.create',
            'campaign_updates.edit',
            'campaign_updates.delete',

            // Donations
            'donations.view',

            // Receipts
            'receipts.view',
            'receipts.create',
            'receipts.send',
            'receipts.export',

            // Reports (PDF reports)
            'reports.view',
            'reports.create',
            'reports.edit',
            'reports.delete',

            // Finance Reports (NEW)
            'finance_reports.view',
            'finance_reports.export', // optional for later

            // CMS Pages
            'pages.view',
            'pages.create',
            'pages.edit',
            'pages.delete',

            // Admin
            'settings.manage',
            'users.manage',
            'roles.manage',
        ];

        // Create/ensure permissions
        foreach ($perms as $p) {
            Permission::firstOrCreate([
                'name' => $p,
                'guard_name' => $guard,
            ]);
        }

        // Roles (ensure same guard)
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => $guard]);
        $admin      = Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
        $editor     = Role::firstOrCreate(['name' => 'editor', 'guard_name' => $guard]);
        $finance    = Role::firstOrCreate(['name' => 'finance', 'guard_name' => $guard]);

        /**
         * Role Permissions
         */
        // super_admin: everything
        $superAdmin->syncPermissions($perms);

        // admin: everything
        $admin->syncPermissions($perms);

        // editor: content only
        $editor->syncPermissions([
            'dashboard.view',

            'campaigns.view',
            'campaigns.create',
            'campaigns.edit',

            'campaign_updates.view',
            'campaign_updates.create',
            'campaign_updates.edit',

            'reports.view',
            'reports.create',
            'reports.edit',

            'pages.view',
            'pages.create',
            'pages.edit',
        ]);

        // finance: donations + receipts + reports + finance reports
        $finance->syncPermissions([
            'dashboard.view',

            'donations.view',

            'receipts.view',
            'receipts.create',
            'receipts.send',
            // 'receipts.export', // optional

            'reports.view',

            'finance_reports.view',
            // 'finance_reports.export', // optional
        ]);

        // Clear cache again just to be safe
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
