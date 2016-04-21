<?php

namespace App\Http\Controllers\Operator;

use App\Http\ApiControllers\Controller;
use Cache;
use Illuminate\Http\Request;
use App\Utils\Json\ResponseTrait;
use App\Models\Seckill;
use App\Repositories\SecKill\SecKillRepositoryInterface;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;

class OperatorSecKillController extends Controller
{
    private $secKill;

    use ResponseTrait;

    function __construct(SecKillRepositoryInterface $secKill)
    {
        $this->middleware('operator');
        $this->secKill = $secKill;
    }

    /**
     * 新建秒杀活动
     *
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $secKillData = $request->get('secKillData');
        $itemData = $request->get('itemData');
        $activity_id = $request->get('activity_id');
        $ids = $this->secKill->createSecKillForActivityWithItem($secKillData, $itemData,
            Auth::user()->employee->employee_id, $activity_id);

        return response()->json([
            'item_id' => $ids['item_id'],
            'secKill_id' => $ids['secKill_id']
        ]);
    }

    /**
     * 使秒杀生效,秒杀默认无效,不在前台展示
     *
     * @param $secKill_id
     * @return mixed
     */
    public function valid($secKill_id)
    {
        $secKill = Seckill::find($secKill_id);
        $ret = $this->responseWithCondition($this->secKill->setStatusToAvailable($secKill),
            "恢复商品失败,请重试");
        $this->clearRelatedCache($secKill_id);
        return $this->response->array($ret);
    }

    /**
     * 使秒杀失效,失效后将不再前台展示
     *
     * @param $secKill_id
     * @return mixed
     */
    public function invalid($secKill_id)
    {
        $secKill = Seckill::find($secKill_id);
        $ret = $this->responseWithCondition($this->secKill->setStatusToUnavailable($secKill),
            "失效商品失败,请重试");
        $this->clearRelatedCache($secKill_id);
        return $this->response->array($ret);
    }

    /**
     * 更新秒杀活动
     *
     * @param Request $request
     * @param $secKill_id
     * @return mixed
     */
    public function update(Request $request, $secKill_id)
    {
        $secKillData = $request->get('secKillData');
        $itemData = $request->get('itemData');
        $activity_id = $request->get('activity_id');

        $updated = $this->secKill->updateSecKillForActivityWithItem($secKill_id, $secKillData,
            $itemData, Auth::user()->employee->employee_id, $activity_id);

        $ret = $this->responseWithCondition($updated,
            "更新商品失败,请重试");
        $this->clearRelatedCache($secKill_id);
        return $this->response->array($ret);
    }

    /**
     * 将秒杀商品上架
     *
     * @param $secKill_id
     * @return mixed
     */
    public function putOnShelf($secKill_id)
    {
        $ret = $this->responseWithCondition($this->secKill->putOnShelf(Seckill::find($secKill_id)),
            "上架商品失败,请重试");
        $this->clearRelatedCache($secKill_id);
        return $this->response->array($ret);
    }

    /**
     * 将秒杀商品下架
     *
     * @param $secKill_id
     * @return mixed
     */
    public function putOffShelf($secKill_id)
    {
        $ret = $this->responseWithCondition($this->secKill->putOffShelf(Seckill::find($secKill_id)),
            "下架商品失败,请重试");
        $this->clearRelatedCache($secKill_id);
        return $this->response->array($ret);
    }

    /**
     * 删除秒杀活动
     *
     * @param $secKill_id
     * @return mixed
     */
    public function delete($secKill_id)
    {
        $ret = $this->responseWithCondition($this->secKill->deleteSecKill(Seckill::find($secKill_id)),
            "删除商品失败,请重试");
        $this->clearRelatedCache($secKill_id);
        return $this->response->array($ret);
    }

    /**
     * 在秒杀活动发生改变时,清楚相关缓存
     *
     * @param $secKill_id
     */
    private function clearRelatedCache($secKill_id)
    {
        $secKill = Seckill::find($secKill_id);

        // 列举需要清楚的缓存Key:指定id的秒杀活动缓存,该秒杀活动是否被缓存的布尔值缓存,所有团购活动下的缓存
        $cache_key = 'SecKill:' . $secKill_id . ':Cache';
        $cached_key = 'SecKill:' . $secKill_id . ':HasCached';

        if ($secKill->activity_id) {
            $cache_all_key = 'Activity:' . $secKill->activity_id . ':SecKillCache';
            Cache::forget($cache_all_key);
        }

        // 清楚相关缓存和全局缓存
        Cache::forget($cache_key);
        Cache::forget($cached_key);

    }

    /**
     * 按照条件生成相应
     *
     * @param $condition
     * @param $errorMessage
     * @return array
     */
    private function responseWithCondition($condition, $errorMessage)
    {
        $ret = $condition ? $this->requestSucceed() : $this->requestFailed(400, $errorMessage);
        return $ret;
    }
}
