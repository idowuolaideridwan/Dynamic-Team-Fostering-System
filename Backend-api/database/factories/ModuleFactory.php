<?php
namespace Database\Factories;

use App\Models\API\V1\Grade\Module;
use Illuminate\Database\Eloquent\Factories\Factory;

class ModuleFactory extends Factory
{
    protected $model = Module::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word() . ' ' . $this->faker->randomElement(['Math', 'Science', 'History']),
            'code' => strtoupper($this->faker->unique()->lexify('???')) . $this->faker->numberBetween(100, 999), 
        ];
    }
}
