<?php

namespace App\Services\API\V1\Contracts\Industry;

interface IndustryServiceInterface
{
    public function getAllIndustries();
    public function getSubIndustriesByIds(array $industryIds);
    public function getRelevantIssuesBySubIds(array $subIds);
    public function deleteSubIndustry(array $subIds, $userId);
    public function deleteRelevantIssues(array $issueIds, $userId);
    public function saveRelevantIssues(array $issueData, int $userId);
}