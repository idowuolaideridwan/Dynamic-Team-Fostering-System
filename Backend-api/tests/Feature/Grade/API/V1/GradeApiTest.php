<?php
namespace Tests\Feature\Grade\API\V1;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\API\V1\User;
use App\Models\API\V1\Grade\Student;
use App\Models\API\V1\Grade\Grade;
use App\Models\API\V1\Grade\Module;

class GradeApiTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        $user = User::factory()->create();
        return auth()->login($user);
    }

    public function test_can_list_students()
    {
        $token = $this->authenticate();

        Student::factory()->count(5)->hasGrades(3)->hasProfile()->create();

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->getJson('/api/v1/students');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'data' => ['data']
                 ]);
    }

    public function test_can_return_student_grade_summary()
    {
        $token = $this->authenticate();

        Student::factory()->hasGrades(3)->create();

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->getJson('/api/v1/students/grades?summary_only=true');

        $response->assertStatus(200)
                 ->assertSee('student_id');
    }

    public function test_can_filter_grade_summary_by_student_ids()
    {
        $token = $this->authenticate();

        $students = Student::factory()->count(2)->hasGrades(3)->create();

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->getJson('/api/v1/students/grades?summary_only=true&students[]=' . $students[0]->student_id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['student_id' => $students[0]->student_id]);
    }

    public function test_requires_authentication()
    {
        $response = $this->getJson('/api/v1/students');
        $response->assertStatus(404);
    }
}
