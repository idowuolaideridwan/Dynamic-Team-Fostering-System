<?php

namespace Tests\Unit\Services\API\V1;

use Tests\TestCase;
use App\Services\API\V1\Service\Grade\GradeService;
use App\Repositories\API\V1\Contracts\Grade\GradeRepositoryInterface;
use Mockery;

class GradeServiceTest extends TestCase
{
    public function test_it_returns_average_and_classification()
    {
        $mockRepo = Mockery::mock(GradeRepositoryInterface::class);

        $service = new GradeService($mockRepo);

        $grades = [60, 70, 50];
        $result = $service->calculateAverageAndClassification($grades);

        $this->assertEquals(60.0, $result['average']);
        $this->assertEquals('Merit', $result['classification']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
