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
                'is_default' => true,
            ]);

            $pipeline1->stages()->createMany([
                ['name' => 'Inbox', 'color' => 'gray', 'order_column' => 1, 'is_default' => true],
                ['name' => 'Contato Feito', 'color' => 'info', 'order_column' => 2, 'is_default' => false],
                ['name' => 'Reunião Agendada', 'color' => 'warning', 'order_column' => 3, 'is_default' => false],
                ['name' => 'Proposta Enviada', 'color' => 'primary', 'order_column' => 4, 'is_default' => false],
                ['name' => 'Fechado/Ganho', 'color' => 'success', 'order_column' => 5, 'is_default' => false],
            ]);
        }

        // Company 2 Funnel
        $c2 = Company::find(2);
        if ($c2) {
            $pipeline2 = Pipeline::create([
                'company_id' => $c2->id,
                'name' => 'Captação de Alunos',
                'is_default' => true,
            ]);

            $pipeline2->stages()->createMany([
                ['name' => 'Inbox', 'color' => 'gray', 'order_column' => 1, 'is_default' => true],
                ['name' => 'Qualificação', 'color' => 'warning', 'order_column' => 2, 'is_default' => false],
                ['name' => 'Matriculado', 'color' => 'success', 'order_column' => 3, 'is_default' => false],
                ['name' => 'Desistência', 'color' => 'danger', 'order_column' => 4, 'is_default' => false],
            ]);
        }
    }
}
