<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Customer permissions
            'view customers',
            'create customers',
            'edit customers',
            'delete customers',

            // Lead permissions
            'view leads',
            'create leads',
            'edit leads',
            'delete leads',
            'assign leads',

            // Opportunity permissions
            'view opportunities',
            'create opportunities',
            'edit opportunities',
            'delete opportunities',

            // Campaign permissions
            'view campaigns',
            'create campaigns',
            'edit campaigns',
            'delete campaigns',

            // Activity permissions
            'view activities',
            'create activities',
            'edit activities',
            'delete activities',

            // Branch permissions
            'view branches',
            'create branches',
            'edit branches',
            'delete branches',

            // User permissions
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Permission permissions
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',

            // Report permissions
            'view reports',
            'export reports',

            // System permissions
            'manage system',
            'view all branches',
            'manage roles',
            'assign roles',
            'manage settings',
            'view system logs',
            'manage integrations',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions

        // Super Admin - Complete system access with all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->syncPermissions(Permission::all());

        // Area Manager - Full access across all branches
        $areaManager = Role::firstOrCreate(['name' => 'Area Manager']);
        $areaManager->syncPermissions(Permission::all());

        // Sales Manager - Full access within their branch
        $salesManager = Role::firstOrCreate(['name' => 'Sales Manager']);
        $salesManager->syncPermissions([
            'view customers',
            'create customers',
            'edit customers',
            'delete customers',
            'view leads',
            'create leads',
            'edit leads',
            'delete leads',
            'assign leads',
            'view opportunities',
            'create opportunities',
            'edit opportunities',
            'delete opportunities',
            'view campaigns',
            'create campaigns',
            'edit campaigns',
            'delete campaigns',
            'view activities',
            'create activities',
            'edit activities',
            'delete activities',
            'view users',
            'create users',
            'edit users',
            'view permissions',
            'view reports',
            'export reports',
        ]);

        // Sales Executive - Limited access to assigned leads and customers
        $salesExecutive = Role::firstOrCreate(['name' => 'Sales Executive']);
        $salesExecutive->syncPermissions([
            'view customers',
            'create customers',
            'edit customers',
            'view leads',
            'create leads',
            'edit leads',
            'view opportunities',
            'create opportunities',
            'edit opportunities',
            'view campaigns',
            'view activities',
            'create activities',
            'edit activities',
            'view reports',
        ]);
    }
}
