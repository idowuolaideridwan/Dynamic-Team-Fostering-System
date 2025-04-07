<?php

namespace App\Services\API\V1\Service\Industry;

use App\Repositories\API\V1\Contracts\Industry\IndustryRepositoryInterface;
use App\Services\API\V1\Contracts\Industry\IndustryServiceInterface;

class IndustryService implements IndustryServiceInterface
{
    private $industryRepository;

    /**
     * Constructor with Dependency Injection.
     *
     * @param IndustryRepositoryInterface $industryRepository
     */
    public function __construct(IndustryRepositoryInterface $industryRepository)
    {
        $this->industryRepository = $industryRepository;
    }

    /**
     * Get all industries.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllIndustries()
    {
        return $this->industryRepository->getAllIndustries();
    }

    /**
     * Get sub-industries by industry IDs.
     *
     * @param array $industryIds
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSubIndustriesByIds(array $industryIds)
    {
        return $this->industryRepository->getSubIndustriesByIds($industryIds);
    }

    /**
     * Get relevant issues by sub-industry IDs.
     *
     * @param array $subIds
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRelevantIssuesBySubIds(array $subIds)
    {
        return $this->industryRepository->getRelevantIssuesBySubIds($subIds);
    }

    public function deleteSubIndustry(array $subIds, $userId)
    {
        return $this->industryRepository->delete($subIds, $userId);
    }

    public function deleteRelevantIssues(array $issueIds, $userId)
    {
        return $this->industryRepository->deleteRelevantIssues($issueIds, $userId);
    }

    public function saveRelevantIssues(array $issueData, int $userId)
    {
        $this->industryRepository->saveRelevantIssues($issueData, $userId);
    }
}
