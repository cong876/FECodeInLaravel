<?php


namespace App\Http\ApiControllers\Activity;

use App\Utils\Json\ResponseTrait;
use App\Http\ApiControllers\Controller;
use App\Transforms\WxGroupPeriodDataTransformer;
use App\Transforms\WxGroupActivityTransformer;
use App\Models\Activity;
use Illuminate\Support\Facades\Cache;

class ActivityController extends Controller
{
    use ResponseTrait;

    public function current()
    {
        $current = Activity::currentPeriod()->first();
        if ($current) {
            if($ret = Cache::get('PeriodActivity:' . $current->activity_id)) {
                $ret['serverTime'] = date('Y-m-d H:i:s');
                return $ret;
            }else {
                return $this->response->item($current, new WxGroupPeriodDataTransformer);
            }
        } else {
            // 开天窗提醒
            $ret = $this->requestFailed(400, "很抱歉,今日没有团购活动");
            return $this->response->array($ret);
        }
    }

    public function show($activity_id)
    {
        $activity = Activity::find($activity_id);
        if ($activity) {
            if($ret = Cache::get('ActivityInfo:' . $activity->activity_id)) {
                $ret['serverTime'] = date('Y-m-d H:i:s');
                return $ret;
            }else {
                return $this->response->item($activity, new WxGroupActivityTransformer);
            }
        } else {
            // 开天窗提醒
            $ret = $this->requestFailed(400, "该活动不存在");
            return $this->response->array($ret);
        }

    }
}