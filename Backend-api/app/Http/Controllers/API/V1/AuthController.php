<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller; 
use App\Http\Requests\API\V1\AuthRequest\{RegisterRequest,LoginRequest,ResetPasswordRequest,ForgotPasswordRequest};
use App\Services\API\V1\Contracts\UserServiceInterface;
use App\Models\API\V1\User;
use App\Helpers\Helper;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="API Endpoints for user authentication"
 * )
 */
class AuthController extends Controller
{
    protected $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/register", 
     *     tags={"Authentication"},
     *     summary="Register new user",
     *     description="Register a new user with name, email and password",
     *     operationId="register",
     *     @OA\RequestBody(
     *         required=true,
     *         description="User registration details",
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User successfully registered",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="User successfully registered"),
     *             @OA\Property(
     *                 property="info",
     *                 type="object",
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="john@example.com"),
     *                     @OA\Property(property="created_at", type="string", format="date-time")
     *                 ),
     *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="info",
     *                 type="object",
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="The email has already been taken."))
     *             )
     *         )
     *     )
     * )
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try 
        {
            $user = $this->userService->register($request->validated());

            $token = JWTAuth::fromUser($user);

            $status = Helper::MSG_CREATED;
            $message = "User information successfully registered";
            $info = [
                'user' => $user,
                'token' => $token,
            ];
            $statusCode = Helper::MSG_CREATED;
        } 
        catch (ValidationException $e) 
        {
            $status = Helper::MSG_UNPROCESSED_ENTITY;
            $message = "The given data was invalid.";
            $info = $e->errors();
            $statusCode = Helper::MSG_UNPROCESSED_ENTITY;
        } 
        catch (Exception $e) 
        {
            $status = Helper::MSG_UNPROCESSED_ENTITY;
            $message = "An error occurred during registration";
            $info = $e->getMessage();
            $statusCode = Helper::MSG_UNPROCESSED_ENTITY;
        }

        return response()->json(
            Helper::BuildJSONResponse($status, $message, $info),
            $statusCode
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     tags={"Authentication"},
     *     summary="Login user",
     *     description="Authenticate user with email and password",
     *     operationId="login",
     *     @OA\RequestBody(
     *         required=true,
     *         description="User credentials",
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="idowuolaideridwan@gmail.com"),
     *             @OA\Property(property="password", type="string", format="password", example="idowuolaideridwan@gmail.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="User successfully authenticated"),
     *             @OA\Property(
     *                 property="info",
     *                 type="object",
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="name", type="string", example="Ridwan Idowu"),
     *                     @OA\Property(property="email", type="string", example="idowuolaideridwan@gmail.com"),
     *                     @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Invalid credentials"),
     *             @OA\Property(property="info", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="The given data was invalid"),
     *             @OA\Property(
     *                 property="info",
     *                 type="object",
     *                 @OA\Property(
     *                     property="email",
     *                     type="array",
     *                     @OA\Items(type="string", example="The email field is required")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $token = $this->userService->authenticate($request->only('email', 'password'));
            $user = auth()->user();

            $info = [
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'token' => $token
                ]
            ];

            $statusCode = Helper::MSG_SUCCESS;
            $response = Helper::BuildJSONResponse('success', 'User was successfully authenticated', $info);
            
        } 
        catch (\Exception $e) 
{
    // Get the exception message
    $errorMessage = $e->getMessage();
    
    // Ensure the HTTP status code is valid (100-599), otherwise default to 500
    $statusCode = ($e->getCode() >= 100 && $e->getCode() < 600) 
        ? $e->getCode() 
        : 500;

    // Build the JSON response
    $response = Helper::BuildJSONResponse('error', $errorMessage, null);
}


            return response()->json($response, $statusCode);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/forgot-password", 
     *     tags={"Authentication"},
     *     summary="Forgot password",
     *     description="Send password reset link to user's email",
     *     operationId="forgotPassword",
     *     @OA\RequestBody(
     *         required=true,
     *         description="User email",
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset link sent",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Password reset link sent to your email.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="User not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Internal server error.")
     *         )
     *     )
     * )
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $user = User::where('email', $validatedData['email'])->firstOrFail();
            $token = Str::random(60);
            DB::table('password_resets')->upsert([
                'email' => $user->email,
                'token' => $token,
                'created_at' => Carbon::now()
            ], ['email'], ['token', 'created_at']);

            //Mail::to($user->email)->send(new SendPasswordResetNotification($token));

            return response()->json(['status' => 'success', 'message' => 'Password reset link sent to your email.', 'code' => $token], 200);
        } catch (ValidationException $e) {
            return response()->json(['status' => 'error', 'message' => 'Validation error', 'info' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'User not found.'], 404);
        } catch (\Exception $e) {
            Log::error('Password reset error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Internal server error.'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/reset-password",
     *     tags={"Authentication"},
     *     summary="Reset user password",
     *     description="Reset the password for a user",
     *     operationId="resetPassword",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Password reset details",
     *         @OA\JsonContent(
     *             required={"token", "email", "password", "password_confirmation"},
     *             @OA\Property(property="token", type="string", example="reset_token"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="new_password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="new_password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Password reset successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="User not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Internal server error.")
     *         )
     *     )
     * )
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $user = User::where('email', $validatedData['email'])->firstOrFail();
            $token = $validatedData['token'];
            
            //Log::error($token);

            // Verify the token
            $tokenExists = DB::table('password_resets')
                ->where([
                    ['email', $user->email],
                    ['token', $token],
                    ['created_at', '>', now()->subHours(2)], // Token can be used within 2 hours
                ])
                ->first();

            if (!$tokenExists) {
                return response()->json(['status' => 'error', 'message' => 'Invalid or expired token.'], 404);
            }

            // Reset the password
            $user->password = bcrypt($validatedData['password']);
            $user->save();

            // Remove password reset token
            DB::table('password_resets')->where('email', $user->email)->delete();

            return response()->json(['status' => 'success', 'message' => 'Password reset successfully.'], 200);
        } catch (ValidationException $e) {
            return response()->json(['status' => 'error', 'message' => 'Validation error', 'info' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'User not found.'], 404);
        } catch (\Exception $e) {
            Log::error('Password reset error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Internal server error.'], 500);
        }
    }

    public function getAuthenticatedUser(): JsonResponse
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }

        return response()->json(compact('user'));
    }
}
