<?php

namespace Tests\Feature\Industry;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\API\V1\User;
use App\Models\API\V1\Industry\Industry; // Assuming you have Industry model
use App\Models\API\V1\Industry\SubIndustry; // Assuming you have SubIndustry model
use App\Models\API\V1\Industry\RelevantIssue; // Assuming you have RelevantIssue model
use App\Helpers\Helper;
use Illuminate\Foundation\Testing\WithFaker;

class IndustryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $apiUrl;

    protected function setUp(): void
    {
        parent::setUp();
        $this->apiUrl = Helper::getApiUrl();
    }

    protected function authenticateUser()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password')
        ]);

        $response = $this->postJson($this->apiUrl.'/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        return $response->json('data.user.token');
    }

    public function test_get_all_industries()
    {
        $token = $this->authenticateUser();

        // Assuming you have an Industry model and a factory for it
        Industry::factory()->create(); 

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson($this->apiUrl . '/get_industries');

        $response->assertStatus(Helper::MSG_SUCCESS);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    // Add other expected fields
                ],
            ],
        ]);
    }

    public function test_get_sub_industries_by_ids()
    {
        $token = $this->authenticateUser();

        $industry = Industry::factory()->create(); 
        $subIndustry = SubIndustry::factory()->create([
            'industry_id' => $industry->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson($this->apiUrl . '/get_sub_industries', [
            'industry_ids' => [$industry->id],
        ]);

        $response->assertStatus(Helper::MSG_SUCCESS);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                '*' => [
                    'subID',
                    'subName',
                    // Add other expected fields
                ],
            ],
        ]);
    }

    public function test_get_relevant_issues_by_sub_ids()
    {
        $token = $this->authenticateUser();

        // Assuming SubIndustry has a relationship with RelevantIssue
        $subIndustry = SubIndustry::factory()->create();
        $relevantIssue = RelevantIssue::factory()->create([
            'subID' => $subIndustry->subID,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson($this->apiUrl . '/get_relevant_issues', [
            'sub_ids' => [$subIndustry->subID],
        ]);

        $response->assertStatus(Helper::MSG_SUCCESS);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                '*' => [
                    'subID',
                    'subName',
                    'issues' => [
                        '*' => [
                            'name',
                            'relevant_issues' => [
                                '*' => [
                                    'subissue_name',
                                ]
                            ]
                        ]
                    ]
                ],
            ],
        ]);
    }

    public function test_get_relevant_issues_empty_response()
    {
        $token = $this->authenticateUser();

        // Test with invalid subIDs (non-existent)
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson($this->apiUrl . '/get_relevant_issues', [
            'sub_ids' => [9999], // Non-existent subID
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'status' => 'error',
            'message' => 'No relevant issues found'
        ]);
    }

    public function test_get_sub_industries_invalid_ids()
    {
        $token = $this->authenticateUser();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson($this->apiUrl . '/get_sub_industries', [
            'industry_ids' => [999], // Invalid industry ID
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'status' => 'error',
            'message' => 'No sub-industries found for the given industry IDs'
        ]);
    }
}
