<?php

namespace Database\Seeders;

use App\Enums;
use App\Models;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'pay',
        ];

        foreach ($permissions as $permissionSlug) {
            Permission::updateOrCreate(
                ['slug' => $permissionSlug],
                [],
            );
        }

        $personRole = Models\Role::whereSlug(Enums\Role::Person)->first();
        $personRolePermissionsSlugs = [
            'pay',
        ];

        $personRolePermissions = Permission::whereIn('slug', $personRolePermissionsSlugs)->pluck('id');
        $personRole->permissions()->sync($personRolePermissions);
    }
}
