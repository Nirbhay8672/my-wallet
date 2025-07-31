<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::truncate();

        DB::table('model_has_roles')->truncate();
        DB::table('role_has_permissions')->truncate();
        Role::truncate();
        Permission::truncate();

        $permissions_array = [
            // dashboard
            ['display_name' => 'View Dashboard', 'name' => 'view_dashboard', 'category' => 'Dashboard', 'guard_name' => 'web'],

            // profile
            ['display_name' => 'View Profile', 'name' => 'view_profile', 'category' => 'Profile', 'guard_name' => 'web'],
            ['display_name' => 'Update Profile', 'name' => 'update_profile', 'category' => 'Profile', 'guard_name' => 'web'],

            // users
            ['display_name' => 'View Users', 'name' => 'view_users', 'category' => 'User' , 'guard_name' => 'web'],
            ['display_name' => 'Add User', 'name' => 'add_user', 'category' => 'User' , 'guard_name' => 'web'],
            ['display_name' => 'Update User', 'name' => 'update_user', 'category' => 'User' , 'guard_name' => 'web'],
            ['display_name' => 'Delete User', 'name' => 'delete_user', 'category' => 'User' , 'guard_name' => 'web'],

            // permissions
            ['display_name' => 'View Permissions', 'name' => 'view_permissions', 'category' => 'Permission' , 'guard_name' => 'web'],
            ['display_name' => 'Update Permissions', 'name' => 'update_permission', 'category' => 'Permission' , 'guard_name' => 'web'],

            // sources
            ['display_name' => 'View Sources', 'name' => 'view_sources', 'category' => 'Source' , 'guard_name' => 'web'],
            ['display_name' => 'Add Source', 'name' => 'add_source', 'category' => 'Source' , 'guard_name' => 'web'],
            ['display_name' => 'Update Source', 'name' => 'update_source', 'category' => 'Source' , 'guard_name' => 'web'],
            ['display_name' => 'Delete Source', 'name' => 'delete_source', 'category' => 'Source' , 'guard_name' => 'web'],

            // banks
            ['display_name' => 'View Banks', 'name' => 'view_banks', 'category' => 'Bank', 'guard_name' => 'web'],
            ['display_name' => 'Add Bank', 'name' => 'add_bank', 'category' => 'Bank', 'guard_name' => 'web'],
            ['display_name' => 'Edit Bank', 'name' => 'edit_bank', 'category' => 'Bank', 'guard_name' => 'web'],
            ['display_name' => 'Delete Bank', 'name' => 'delete_bank', 'category' => 'Bank', 'guard_name' => 'web'],

            // accounts
            ['display_name' => 'View Accounts', 'name' => 'view_accounts', 'category' => 'Account', 'guard_name' => 'web'],
            ['display_name' => 'Add Account', 'name' => 'add_account', 'category' => 'Account', 'guard_name' => 'web'],
            ['display_name' => 'Edit Account', 'name' => 'edit_account', 'category' => 'Account', 'guard_name' => 'web'],
            ['display_name' => 'Delete Account', 'name' => 'delete_account', 'category' => 'Account', 'guard_name' => 'web'],

            // income types
            ['display_name' => 'View Income Types', 'name' => 'view_income_types', 'category' => 'Income Type', 'guard_name' => 'web'],
            ['display_name' => 'Add Income Type', 'name' => 'add_income_type', 'category' => 'Income Type', 'guard_name' => 'web'],
            ['display_name' => 'Edit Income Type', 'name' => 'edit_income_type', 'category' => 'Income Type', 'guard_name' => 'web'],
            ['display_name' => 'Delete Income Type', 'name' => 'delete_income_type', 'category' => 'Income Type', 'guard_name' => 'web'],

            // expense types
            ['display_name' => 'View Expense Types', 'name' => 'view_expense_types', 'category' => 'Expense Type', 'guard_name' => 'web'],
            ['display_name' => 'Add Expense Type', 'name' => 'add_expense_type', 'category' => 'Expense Type', 'guard_name' => 'web'],
            ['display_name' => 'Edit Expense Type', 'name' => 'edit_expense_type', 'category' => 'Expense Type', 'guard_name' => 'web'],
            ['display_name' => 'Delete Expense Type', 'name' => 'delete_expense_type', 'category' => 'Expense Type', 'guard_name' => 'web'],
        ];

        DB::table('permissions')->insert($permissions_array);
        $permissionCollection = collect($permissions_array);

        // super admin start
        $super_admin_role = Role::create([
            'name' => 'super_admin',
            'display_name' => 'Super Admin',
        ]);

        $super_admin_role->givePermissionTo($permissionCollection->pluck('name'));

        $super_admin = User::create([
            'name' => 'Admin',
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123123'),
            'profile_path' => '',
        ]);

        $super_admin->assignRole($super_admin_role);
        $this->storeProfileImage($super_admin);

        // super admin end


        // user start

        $user_permissions = [
            'view_dashboard',
            'view_profile',
            'update_profile',
            'view_accounts',
            'add_account',
            'edit_account',
            'delete_account',
        ];

        // user role
        $user_role = Role::create([
            'name' => 'user',
            'display_name' => 'User',
        ]);

        $user_role->givePermissionTo($user_permissions);

        $user_1 = User::create([
            'name' => 'Nux',
            'first_name' => 'Nirbhay',
            'last_name' => 'Hathaliya',
            'email' => 'user@gmail.com',
            'password' => bcrypt('123123'),
            'profile_path' => '',
        ]);

        $user_1->assignRole($user_role);
        $this->storeProfileImage($user_1);

        $user_2 = User::create([
            'name' => 'Usha',
            'first_name' => 'Usha',
            'last_name' => 'Hathaliya',
            'email' => 'ushavadher707@gmail.com',
            'password' => bcrypt('123123'),
            'profile_path' => '',
        ]);

        $user_2->assignRole($user_role);
        $this->storeProfileImage($user_2);

        // user end
    }

    public function storeProfileImage($admin)
    {
        $sourceFilePath = public_path('/images/profile.png');
        $destinationPath = public_path('/uploads/users/' . $admin->id);
        $fileName = time() . '.jpg';

        if (File::exists($destinationPath . '/' . $fileName)) {
            return 'File already exists!';
        }

        if (!File::isDirectory($destinationPath)) {
            File::makeDirectory($destinationPath, 0777, true, true);
        }

        File::copy($sourceFilePath, $destinationPath . '/' . $fileName);

        $admin->fill([
            'profile_path' => '/uploads/users/' . $admin->id . '/' . $fileName,
        ])->save();
    }
}
