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

class UserLoginTest extends TestCase
{
    protected $apiUrl;

    protected function setUp(): void
    {
        parent::setUp();
        $this->apiUrl = Helper::getApiUrl() . '/login';
    }
            /** @test */
        public function user_can_login_with_correct_credentials()
        {
            // Create a user
            $user = User::factory()->create([
                'password' => bcrypt($password = 'idowuolaideridwan'),  // Ensure the password is hashed
            ]);

            // Attempt to login
            $response = $this->postJson($this->apiUrl, [
                'email' => $user->email,
                'password' => $password,
            ]);

            // Assert it was successful and a token was received
            $response->assertStatus(200);
            $response->assertJsonStructure([
                'data' => [
                    'user' => [
                        'token'
                    ]
                ]
            ]);
        }


        /** @test */
        public function user_cannot_login_with_incorrect_credentials()
        {
            // Create a user
            $user = User::factory()->create([
                'password' => bcrypt('correct-password')
            ]);

            // Attempt to login with incorrect password
            $response = $this->postJson($this->apiUrl, [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);

            // Assert it was unsuccessful
            $response->assertStatus(401);
            $response->assertJson([
                'message' => 'Invalid credentials'
            ]);
        }

}
