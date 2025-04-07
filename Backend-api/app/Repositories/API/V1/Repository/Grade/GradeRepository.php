<?php

namespace App\Repositories\API\V1\Repository\Grade;

use App\Models\API\V1\Grade\Student;
use Illuminate\Support\Facades\Cache;
use App\Repositories\API\V1\Contracts\Grade\GradeRepositoryInterface;

class GradeRepository implements GradeRepositoryInterface
{
    public function getPaginatedStudents($perPage = 15)
    {
        return Cache::remember("students_paginated_$perPage", 300, function () use ($perPage) {
            return Student::with([
                    'profile:id,student_id,email,phone,gender',
                    'grades.module:id,name'
                ])
                ->select('id', 'student_id', 'first_name', 'last_name', 'dob')
                ->paginate($perPage);
        });
    }

    public function getStudentAverages(array $studentIds = [], bool $summaryOnly = false)
    {
        $students = Student::with('grades.module')
            ->when(!empty($studentIds), fn($q) => $q->whereIn('student_id', $studentIds))
            ->select('id', 'student_id', 'first_name', 'last_name')
            ->get();

        return $students->map(function ($student) use ($summaryOnly) {
            $grades = $student->grades->pluck('grade');
            $average = round($grades->avg(), 2);

            return [
                'student_id' => $student->student_id,
                'name' => $student->first_name . ' ' . $student->last_name,
                'average' => $average,
                'classification' => $summaryOnly ? null : $this->classify($average),
            ];
        });
    }

    private function classify($avg)
    {
        return match (true) {
            $avg >= 70 => 'Distinction',
            $avg >= 60 => 'Merit',
            $avg >= 40 => 'Pass',
            default    => 'Fail',
        };
    }

}
