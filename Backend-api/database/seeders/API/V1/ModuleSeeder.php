<?php

namespace Database\Seeders\API\V1;

use Illuminate\Database\Seeder;
use App\Models\API\V1\Grade\Module;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            ['code' => 'CSC101', 'name' => 'Mathematics', 'description' => 'Element of Communication', 'credit_value' => 20, 'semester' => 'Spring'],
            ['code' => 'CSCD102', 'name' => 'Science', 'description' => 'Compiler Construction', 'credit_value' => 20, 'semester' => 'Spring'],
            ['code' => 'CSC103', 'name' => 'English', 'description' => 'Introduction to Software Development', 'credit_value' => 20, 'semester' => 'Spring'],
        ];

        foreach ($modules as $data) {
            Module::create($data);
        }
    }
}
