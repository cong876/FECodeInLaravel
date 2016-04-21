<?php


namespace App\Http\ApiControllers\UserRequirement;

use App\Events\RequirementNotification;
use App\Events\SavePictureToQN;
use App\Helper\WXAccessToken;
use App\Helper\WXNotice;
use App\Models\Requirement;
use App\Models\User;
use App\Repositories\Item\ItemRepositoryInterface;
use App\Repositories\MainOrder\MainOrderRepositoryInterface;
use App\Repositories\Requirement\RequirementRepositoryInterface;
use App\Repositories\SubOrder\SubOrderRepositoryInterface;
use App\Transforms\MyOrderWxRequirementTransformer;
use App\Utils\Json\ResponseTrait;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use App\Http\ApiControllers\Controller;
use Illuminate\Support\Facades\Cache;
use Event;

class UserRequirementController extends Controller
{
    use ResponseTrait;

    private $mainOrder, $suborder, $requirement, $item;

    function __construct(MainOrderRepositoryInterface $mainOrder,
                         SubOrderRepositoryInterface $subOrder,
                         RequirementRepositoryInterface $requirement,
                         ItemRepositoryInterface $item)
    {

        $this->mainOrder = $mainOrder;
        $this->suborder = $subOrder;
        $this->requirement = $requirement;
        $this->item = $item;
    }

    // 用户所有需求
    public function index(Request $request, $userId, $state = 'waitOffer')
    {
        $query = DB::table('requirements')->where('hlj_id', $userId);
        if ($request->input('state')) {
            $state = $request->input('state');
        }
        switch ($state) {
            case 'waitOffer':
                $query->whereIn('state', [101, 201]);
                break;
            case 'finished':
                $query->where('state', 301);
                break;
            case 'closed':
                $query->whereIn('state', [411, 431]);
                break;
            case 'closedByUser':
                $query->where('state', 411);
                break;
            case 'closedByOperator':
                $query->where('state', 431);
                break;
            case 'waitResponse':
                $query->where('state', 101);
                break;
            case 'waitSplit':
                $query->where('state', 201);
                break;
        }
        $requirements = $query->orderBy('updated_at', 'desc')->get();
        // 处理数据
        foreach ($requirements as $requirement) {
            $requirementDetails = DB::table('requirement_details')
                ->where('requirement_id', $requirement->requirement_id)
                ->get();
            $requirement->details = [];
            foreach ($requirementDetails as $detail) {
                $requirement->number = $requirement->number ?? 0 + $detail->number;
                $requirement->title = $requirement->title ?? '' . $detail->title . ';';
                $decodedPicUrls = json_decode($detail->pic_urls);
                $requirement->pic_urls = array_merge($requirement->pic_urls ?? [], $decodedPicUrls);
                array_push($requirement->details,
                    [
                        'title' => $detail->title,
                        'pic_urls' => $decodedPicUrls,
                        'number' => $detail->number,
                    ]
                );
            }
            if ($operator_id = $requirement->operator_id) {
                $cacheKey = 'Operator:' . $operator_id . ':Mobile';
                $operatorMobile = Cache::get($cacheKey, function () use ($cacheKey, $operator_id) {
                    $expiresAt = Carbon::now()->addHours(24);
                    $operator = DB::table('employees')
                        ->where('employee_id', $operator_id)
                        ->leftJoin('users', 'employees.hlj_id', '=', 'users.hlj_id')
                        ->first();
                    $mobile = $operator->mobile;
                    Cache::put($cacheKey, $mobile, $expiresAt);
                    return $mobile;
                });
                $requirement->operatorMobile = $operatorMobile;
            } else {
                $requirement->operatorMobile = '18701133614';
            }
        }
        return $this->response->collection(collect($requirements), new MyOrderWxRequirementTransformer);
    }

    // 用户取消需求
    public function delete($userId, $reqId)
    {
        $requirement = Requirement::where('requirement_id', $reqId)->first();

        if ($requirement->hlj_id != $userId) {
            $ret = $this->requestFailed(400, "用户ID与需求用户不匹配");
            return $this->response->array($ret);
        }

        $details = $requirement->requirementDetails;
        $title = '';
        foreach ($details as $detail) {
            $detail->setRequirementDetailUnavailable();
            $title .= $detail->title . '；';
        }
        foreach ($requirement->items as $item) {
            $item->is_available = false;
            $item->detail_positive->is_available = false;
            $item->detail_positive->save();
            $this->requirement->deleteRelation($requirement, $item->item_id);
            $item->save();
        }
        if ($mainOrder = $requirement->main_order) {
            foreach ($mainOrder->subOrders as $subOrder) {
                $this->suborder->deleteSubOrderByUser($subOrder);
            }
            foreach ($mainOrder->items as $item) {
                $this->item->deleteItem($item);
                $item->detail_positive->is_available = false;
                $item->detail_positive->save();
            }
            $this->mainOrder->deleteMainOrderByUser($mainOrder);
        }

        // 推送
        if (mb_strlen($title) > 12) {
            $title = mb_substr($title, 0, 12) . '...';
        } else {
            $title = rtrim($title, '；');
        }
        $notice = new WXNotice();
        $notice->requestCanceled(User::find($userId)->openid, $requirement->requirement_number, $title, '未报价');
        $requirement->state = 411;
        $requirement->save();
        $ret = $this->requestSucceed();
        return $this->response->array($ret);
    }


    // 提交需求
    public function store(Request $request, $user_id)
    {

        // 限制重复提交
        $key = md5('User:' . $user_id . ':Requirement:' . json_encode($request->get('items')));
        if (Cache::get($key)) {
            $ret = $this->requestFailed(400, "请勿重复提交需求");
            return $this->response->array($ret);
        } else {
            $expiresAt = Carbon::now()->addMinutes(5);
            Cache::put($key, 1, $expiresAt);
        }
        $user = DB::table('users')->where('hlj_id', $user_id)->first();
        $items = $request->get('items');
        for ($i = 0; $i < count($items); $i++) {
            $picMediaIds = $items[$i]['pic_urls'];
            $items[$i]['title'] = $this->deleteHtml($items[$i]['title']);
            foreach ($picMediaIds as $key => $val) {
                $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token=' . WXAccessToken::getToken() . '&media_id=' . $val;
                $fileName = $val . '.jpg';
                Event::fire(new SavePictureToQN($url, 'yeyetech', $fileName));
                $items[$i]['pic_urls'][$key] = 'http://7xljye.com1.z0.glb.clouddn.com/' . $fileName;
            }
        }
        DB::beginTransaction();
        $requirement = $this->requirement->create(
            ['detail' => json_encode($items), 'country_id' => 11],
            $user_id);
        $updated = DB::table('buyers')->where('hlj_id', $user_id)->increment('buyer_requirements_num');
        if ($requirement && $updated) {
            DB::commit();
            Event::fire(new RequirementNotification($user, $requirement, collect($items)));
            $ret = $this->requestSucceed();
        } else {
            DB::rollback();
            $ret = $this->requestFailed(400, "系统存储失败请重试");
        }
        return $this->response->array($ret);
    }

    public function deleteHtml($str)
    {
        $str = trim($str);
        $str = strip_tags($str, "");
        $str = preg_replace("{\t}", "", $str);
        $str = preg_replace("{\r\n}", "", $str);
        $str = preg_replace("{\r}", "", $str);
        $str = preg_replace("{\n}", "", $str);
        return $str;
    }
}