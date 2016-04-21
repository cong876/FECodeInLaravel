<?php namespace App\Repositories\Requirement;

use App\Models\Requirement;

interface RequirementRepositoryInterface {

    public function is_available(Requirement $requirement);

    public function create(array $data, $hlj_id);

    public function getRequirementUser(Requirement $requirement);

    public function requirementIsDone(Requirement $requirement);

    public function getAllWaitDispatchRequirementsWithPaginate($pageCount);

    public function getAllWaitDispatchRequirementsWithPaginateByEmployeeId($pageCount, $oid);

    public function getAllWaitResponseRequirementsWithPaginate($pageCount);

    public function getAllWaitSplitRequirementsWithPaginate($pageCount);

    public function getAllWaitSplitRequirementsWithPaginateByEmployeeId($pageCount, $oid);

    public function getAllFinishedRequirementsWithPaginate($pageCount);

    public function getAllClosedRequirementsWithPaginate($pageCount);

    public function updateStateToWaitSplit(Requirement $requirement);

    public function updateStateToCancelRequirement(Requirement $requirement);

    public function createRelation(Requirement $requirement,$item_id);

    public function deleteRelation(Requirement $requirement,$item_id);
}