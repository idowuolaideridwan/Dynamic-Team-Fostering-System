<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\API\V1\StudentSeeder;
use Database\Seeders\API\V1\ModuleSeeder;
use Database\Seeders\API\V1\GradeSeeder;
use Database\Seeders\API\V1\StudentProfileSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            StudentSeeder::class,
            StudentProfileSeeder::class,
            ModuleSeeder::class,
            GradeSeeder::class,
        ]);
    }
}
