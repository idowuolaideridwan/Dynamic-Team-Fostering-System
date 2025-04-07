<?php

namespace Database\Seeders\API\V1;

use Illuminate\Database\Seeder;
use App\Models\API\V1\Grade\Student;
use App\Models\API\V1\Grade\Module;
use App\Models\API\V1\Grade\Grade;

class GradeSeeder extends Seeder
{
    public function run(): void
    {
        $modules = Module::all();
        $students = Student::all();

        foreach ($students as $student) {
            foreach ($modules as $module) {
                Grade::create([
                    'student_id' => $student->id,
                    'module_id'  => $module->id,
                    'grade'      => rand(35, 85),
                    'comments'   => null,
                    'graded_at'  => now()->subDays(rand(1, 90)),
                ]);
            }
        }
    }
}
