<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        Permission::create(['name' => 'manage users']);
        Permission::create(['name' => 'manage roles']);
        Permission::create(['name' => 'manage activity']);
        Permission::create(['name' => 'manage achievement']);
        Permission::create(['name' => 'manage finance']);
        Role::create(['name' => 'Super Admin'])->syncPermissions([1, 2, 3, 4, 5]);
        Role::create(['name' => 'Alumni']);
        Role::create(['name' => 'BPH']);
        Role::create(['name' => 'SC']);
        Role::create(['name' => 'OC']);
        User::create([
            'name'          => 'Super Admin',
            'username'      => 'superadmin',
            'phonenumber'   => '082244101304',
            'email'         => 'pramudya.wibowo72@gmail.com',
            'password'      => bcrypt('EEPROM2023'),
            'email_verified_at' => now(),
        ])->assignRole('Super Admin');
    }
}
