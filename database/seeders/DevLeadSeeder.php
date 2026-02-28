<?php

namespace Database\Seeders;

use App\Enums\LeadMedium;
use App\Models\Company;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\Pipeline;
use App\Models\User;
use Illuminate\Database\Seeder;

class DevLeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Leads for Company 1
        $c1 = Company::find(1);
        if ($c1) {
            $pipeline1 = Pipeline::where('company_id', $c1->id)->first();
            $inbox1 = $pipeline1->stages()->where('name', 'Inbox')->first();
            $user1 = User::where('company_id', $c1->id)->first();
            $source1 = LeadSource::where('company_id', null)->inRandomOrder()->first(); // Global source

            Lead::create([
                'company_id' => $c1->id,
                'lead_source_id' => $source1?->id,
                'user_id' => $user1?->id,
                'pipeline_stage_id' => $inbox1?->id,
                'name' => 'João Silva (Teste C1)',
                'email' => 'joao.silva@teste1.com',
                'phone' => '11999999999',
                'message' => 'Gostaria de um orçamento B2B.',
                'medium' => LeadMedium::Organic,
            ]);

            Lead::create([
                'company_id' => $c1->id,
                'lead_source_id' => null,
                'user_id' => null, // Unassigned
                'pipeline_stage_id' => $inbox1?->id,
                'name' => 'Maria Souza (C1)',
                'email' => 'maria@teste1.com',
                'message' => 'Preciso de ajuda urgente com serviços.',
                'medium' => LeadMedium::Paid,
            ]);
        }

        // Seed Leads for Company 2
        $c2 = Company::find(2);
        if ($c2) {
            $pipeline2 = Pipeline::where('company_id', $c2->id)->first();
            $inbox2 = $pipeline2->stages()->where('name', 'Inbox')->first();
            $user2 = User::where('company_id', $c2->id)->first();

            // Randomly grab a custom tenant source if it exists
            $source2 = LeadSource::where('company_id', $c2->id)->inRandomOrder()->first();

            Lead::create([
                'company_id' => $c2->id,
                'lead_source_id' => $source2?->id,
                'user_id' => $user2?->id,
                'pipeline_stage_id' => $inbox2?->id,
                'name' => 'Carlos Estudante (Teste C2)',
                'email' => 'carlos@teste2.com',
                'phone' => '21988888888',
                'message' => 'Quero me matricular na próxima turma.',
                'medium' => LeadMedium::Paid,
            ]);

            Lead::create([
                'company_id' => $c2->id,
                'lead_source_id' => null,
                'user_id' => null, // Unassigned
                'pipeline_stage_id' => $inbox2?->id,
                'name' => 'Ana Curiosa (C2)',
                'email' => 'ana@teste2.com',
                'message' => 'Como funciona as aulas?',
                'medium' => LeadMedium::Organic,
            ]);
        }
    }
}
