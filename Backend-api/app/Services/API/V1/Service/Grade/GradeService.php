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

    public function calculateAverageAndClassification(array $grades): array // Used to mock the test
    {
        $avg = round(array_sum($grades) / count($grades), 1);

        $classification = match (true) {
            $avg >= 70 => 'Distinction',
            $avg >= 60 => 'Merit',
            $avg >= 40 => 'Pass',
            default    => 'Fail',
        };

        return [
            'average' => $avg,
            'classification' => $classification,
        ];
    }


}
