<?php

namespace App\Repositories\API\V1\Contracts\Industry;

interface IndustryRepositoryInterface
{
    public function getAllIndustries();
    public function getSubIndustriesByIds(array $industryIds);
    public function getRelevantIssuesBySubIds(array $subIds);
    public function delete(array $subIds, int $userId);
    public function deleteRelevantIssues(array $issueIds, int $userId);
    public function saveRelevantIssues(array $issueData, int $userId);
}
