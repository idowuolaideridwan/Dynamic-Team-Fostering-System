<?php

namespace App\Services\API\V1\Service\Grade;

use App\Repositories\API\V1\Contracts\Grade\GradeRepositoryInterface;
use App\Services\API\V1\Contracts\Grade\GradeServiceInterface;

class GradeService implements GradeServiceInterface
{
    protected GradeRepositoryInterface $gradeRepo;

    public function __construct(GradeRepositoryInterface $gradeRepo)
    {
        $this->gradeRepo = $gradeRepo;
    }

    public function getStudentList()
    {
        return $this->gradeRepo->getPaginatedStudents(15);
    }

    public function getStudentAverages(array $studentIds, bool $summaryOnly)
{
    return $this->gradeRepo->getStudentAverages($studentIds, $summaryOnly);
}

}
