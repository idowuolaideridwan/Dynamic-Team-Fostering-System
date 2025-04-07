<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\API\V1\Contact;
use App\Models\API\V1\User;
use App\Helpers\Helper;
use App\Services\API\V1\Contracts\ContactServiceInterface;
use App\Services\API\V1\Contracts\UserServiceInterface;
use Illuminate\Foundation\Testing\WithFaker;

class ContactTest extends TestCase
{
    protected $apiUrl;

    protected function setUp(): void
    {
        parent::setUp();
        $this->apiUrl = Helper::getApiUrl();
    }

    use RefreshDatabase, WithFaker;

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

    public function test_contact_can_be_retrieved()
    {
        $token = $this->authenticateUser();
    
        $contact = Contact::factory()->create();
    
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson($this->apiUrl.'/contacts/' . $contact->id);
    
        $response->assertStatus(Helper::MSG_SUCCESS);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'contact_id',
                // Add other keys as expected in your API response
            ]
        ]);
    
        $response->assertJson([
            'data' => [
                'id' => $contact->id,
                'contact_id' => $contact->contact_id,
                // Validate other fields if necessary
            ]
        ]);
    }
    
    public function test_contact_can_be_created()
    {
        $token = $this->authenticateUser();
    
        $contactData = Contact::factory()->make()->toArray();
    
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson($this->apiUrl.'/contacts', $contactData);
    
        $response->assertStatus(Helper::MSG_CREATED);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'contact_id'
                // Add other expected fields
            ]
        ]);
    
        $response->assertJson([
            'data' => $contactData
        ]);
    }
    

    public function test_contact_can_be_updated()
{
    $token = $this->authenticateUser();

    $contact = Contact::factory()->create();
    $updatedData = [
        "contact_fullname" => "IDOWU OLAIDE RIDWAN",
        "contact_companyname" => "CROSSERA HOUSING",
        "contact_email" => "idowuolaideridwan@gmail.com",
        "contact_mobile" => "1234567893",
        "contact_landline" => "1234567893",
        "contact_address" => "FLAT 1, HERON STREETs",
        "contact_postcode" => "ST4 3ARs",
        "contact_status" => "1"
    ];
    

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->putJson($this->apiUrl.'/contacts/' . $contact->id, $updatedData);

    $response->assertStatus(Helper::MSG_SUCCESS);
    $this->assertDatabaseHas('contacts', [
        'id' => $contact->id,
        'contact_fullname' => 'IDOWU OLAIDE RIDWAN'
    ]);
}

public function test_contact_can_be_deleted()
{
    $token = $this->authenticateUser();

    $contact = Contact::factory()->create();

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->deleteJson($this->apiUrl.'/contacts/' . $contact->id);

    $response->assertStatus(Helper::MSG_SUCCESS);
    $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
}


}
