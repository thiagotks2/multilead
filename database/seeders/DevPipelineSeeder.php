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
                'name' => 'Funil de Vendas B2B',
            ]);

            $pipeline1->stages()->createMany([
                ['name' => 'Inbox', 'color' => 'gray', 'order_column' => 1],
                ['name' => 'Contato Feito', 'color' => 'info', 'order_column' => 2],
                ['name' => 'Reunião Agendada', 'color' => 'warning', 'order_column' => 3],
                ['name' => 'Proposta Enviada', 'color' => 'primary', 'order_column' => 4],
                ['name' => 'Fechado/Ganho', 'color' => 'success', 'order_column' => 5],
            ]);
        }

        // Company 2 Funnel
        $c2 = Company::find(2);
        if ($c2) {
            $pipeline2 = Pipeline::create([
                'company_id' => $c2->id,
                'name' => 'Captação de Alunos',
            ]);

            $pipeline2->stages()->createMany([
                ['name' => 'Inbox', 'color' => 'gray', 'order_column' => 1],
                ['name' => 'Qualificação', 'color' => 'warning', 'order_column' => 2],
                ['name' => 'Matriculado', 'color' => 'success', 'order_column' => 3],
                ['name' => 'Desistência', 'color' => 'danger', 'order_column' => 4],
            ]);
        }
    }
}
