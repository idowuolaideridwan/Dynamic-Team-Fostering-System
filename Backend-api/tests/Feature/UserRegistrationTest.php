<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\API\V1\User;
use App\Helpers\Helper;
use App\Services\API\V1\Contracts\UserServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Mockery;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr; // Add this line

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected $apiUrl;
    protected $userService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->apiUrl = Helper::getApiUrl() . '/register';
        $this->userService = $this->mock(UserServiceInterface::class);
    }

    /** @test */
    public function user_can_register_successfully()
    {
        \Log::info('Test started: user_can_register_successfully');

        $userData = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        \Log::info('Setting up mock');

        $this->userService->shouldReceive('register')
            ->once()
            ->with(Mockery::on(function ($arg) use ($userData) {
                unset($userData['password_confirmation']);
                \Log::info('Arguments passed to register (shouldReceive):', $arg);
                return $arg == $userData;
            }))
            ->andReturnUsing(function ($arg) {
                \Log::info("Creating user with data:", $arg);
                $user = User::create($arg);
                \Log::info("Created user:", optional($user)->toArray());
                return $user;
            });

        \Log::info('Making POST request to API');

        $response = $this->postJson($this->apiUrl, $userData);

        \Log::info('Response received', ['status' => $response->status(), 'content' => $response->getContent()]);

        if ($response->status() !== Helper::MSG_CREATED) {
            dd($response->getContent()); // Debug output
        }

        $response->assertStatus(Helper::MSG_CREATED);
        $response->assertJson(['message' => 'User information successfully registered']);
        $this->assertDatabaseHas('users', ['email' => 'johndoe@example.com']);

        \Log::info('Test completed: user_can_register_successfully');
    }

    /** @test */
    public function registration_fails_if_email_already_exists()
    {
        // Prepare user data with an email that already exists
        $userData = Helper::prepareUserData(['email' => 'johndoe@example.com']);

        // Mock the UserServiceInterface to throw a ValidationException for duplicate email
        $this->userService->shouldReceive('register')
            ->once()
            ->with(Mockery::on(function ($arg) use ($userData) {
                unset($arg['password_confirmation']); // Exclude password_confirmation
                \Log::info('Arguments passed to register (shouldReceive):', $arg);
                return $arg == Arr::except($userData, ['password_confirmation']);
            }))
            ->andThrow(ValidationException::withMessages(['email' => 'The email has already been taken.']));

        // Make a POST request to the registration endpoint with the user data
        $response = $this->postJson($this->apiUrl, $userData);

        // Assertions
        $response->assertStatus(Helper::MSG_UNPROCESSED_ENTITY);
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'data' => [
                'email' => ['The email has already been taken.']
            ]
        ]);

        $emailErrors = $response->json('data.email');
        $this->assertEquals('The email has already been taken.', $emailErrors[0]);
    }


    /** @test */
    public function registration_fails_if_name_is_missing()
    {
        $userData = Helper::prepareUserData(['name' => null]);

        $response = $this->postJson($this->apiUrl, $userData);

        $response->assertStatus(Helper::MSG_UNPROCESSED_ENTITY);
        $response->assertJsonValidationErrors('name');
    }

    /** @test */
    public function registration_fails_if_email_is_invalid()
    {
        $userData = Helper::prepareUserData(['email' => 'invalid-email']);

        $response = $this->postJson($this->apiUrl, $userData);

        $response->assertStatus(Helper::MSG_UNPROCESSED_ENTITY);
        $response->assertJsonValidationErrors('email');
    }

    /** @test */
    public function registration_fails_if_passwords_do_not_match()
    {
        $userData = Helper::prepareUserData(['password_confirmation' => 'differentPassword123']);

        $response = $this->postJson($this->apiUrl, $userData);

        $response->assertStatus(Helper::MSG_UNPROCESSED_ENTITY);
        $response->assertJsonValidationErrors('password');
    }

    /** @test */
    public function registration_fails_if_password_is_too_short()
    {
        $userData = Helper::prepareUserData(['password' => 'short', 'password_confirmation' => 'short']);

        $response = $this->postJson($this->apiUrl, $userData);

        $response->assertStatus(Helper::MSG_UNPROCESSED_ENTITY);
        $response->assertJsonValidationErrors('password');
    }
}
