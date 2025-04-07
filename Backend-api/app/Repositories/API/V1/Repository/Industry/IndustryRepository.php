<?php

namespace App\Repositories\API\V1\Repository\Industry;

use App\Models\API\V1\Industry\Industry;
use App\Models\API\V1\Industry\SubIndustry;
use App\Models\API\V1\Industry\Issue;
use App\Models\API\V1\Industry\RelevantIssue;
use App\Models\API\V1\Industry\SavedRelevantIssue;
use App\Models\API\V1\Industry\SubIssue;
use App\Repositories\API\V1\Contracts\Industry\IndustryRepositoryInterface;

class IndustryRepository implements IndustryRepositoryInterface
{
    /**
     * Get all industries.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllIndustries()
    {
        return Industry::all();
    }

    /**
     * Get sub-industries by industry IDs.
     *
     * @param array $industryIds
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSubIndustriesByIds(array $industryIds)
    {
        return SubIndustry::whereIn('industryID', $industryIds)->get();
    }

    /**
     * Get relevant issues by sub-industry IDs.
     *
     * @param array $subIds
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRelevantIssuesBySubIds(array $subIds)
    {
        return RelevantIssue::whereIn('subID', $subIds)->get();
    }

    public function delete(array $subIds, $userId)
    {
        SavedRelevantIssue::whereIn('subID', $subIds)
                ->where('userID', $userId)
                ->delete();
        return true;
    }

    public function deleteRelevantIssues(array $issueIds, $userId) // to-do 
    {
        SavedRelevantIssue::whereIn('subissue_name', $issueIds) // refactor to use issueId
                ->where('userID', $userId)
                ->delete();
        return true;
    }

    public function saveRelevantIssues(array $issueData, int $userId)
{
    $batchInsertData = [];

    $subIndustryMapping = SubIndustry::pluck('subName', 'subID')->toArray();
    $IssueMapping = Issue::pluck('issue_name', 'id')->toArray();
    $subIssueMapping = SubIssue::pluck('subissue_name', 'id')->toArray();

    foreach ($issueData as $data) {
        try {
            
            $subIndustryName = $subIndustryMapping[$data['sub_id']] ?? 'Unknown';
            $issueName = $IssueMapping[$data['issue_name']] ?? 'Unknown';
            $subIssueName = $subIssueMapping[$data['subissue_id']] ?? 'Unknown';

            $batchInsertData[] = [
                'userID' => $userId,
                'subID' => $data['sub_id'] ?? 0,                
                'subissue_id' => $data['subissue_id'] ?? 'N/A', 
                'subName' => $subIndustryName, //correct
                'issue_name' => $issueName, // issue_name   
                'subissue_name' => $subIssueName, // subissue_id
                'environmental_score' => $data['environmental_score'] ?? 0, 
                'business_score' => $data['business_score'] ?? 0,
                'ranking_score' => (($data['environmental_score'] ?? 0) + ($data['business_score'] ?? 0)/2), 
                'impacts' => $data['impacts'] ?? 'No impact provided', 
                'risks' => $data['risks'] ?? 'No risks provided', 
                'opportunities' => $data['opportunities'] ?? 'No opportunities provided',
            ];
        } catch (\Exception $e) {
            // Log the error and continue with the next record
            \Log::error('Error preparing sub-issue for insertion:', [
                'user_id' => $userId,
                'data' => $data,
                'error' => $e->getMessage(),
            ]);
        }
    }

    try {
        SavedRelevantIssue::insert($batchInsertData);
    } catch (\Exception $e) {
        // Log any errors during the bulk insert
        \Log::error('Error performing bulk insert of sub-issues:', [
            'user_id' => $userId,
            'error' => $e->getMessage(),
        ]);
    }
}

}
