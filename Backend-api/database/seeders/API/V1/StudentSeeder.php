<?php

namespace Database\Seeders\API\V1;

use Illuminate\Database\Seeder;
use App\Models\API\V1\Grade\Student;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        Student::factory()->count(10)->create();
    }
}
