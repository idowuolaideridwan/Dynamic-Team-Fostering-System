<?php

namespace App\Http\Controllers\API\V1\Grade;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Services\API\V1\Contracts\Grade\GradeServiceInterface;

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
class GradeServiceInterface extends Controller
{
    private $gradeService;

    /**
     * Constructor with Dependency Injection.
     *
     * @param GradeServiceInterface $GradeService
     */
    public function __construct(GradeServiceInterface $gradeService)
    {
        $this->gradeService = $gradeService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/students",
     *     summary="Get all students",
     *     description="Retrieve a list of all students",
     *     tags={"Student Grading Calculation"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *     ),
     * )
     */
    public function getStudentList()
    {
        $students = $this->gradeService->getStudentList();

        return response()->json(
            Helper::BuildJSONResponse(Helper::MSG_SUCCESS, "List of Students", $students),
            Helper::MSG_SUCCESS
        );
    }


}

?>