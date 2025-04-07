<?php

namespace Database\Seeders\API\V1;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\API\V1\Grade\Student;
use App\Models\API\V1\Grade\StudentProfile;
use Faker\Factory as Faker;

class StudentProfileSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        foreach (Student::all() as $student) {
            StudentProfile::create([
                'student_id' => $student->id,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'gender' => $faker->randomElement(['male', 'female', 'other']),
                'address' => $faker->address,
                'enrollment_year' => $faker->year('now'),
            ]);
        }
    }
}
