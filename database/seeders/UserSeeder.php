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

            // website
            ['display_name' => 'View Websites', 'name' => 'view_websites', 'category' => 'Website', 'guard_name' => 'web'],
            ['display_name' => 'Website Details', 'name' => 'website_details', 'category' => 'Website', 'guard_name' => 'web'],
            ['display_name' => 'Add Website', 'name' => 'add_website', 'category' => 'Website', 'guard_name' => 'web'],
            ['display_name' => 'Update Website', 'name' => 'update_website', 'category' => 'Website', 'guard_name' => 'web'],
            ['display_name' => 'Re-Sync Website', 'name' => 're_sync_website', 'category' => 'Website', 'guard_name' => 'web'],
            ['display_name' => 'View Website Payment', 'name' => 'view_website_payment', 'category' => 'Website', 'guard_name' => 'web'],

            // client
            ['display_name' => 'View Clients', 'name' => 'view_clients', 'category' => 'Client', 'guard_name' => 'web'],
            ['display_name' => 'Add Client', 'name' => 'add_client', 'category' => 'Client', 'guard_name' => 'web'],
            ['display_name' => 'Update Client', 'name' => 'update_client', 'category' => 'Client', 'guard_name' => 'web'],
            ['display_name' => 'Delete Client', 'name' => 'delete_website', 'category' => 'Client', 'guard_name' => 'web'],
            ['display_name' => 'Client Details', 'name' => 'client_details', 'category' => 'Client', 'guard_name' => 'web'],

            // payments
            ['display_name' => 'View Payments', 'name' => 'view_payments', 'category' => 'Payment' , 'guard_name' => 'web'],
            ['display_name' => 'Add Payment', 'name' => 'add_payment', 'category' => 'Payment' , 'guard_name' => 'web'],

            // users
            ['display_name' => 'View Users', 'name' => 'view_users', 'category' => 'User' , 'guard_name' => 'web'],
            ['display_name' => 'Add User', 'name' => 'add_user', 'category' => 'User' , 'guard_name' => 'web'],
            ['display_name' => 'Update User', 'name' => 'update_user', 'category' => 'User' , 'guard_name' => 'web'],
            ['display_name' => 'Delete User', 'name' => 'delete_user', 'category' => 'User' , 'guard_name' => 'web'],

            // permissions
            ['display_name' => 'View Permissions', 'name' => 'view_permissions', 'category' => 'Permission' , 'guard_name' => 'web'],
            ['display_name' => 'Update Permissions', 'name' => 'update_permission', 'category' => 'Permission' , 'guard_name' => 'web'],
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
            'name' => 'Gil',
            'first_name' => 'Gil',
            'last_name' => 'David', 
            'email' => 'gil@soulbounds.com',
            'password' => bcrypt('12345678'),
            'profile_path' => '',
        ]);

        $super_admin->assignRole($super_admin_role);
        $this->storeProfileImage($super_admin);

        // super admin end


        // developer start

        $developer_permissions = [
            'view_dashboard',
            'view_profile',
            'update_profile',
            'view_websites',
        ];
 
        // developer role
        $developer_role = Role::create([
            'name' => 'developer',
            'display_name' => 'Developer',
        ]);

        $developer = User::create([
            'name' => 'Nux',
            'first_name' => 'Nirbhay',
            'last_name' => 'Hathaliya', 
            'email' => 'nirbhay.cnc@gmail.com',
            'password' => bcrypt('12345678'),
            'profile_path' => '',
        ]);

        $developer->assignRole($developer_role);
        $this->storeProfileImage($developer);
        $developer_role->givePermissionTo($developer_permissions);

        // developer end
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