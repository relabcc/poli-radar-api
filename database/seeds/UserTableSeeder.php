<?php

use Illuminate\Database\Seeder;
use App\Role;
use App\Permission;
use App\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permission_role')->truncate();
        DB::table('role_user')->truncate();
        DB::table('users')->truncate();
        DB::table('permissions')->truncate();
        DB::table('roles')->truncate();

        $admin = Role::create([
            'name'         => 'admin',
            'display_name' => 'Administrator'
        ]);

        $manager = Role::create([
            'name'         => 'manager',
            'display_name' => 'Politician Manager'
        ]);

        $manageEvent = Permission::create([
            'name' => 'manage-event',
            'display_name' => 'Manage Events'
        ]);

        $admin->attachPermission($manageEvent);
        $manager->attachPermission($manageEvent);

        $user = User::create([
        	'name' => 'Administrator',
        	'email' => 'renddi@relab.cc',
        	'password' => Hash::make('renddi')
    	]);

        $user->attachRole($admin);

        $demo = User::create([
            'name' => 'Demo',
            'email' => 'demo@relab.cc',
            'password' => Hash::make('demo')
        ]);

        $demo->attachRole($manager);

    }
}
