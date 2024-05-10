<?php

namespace Database\Seeders;

use App\Models\Website;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Seeder;

class WebsiteSeeder extends Seeder
{
    public function run(): void
    {
        Website::truncate();

        // website 1
        $website_1 = Website::create([
            'website_name' => 'Website 1',
            'website_url' => 'http://project_management_system.test/',
            'website_logo_path' => '',
            'google_rank' => rand(1, 95),
            'client_id' => 1,
            'time' => 2400,
            'total_update' => rand(5, 10),
            'is_backup_active' => rand(0, 1),
            'total_site_helth' => rand(1, 5),
            'total_php_issue' => rand(2, 20),
            'wp_admin_url' => 'https://wordpress.org/documentation/',
        ]);
        $this->storeFile($website_1);


        // website 2
        $website_2 = Website::create([
            'website_name' => 'Website 2',
            'website_url' => 'http://project_management_system.test/',
            'website_logo_path' => '',
            'google_rank' => rand(1, 95),
            'client_id' => 1,
            'time' => 2400,
            'total_update' => rand(5, 10),
            'is_backup_active' => rand(0, 1),
            'total_site_helth' => rand(1, 5),
            'total_php_issue' => rand(2, 20),
            'wp_admin_url' => 'https://wordpress.org/documentation/',
        ]);
        $this->storeFile($website_2);


        // website 3
        $website_3 = Website::create([
            'website_name' => 'Website 3',
            'website_url' => 'http://project_management_system.test/',
            'website_logo_path' => '',
            'google_rank' => rand(1, 95),
            'client_id' => 2,
            'time' => 2400,
            'total_update' => rand(5, 10),
            'is_backup_active' => rand(0, 1),
            'total_site_helth' => rand(1, 5),
            'total_php_issue' => rand(2, 20),
            'wp_admin_url' => 'https://wordpress.org/documentation/',
        ]);
        $this->storeFile($website_3);


        // website 4
        $website_4 = Website::create([
            'website_name' => 'Website 4',
            'website_url' => 'http://project_management_system.test/',
            'website_logo_path' => '',
            'google_rank' => rand(1, 95),
            'client_id' => 2,
            'time' => 2400,
            'total_update' => rand(5, 10),
            'is_backup_active' => rand(0, 1),
            'total_site_helth' => rand(1, 5),
            'total_php_issue' => rand(2, 20),
            'wp_admin_url' => 'https://wordpress.org/documentation/',
        ]);
        $this->storeFile($website_4);


        // website 5
        $website_5 = Website::create([
            'website_name' => 'Website 5',
            'website_url' => 'http://project_management_system.test/',
            'website_logo_path' => '',
            'google_rank' => rand(1, 95),
            'client_id' => 2,
            'time' => 2400,
            'total_update' => rand(5, 10),
            'is_backup_active' => rand(0, 1),
            'total_site_helth' => rand(1, 5),
            'total_php_issue' => rand(2, 20),
            'wp_admin_url' => 'https://wordpress.org/documentation/',
        ]);
        $this->storeFile($website_5);


        // website 6
        $website_6 = Website::create([
            'website_name' => 'Website 6',
            'website_url' => 'http://project_management_system.test/',
            'website_logo_path' => '',
            'google_rank' => rand(1, 95),
            'client_id' => 5,
            'time' => 2400,
            'total_update' => rand(5, 10),
            'is_backup_active' => rand(0, 1),
            'total_site_helth' => rand(1, 5),
            'total_php_issue' => rand(2, 20),
            'wp_admin_url' => 'https://wordpress.org/documentation/',
        ]);
        $this->storeFile($website_6);

        // website 7
        $website_7 = Website::create([
            'website_name' => 'Website 7',
            'website_url' => 'http://project_management_system.test/',
            'website_logo_path' => '',
            'google_rank' => rand(1, 95),
            'client_id' => 5,
            'time' => 2400,
            'total_update' => rand(5, 10),
            'is_backup_active' => rand(0, 1),
            'total_site_helth' => rand(1, 5),
            'total_php_issue' => rand(2, 20),
            'wp_admin_url' => 'https://wordpress.org/documentation/',
        ]);
        $this->storeFile($website_7);

        // website 8
        $website_8 = Website::create([
            'website_name' => 'Website 8',
            'website_url' => 'http://project_management_system.test/',
            'website_logo_path' => '',
            'google_rank' => rand(1, 95),
            'client_id' => 4,
            'time' => 2400,
            'total_update' => rand(5, 10),
            'is_backup_active' => rand(0, 1),
            'total_site_helth' => rand(1, 5),
            'total_php_issue' => rand(2, 20),
            'wp_admin_url' => 'https://wordpress.org/documentation/',
        ]);
        $this->storeFile($website_8);


        // website 9
        $website_9 = Website::create([
            'website_name' => 'Website 9',
            'website_url' => 'http://project_management_system.test/',
            'website_logo_path' => '',
            'google_rank' => rand(1, 95),
            'client_id' => 4,
            'time' => 2400,
            'total_update' => rand(5, 10),
            'is_backup_active' => rand(0, 1),
            'total_site_helth' => rand(1, 5),
            'total_php_issue' => rand(2, 20),
            'wp_admin_url' => 'https://wordpress.org/documentation/',
        ]);
        $this->storeFile($website_9);

        // website 10
        $website_10 = Website::create([
            'website_name' => 'Website 10',
            'website_url' => 'http://project_management_system.test/',
            'website_logo_path' => '',
            'google_rank' => rand(1, 95),
            'client_id' => 3,
            'time' => 2400,
            'total_update' => rand(5, 10),
            'is_backup_active' => rand(0, 1),
            'total_site_helth' => rand(1, 5),
            'total_php_issue' => rand(2, 20),
            'wp_admin_url' => 'https://wordpress.org/documentation/',
        ]);
        $this->storeFile($website_10);
    }

    private function storeFile(Website $project)
    {
        $sourceFilePath = public_path('/images/wordpress.png');
        $destinationPath = public_path('/uploads/websites/' . $project->id);
        $fileName = time() . '.jpg';

        if (File::exists($destinationPath . '/' . $fileName)) {
            return 'File already exists!';
        }

        if (!File::isDirectory($destinationPath)) {
            File::makeDirectory($destinationPath, 0777, true, true);
        }

        File::copy($sourceFilePath, $destinationPath . '/' . $fileName);

        $project->fill([
            'website_logo_path' => '/uploads/websites/' . $project->id . '/' . $fileName,
        ])->save();
    }
}