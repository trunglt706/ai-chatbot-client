<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            ['code' => 'chatbot', 'name' => 'Chatbot', 'status' => 2, 'version' => '1.0.2', 'url' => '/chatbot'],
        ];

        foreach ($modules as $moduleData) {
            Module::firstOrCreate(['code' => $moduleData['name']], $moduleData);
        }
    }
}
