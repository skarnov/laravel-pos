<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Configurations extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('configurations')->insert([
            'config_name' => 'project_name',
            'config_setting' => '',
        ]);
        DB::table('configurations')->insert([
            'config_name' => 'meta_description',
            'config_setting' => '',
        ]);
        DB::table('configurations')->insert([
            'config_name' => 'website_favicon',
            'config_setting' => '',
        ]);
        DB::table('configurations')->insert([
            'config_name' => 'website_logo',
            'config_setting' => '',
        ]);
        DB::table('configurations')->insert([
            'config_name' => 'company_address',
            'config_setting' => '',
        ]);
        DB::table('configurations')->insert([
            'config_name' => 'company_phone',
            'config_setting' => '',
        ]);
        DB::table('configurations')->insert([
            'config_name' => 'company_email',
            'config_setting' => '',
        ]);
        DB::table('configurations')->insert([
            'config_name' => 'copyright_info',
            'config_setting' => '',
        ]);
        DB::table('configurations')->insert([
            'config_name' => 'contact_email',
            'config_setting' => '',
        ]);
        DB::table('configurations')->insert([
            'config_name' => 'dashboard_favicon',
            'config_setting' => '',
        ]);
        DB::table('configurations')->insert([
            'config_name' => 'dashboard_logo',
            'config_setting' => '',
        ]);
        DB::table('configurations')->insert([
            'config_name' => 'time_zone',
            'config_setting' => 'Asia/Dhaka',
        ]);
        DB::table('configurations')->insert([
            'config_name' => 'time_format',
            'config_setting' => 'g:i A',
        ]);
        DB::table('configurations')->insert([
            'config_name' => 'date_format',
            'config_setting' => 'F j, Y',
        ]);
        DB::table('configurations')->insert([
            'config_name' => 'add_button',
            'config_setting' => 'btn btn-success btn-sm',
        ]);
        DB::table('configurations')->insert([
            'config_name' => 'edit_button',
            'config_setting' => 'btn btn-primary btn-sm',
        ]);
        DB::table('configurations')->insert([
            'config_name' => 'add_icon',
            'config_setting' => '<i class="fa fa-save"></i>',
        ]);
        DB::table('configurations')->insert([
            'config_name' => 'edit_icon',
            'config_setting' => '<i class="fa fa-edit"></i>',
        ]);
        DB::table('configurations')->insert([
            'config_name' => 'currency_name',
            'config_setting' => '',
        ]);
        DB::table('configurations')->insert([
            'config_name' => 'currency_sign',
            'config_setting' => '',
        ]);
        DB::table('configurations')->insert([
            'config_name' => 'page_image',
            'config_setting' => '',
        ]);
        DB::table('configurations')->insert([
            'config_name' => 'facebook_link',
            'config_setting' => '',
        ]);
        DB::table('configurations')->insert([
            'config_name' => 'linkedin_link',
            'config_setting' => '',
        ]);
        DB::table('configurations')->insert([
            'config_name' => 'instagram_link',
            'config_setting' => '',
        ]);
        DB::table('configurations')->insert([
            'config_name' => 'pinterest_link',
            'config_setting' => '',
        ]);
        DB::table('configurations')->insert([
            'config_name' => 'youtube_link',
            'config_setting' => '',
        ]);
        DB::table('configurations')->insert([
            'config_name' => 'google_analytics',
            'config_setting' => '',
        ]);
    }
}
