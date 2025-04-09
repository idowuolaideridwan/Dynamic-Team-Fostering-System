<?php

namespace Database\Factories;

use App\Models\API\V1\Grade\Student;
use App\Models\API\V1\Grade\Module;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'student_id' => '123' . $this->faker->unique()->numerify('###S'),
            'first_name' => $this->faker->firstName,
            'last_name'  => $this->faker->lastName,
            'dob'        => $this->faker->date('Y-m-d', '2002-01-01'),
        ];
    }

    public function hasProfile()
    {
        return $this->afterCreating(function ($student) {
            $student->profile()->create([
                'email' => $this->faker->safeEmail,
                'phone' => $this->faker->phoneNumber,
                'gender' => $this->faker->randomElement(['male', 'female', 'other']),
                'enrollment_year' => $this->faker->year('now'),
            ]);
        });
    }

    public function hasGrades($count = 3)
    {
        return $this->afterCreating(function ($student) use ($count) {
            $modules = Module::factory()->count($count)->create();
            foreach ($modules as $module) {
                $student->grades()->create([
                    'module_id' => $module->id,
                    'grade'     => rand(30, 90),
                    'graded_at' => now(),
                ]);
            }
        });
    }
}
