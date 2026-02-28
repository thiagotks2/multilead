<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Pipeline;
use Illuminate\Database\Seeder;

class DevPipelineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Company 1 Funnel
        $c1 = Company::find(1);
        if ($c1) {
            $pipeline1 = Pipeline::create([
                'company_id' => $c1->id,
                'name' => 'B2B Sales Funnel',
                'is_default' => true,
            ]);

            $pipeline1->stages()->createMany([
                ['name' => 'Inbox', 'color' => '#64748b', 'order_column' => 1, 'is_default' => true, 'is_visible' => true],
                ['name' => 'Contacted', 'color' => '#3b82f6', 'order_column' => 2, 'is_default' => false, 'is_visible' => true],
                ['name' => 'Meeting Scheduled', 'color' => '#eab308', 'order_column' => 3, 'is_default' => false, 'is_visible' => true],
                ['name' => 'Proposal Sent', 'color' => '#8b5cf6', 'order_column' => 4, 'is_default' => false, 'is_visible' => true],
                ['name' => 'Won', 'color' => '#22c55e', 'order_column' => 5, 'is_default' => false, 'is_visible' => true],
                ['name' => 'Lost', 'color' => '#ef4444', 'order_column' => 6, 'is_default' => false, 'is_visible' => false],
            ]);
        }

        // Company 2 Funnel
        $c2 = Company::find(2);
        if ($c2) {
            $pipeline2 = Pipeline::create([
                'company_id' => $c2->id,
                'name' => 'Student Enrollment',
                'is_default' => true,
            ]);

            $pipeline2->stages()->createMany([
                ['name' => 'Inbox', 'color' => '#64748b', 'order_column' => 1, 'is_default' => true, 'is_visible' => true],
                ['name' => 'Qualification', 'color' => '#eab308', 'order_column' => 2, 'is_default' => false, 'is_visible' => true],
                ['name' => 'Enrolled', 'color' => '#22c55e', 'order_column' => 3, 'is_default' => false, 'is_visible' => true],
                ['name' => 'Dropped Out', 'color' => '#ef4444', 'order_column' => 4, 'is_default' => false, 'is_visible' => false],
            ]);
        }
    }
}
