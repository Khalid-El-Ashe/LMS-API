<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AdministratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $admin = Admin::query()->firstOrCreate(
            ['email' => 'administrator@areisto.com'],
            [
                'name' => 'Administrator',
                'username' => 'administrator',
                'password' => Hash::make('administrator@2025'),
                'email' => 'administrator@areisto.com',
            ]);
        $role = Role::query()->where('name', 'administrator')->first();
        $admin->assignRole($role->name);
    }
}
