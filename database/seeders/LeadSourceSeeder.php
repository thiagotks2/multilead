<?php

namespace Database\Seeders;

use App\Models\LeadSource;
use Illuminate\Database\Seeder;

class LeadSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            'Facebook',
            'Instagram',
            'WhatsApp',
            'Institutional Website',
            'Email',
            'Phone Call',
        ];

        foreach ($defaults as $source) {
            LeadSource::firstOrCreate(
                ['name' => $source],
                ['company_id' => null] // Global orphaned sources available to all companies
            );
        }
    }
}
