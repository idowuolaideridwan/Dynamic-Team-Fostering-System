<?php

namespace App\Repositories\API\V1\Contracts\Grade;

interface GradeRepositoryInterface
{
    public function getPaginatedStudents($perPage);
}
