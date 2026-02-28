<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('companies')->insert([
            ['name' => 'Company 1', 'active' => true, 'document_type' => 'CNPJ', 'document_number' => '1234.1343.1/2000-00'],
            ['name' => 'Company 2', 'active' => true, 'document_type' => 'CNPJ', 'document_number' => '9876.5432.1/0001-99'],
        ]);
    }
}
