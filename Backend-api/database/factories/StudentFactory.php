<?php

namespace Database\Factories;

use App\Models\API\V1\Grade\Student;
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
}
