<?php

namespace App\Http\Controllers\API\V1\Grade;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Services\API\V1\Contracts\Grade\GradeServiceInterface;
use App\Http\Requests\API\V1\GradeRequest\GetStudentAveragesRequest;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Student Grading Calculation",
 *     description="API Endpoints for Student Grade"
 * )
 */
class GradeController extends Controller
{
    private GradeServiceInterface $gradeService;

    /**
     * Constructor with Dependency Injection.
     *
     * @param GradeServiceInterface $gradeService
     */
    public function __construct(GradeServiceInterface $gradeService)
    {
        $this->gradeService = $gradeService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/students",
     *     summary="Get all students with profile and grades",
     *     tags={"Student Grading Calculation"},
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="List of students"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function getStudentList()
    {
        try {
            $students = $this->gradeService->getStudentList();

            return response()->json(
                Helper::BuildJSONResponse(
                    Helper::MSG_SUCCESS,
                    "List of Students",
                    $students
                ),
                Helper::MSG_SUCCESS
            );
        } catch (\Throwable $e) {
            Log::error("Failed to fetch student list: " . $e->getMessage());

            return response()->json(
                Helper::BuildJSONResponse(
                    Helper::MSG_INTERNAL_SERVER_ERROR,
                    "Error retrieving student list",
                    null
                ),
                Helper::MSG_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
 * @OA\Get(
 *     path="/api/v1/students/grades",
 *     summary="Get average grades for all or selected students",
 *     description="Returns average grade and optional classification (Pass, Merit, Distinction) for all students or selected ones.",
 *     tags={"Student Grading Calculation"},
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Parameter(
 *         name="students[]",
 *         in="query",
 *         required=false,
 *         description="Array of student IDs to filter. Example: students[]=1&students[]=3",
 *         @OA\Schema(type="array", @OA\Items(type="string"))
 *     ),
 *     @OA\Parameter(
 *         name="summary_only",
 *         in="query",
 *         required=false,
 *         description="Set to true to exclude classification label from results",
 *         @OA\Schema(type="boolean", example=true)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Successful response with student averages",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="message", type="string", example="Student averages retrieved successfully"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="student_id", type="string", example="123456S"),
 *                     @OA\Property(property="name", type="string", example="Jane Doe"),
 *                     @OA\Property(property="average", type="number", format="float", example=68.3),
 *                     @OA\Property(property="classification", type="string", nullable=true, example="Merit")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="Failed to fetch student averages"),
 *             @OA\Property(property="data", type="string", nullable=true, example=null)
 *         )
 *     )
 * )
 */


    public function getStudentAverages(GetStudentAveragesRequest $request)
    {
        try {
            
            $students = $request->input('students', []);
            $summaryOnly = $request->boolean('summary_only', false);

            $result = $this->gradeService->getStudentAverages($students, $summaryOnly);

            return response()->json([
                'status' => 'success',
                'message' => 'Student averages retrieved successfully',
                'data' => $result
            ], Helper::MSG_SUCCESS);
        } catch (\Throwable $e) {
            Log::error('Grade average fetch error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch student averages',
                'data' => null
            ],  Helper::MSG_INTERNAL_SERVER_ERROR);
        }
    }

}
