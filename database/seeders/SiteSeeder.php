<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Site;
use Illuminate\Database\Seeder;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $company = Company::first();

        if ($company) {
            Site::create([
                'company_id' => $company->id,
                'name' => 'Site Principal - '.$company->name,
                'status' => \App\Enums\SiteStatus::Development,
                'visual_settings' => [],
                'default_meta_title' => $company->name,
                'default_meta_description' => 'Bem-vindo ao site principal da '.$company->name,
                'mail_default_recipient' => 'contato@'.strtolower(str_replace(' ', '', $company->name)).'.com',
            ]);
        }
    }
}
