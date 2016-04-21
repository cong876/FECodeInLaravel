<?php


namespace App\Repositories\Activity;

use App\Repositories\BaseRepository;
use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ActivityRepository extends BaseRepository implements ActivityRepositoryInterface {

    protected $model;

    public function __construct(Activity $model)
    {
        $this->model = $model;
    }

    public function createActivity(array $data)
    {
        $activity = $this->model->create($data);
        return $activity;
    }

    public function updateActivity(Activity $activity, array $data)
    {
        return $activity->update($data);
    }

    public function deleteActivity(Activity $activity)
    {
        return $activity->delete();
    }

    public function getAllSubjectActivitiesWithPaginate($paginate)
    {
        return $this->model->AllSubjectActivities()->orderBy('activity_start_time','desc')->orderBy('activity_due_time','desc')->paginate($paginate);
    }

    public function getAllPeriodActivitiesWithPaginate($paginate)
    {
        return $this->model->AllPeriodActivities()->orderBy('activity_start_time','desc')->orderBy('activity_due_time','desc')->paginate($paginate);
    }

    public function getCurrentPeriodActivity() {
        $now = Carbon::now();
        $activity = $this->model->where('activity_type', 1)->whereBetween('activity_due_time', [$now->toDateTimeString(), $now->addDay()->toDateTimeString()])->first();
        return $activity;
    }

    public function getTomorrowPeriodActivity()
    {
        $now = Carbon::now();
        $activity = $this->model->where('activity_type', 1)->whereBetween('activity_due_time', [$now->addHours(24)->toDateTimeString(), $now->addHours(48)->toDateTimeString()])->first();
        return $activity;
    }
}