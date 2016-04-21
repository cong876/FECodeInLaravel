<?php namespace App\Repositories\Requirement;

use App\Exceptions\GeneralException;
use App\Models\Requirement;
use App\Repositories\BaseRepository;

class RequirementRepository extends BaseRepository implements RequirementRepositoryInterface
{

    protected $model;

    public function __construct(Requirement $model)
    {
        $this->model = $model;
    }

    /*
     * 判断需求是否无效
     *
     * @param App\Models\Requirement $requirement
     * @return bool
     */
    public function is_available(Requirement $requirement)
    {
        return $requirement->is_available;
    }

    /*
     * 新建需求
     *
     * @param array $data
     * @param int $hlj_id
     * @return App\Models\Requirement|GeneralException
     */
    public function create(array $data, $hlj_id)
    {
        if (empty($hlj_id))
            return new GeneralException('requirement[hlj_id] must exit!');
        $data['hlj_id'] = $hlj_id;
        $micro = microtime(true);
        $split = explode('.', $micro);
        $requirement_number = 'YQ' . implode('', $split);
        $data['requirement_number'] = $requirement_number;
        $requirement = $this->model->create(array_except($data, 'detail'));
        $details = json_decode($data['detail'], true);
        foreach ($details as $detail) {
            $requirement->requirementDetails()->create(array_except($detail, 'order'));
        }
        return $requirement;
    }

    /*
     * 得到发布需求的用户
     *
     * @param App\Models\Requirement $requirement
     * @return App\Models\User|false
     */
    public function getRequirementUser(Requirement $requirement)
    {
        return $requirement->user();
    }

    /*
     *
     * 获得所有发布的需求(不限制ID)
     *
     */
    public function getAllWaitDispatchRequirementsWithPaginate($pageCount)
    {
        return $this->model->with('user')->waitDispatch()->orderBy('requirement_id', 'desc')->paginate($pageCount);
    }

    public function getAllWaitDispatchRequirementsWithPaginateByEmployeeId($pageCount, $oid)
    {
        return $this->model->with('user')->waitResponse()->where('operator_id', $oid)->orderBy('requirement_id', 'desc')->paginate($pageCount);
    }

    public function getAllWaitResponseRequirementsWithPaginate($pageCount)
    {
        return $this->model->with('user')->AllWaitResponse()->orderBy('requirement_id', 'desc')->paginate($pageCount);
    }

    /*
     *
     * 状态更改为待生成子订单
     *
     */
    public function updateStateToWaitSplit(Requirement $requirement)
    {
        $requirement->state = 201;
        return $requirement->save();
    }

    /*
     *
     * 需求状态变更为已关闭
     *
     */
    public function updateStateToCancelRequirement(Requirement $requirement)
    {
        $requirement->state = 431;
        return $requirement->save();
//        return $this->deleteRequirement($requirement);
    }

    /*
     * 删除需求
     *
     * @param App\Models\Requirement $requirement
     * @return bool
     */
//    public function deleteRequirement(Requirement $requirement){
//        $requirement->is_available = false;
//        return $requirement->save();
//    }

    /*
     * 需求变为订单
     *
     * @param App\Models\Requirement $requirement
     * @return bool|GeneralException
     *
     */
    public function requirementIsDone(Requirement $requirement)
    {
        if (!$this->is_available($requirement)) {
            return new GeneralException('The Requirement has been deleted!');
        }
        return $this->deleteRequirement($requirement);
    }

    /**
     *
     * 需求与商品绑定
     * @param Requirement $requirement
     * @param $item_id
     * @return bool
     */
    public function createRelation(Requirement $requirement, $item_id)
    {
        $requirement->items()->attach($item_id);
        return true;
    }

    /**
     *
     * 需求与商品解绑
     * @param Requirement $requirement
     * @param $item_id
     * @return int
     */
    public function deleteRelation(Requirement $requirement, $item_id)
    {
        return $requirement->items()->detach($item_id);
    }

    /**
     *
     * 获取所有待分配订单需求
     * @param $pageCount
     * @return mixed
     */
    public function getAllWaitSplitRequirementsWithPaginate($pageCount)
    {
        return $this->model->with('user')->waitSplit()->orderBy('requirement_id', 'desc')->paginate($pageCount);
    }

    /**
     *
     * 该运营处理的待分配订单需求
     * @param $pageCount
     * @param $oid
     * @return mixed
     */
    public function getAllWaitSplitRequirementsWithPaginateByEmployeeId($pageCount, $oid){
        return $this->model->with('user')->where('operator_id', $oid)->waitSplit()->orderBy('requirement_id', 'desc')->paginate($pageCount);
    }


    /**
     *
     * 获取所有已完成需求
     * @param $pageCount
     * @return mixed
     *
     */
    public function getAllFinishedRequirementsWithPaginate($pageCount)
    {
        return $this->model->with('user')->Finished()->orderBy('updated_at', 'desc')->paginate($pageCount);
    }

    /**
     * 获取所有已关闭需求
     *
     * @param $pageCount
     * @return mixed
     */
    public function getAllClosedRequirementsWithPaginate($pageCount)
    {
        return $this->model->with('user')->Closed()->orderBy('updated_at', 'desc')->paginate($pageCount);
    }

}