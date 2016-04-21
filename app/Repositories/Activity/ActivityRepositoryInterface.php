<?php

namespace App\Repositories\Activity;

use App\Models\Activity;

interface ActivityRepositoryInterface {

    public function createActivity(array $data);

    public function updateActivity(Activity $activity, array $data);

    public function deleteActivity(Activity $activity);

    public function getAllSubjectActivitiesWithPaginate($paginate);

    public function getAllPeriodActivitiesWithPaginate($paginate);

    public function getCurrentPeriodActivity();

    public function getTomorrowPeriodActivity();

}