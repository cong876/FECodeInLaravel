<?php

namespace App\Http\Controllers\Operator;

use App\Models\Activity;
use App\Models\Item;
use App\Models\ItemTag;
use App\Models\TaskItem;
use App\Utils\Json\ResponseTrait;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\Item\ItemRepositoryInterface;
use App\Repositories\MainOrder\MainOrderRepositoryInterface;
use App\Repositories\SubOrder\SubOrderRepositoryInterface;
use App\Repositories\DetailPassiveExtra\DetailPassiveExtraRepositoryInterface;
use App\Repositories\Activity\ActivityRepositoryInterface;
use App\Repositories\GroupItem\GroupItemRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Overtrue\Wechat\Url;

class ActivityController extends Controller
{
    private $item, $detail_positive, $activity, $mainOrder, $suborder, $groupItem;
    use ResponseTrait;

    function __construct(ItemRepositoryInterface $item, DetailPassiveExtraRepositoryInterface $detail_passive,
                         ActivityRepositoryInterface $activity, MainOrderRepositoryInterface $mainOrder,
                         SubOrderRepositoryInterface $subOrder, GroupItemRepositoryInterface $groupItem)
    {

        $this->middleware('operator');

        $this->detail_positive = $detail_passive;

        $this->item = $item;

        $this->activity = $activity;

        $this->mainOrder = $mainOrder;

        $this->suborder = $subOrder;

        $this->groupItem = $groupItem;

    }

    /**
     * @param Request $request
     * @return $this
     * 展示所有活动
     */
    public function allActivities(Request $request)
    {
        $activities_all = Activity::orderBy('activity_start_time', 'desc')->orderBy('activity_due_time', 'desc')->get();
        $page = $request->page;
        $activity = $activities_all->forPage($page, 20);
        $activities = new LengthAwarePaginator($activity, count($activities_all), 20, null, array('path' => LengthAwarePaginator::resolveCurrentPath()));
        return view('operation.activitiesManagement.operatingActivities')->with(['activities' => $activities]);
    }

    /**
     * @return $this
     * 展示所有主题性活动
     */
    public function allSubjectActivities()
    {
        $activities = $this->activity->getAllSubjectActivitiesWithPaginate(20);
        return view('operation.activitiesManagement.operatingActivities')->with(['activities' => $activities]);
    }

    /**
     * @return $this
     * 展示所有周期性活动
     */
    public function allPeriodActivities()
    {
        $activities = $this->activity->getAllPeriodActivitiesWithPaginate(20);
        return view('operation.activitiesManagement.operatingActivities')->with(['activities' => $activities]);
    }

    /**
     * 活动详情页
     * @param Request $request
     * @return $this
     */
    public function activityManagementDetail(Request $request)
    {
        $activity = Activity::find($request->activityId);

        // 标签
        $tags = ItemTag::where('is_available', 1)->get();
        $filteredTags = [];
        foreach ($tags as $tag) {
            array_push($filteredTags, [
                'id' => $tag->item_tag_id,
                'tag_name' => $tag->tag_name,
                'style' => json_decode($tag->tag_attributes)->style,
            ]);
        }

        // 商品
        if (empty($activity->item_order)) {
            $items = $activity->items;
        } else {
            $item_order = json_decode($activity->item_order);
            $items = [];
            for ($i = 0; $i < count($item_order); $i++) {
                array_push($items,Item::find($item_order[$i]));
            }
        }
        $filteredItems = [];
        foreach ($items as $item) {
            $meta = json_decode($item->attributes);
            $tag_ids = $meta->tag_meta ?? [];
            $order_info = $meta->activity_meta;
            array_push($filteredItems, [
                'item_id' => $item->item_id,
                'title' => $item->title,
                'buy_per_user' => $item->buy_per_user,
                'price' => $item->price,
                'country_id' => $item->country_id,
                'pic_urls' => $item->pic_urls,
                'is_on_shelf' => $item->is_on_shelf,
                'description' => $item->detail_passive->description,
                'inventory' => $item->skus->first()->sku_inventory,
                'market_price' => $order_info->market_price,
                'postage' => $order_info->postage,
                'seller_id' => $order_info->seller_id,
                'operator_id' => $order_info->operator_id,
                'tag_ids' => $tag_ids,
            ]);
        }

        // 秒杀
        $secKills = $activity->secKills ?? [];
        $filteredSecKills = [];
        foreach ($secKills as $secKill) {
            $item = Item::find($secKill->item_id);
            $meta = json_decode($item->attributes);
            $order_info = $meta->activity_meta;
            array_push($filteredSecKills, [
                'id' => (int)$secKill->id,
                'start_time' => $secKill->start_time,
                'is_available' => boolval($secKill->is_available),
                'item' => [
                    'item_id' => $item->item_id,
                    'title' => $item->title,
                    'pic_urls' => $item->pic_urls,
                    'description' => $item->detail_passive->description,
                    'price' => sprintf("%.2f",$item->price),
                    'is_on_shelf' => boolval($item->is_on_shelf),
                    'inventory' => $item->skus->first()->sku_inventory,
                    'country_id' => $item->country_id,
                    'buy_per_user' => $item->buy_per_user,
                    'market_price' => sprintf("%.2f", $order_info->market_price),
                    'operator_id' => $order_info->operator_id,
                    'postage' => sprintf("%.2f", $order_info->postage),
                    'seller_id' => $order_info->seller_id
                ]
            ]);
        }

        return view('operation.activitiesManagement.operatingActivityDetail')->with([
            'activity' => $activity,
            'tags' => $filteredTags,
            'items' => $filteredItems,
            'killItems' => $filteredSecKills
        ]);
    }

    /**
     * 创建新的活动
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createActivity(Request $request)
    {
        $id = Auth::user()->employee->employee_id;
        $activity_type = $request->activityType;
        $start_time = strtotime($request->startTime) + 36000;
        $due_time = strtotime($request->endTime) + 36000;
        $activity_start_time = date('Y-m-d H:i:s', $start_time);
        $activity_due_time = date('Y-m-d H:i:s', $due_time);
        if ($activity_type == 1) {
            if (Activity::where('activity_start_time', $activity_start_time)->where('activity_type', 1)->first()) {
                abort(419);
            }
            if ($due_time - $start_time <= 86400) {
                $activity_title = $request->startTime . ' 今日团购';
            } else {
                $activity_title = '';
            }
        } else {
            $activity_title = '';
        }
        $activity = $this->activity->createActivity(array('publisher_id' => $id, 'activity_type' => $activity_type,
            'activity_title' => $activity_title, 'activity_start_time' => $activity_start_time, 'activity_due_time' => $activity_due_time));
        return redirect(url('operator/activitiesManagementDetail/' . $activity->activity_id));
    }

    /**
     * 生成团购商品
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createItem(Request $request)
    {
        $activity = $this->activity->getById($request->activityId);
        $this->clearActivityCache($activity);
        $input_price = $request->price;
        $input_title = $request->title;
        $input_pics = $request->pic_url;
        $input_des = $request->description;
        $input_number = $request->inventory;
        $limited_number = $request->limitedNumber;

        $publisher_id = Auth::user()->employee->employee_id;
        $newItem = $this->item->create(array('title' => $input_title,
            'pic_urls' => $input_pics, 'price' => $input_price,
            'is_positive' => false, 'is_on_shelf' => true,
            'country_id' => $request->country,
            'item_type' => 2,
            'buy_per_user' => $limited_number),
            $publisher_id, null, false, array(array('sku_spec' => 'Normal',
                'sku_inventory' => $input_number, 'sku_price' => $input_price)), null, array('description' => $input_des));
        $order_info['market_price'] = $request->marketPrice;
        $order_info['operator_id'] = $request->editor;
        $order_info['seller_id'] = $request->seller;
        $order_info['postage'] = $request->postage;
        $meta = [];
        $meta['activity_meta'] = $order_info;
        $meta['tag_meta'] = $request->tag_ids;
        $newItem->attributes = json_encode($meta);
        $newItem->save();
        $activity->items()->attach($newItem->item_id);
        if (empty($activity->item_order)) {
            $order = [];
        } else {
            $order = json_decode($activity->item_order);
        }
        array_push($order, "$newItem->item_id");
        $activity->item_order = json_encode($order);
        $activity->save();
        return response()->json($newItem->item_id);
    }

    /**
     * 更新团购商品
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateItem(Request $request)
    {
        $input_price = $request->price;
        $input_title = $request->title;
        $input_pics = $request->pic_url;
        $input_des = $request->description;
        $input_number = $request->inventory;
        $limited_number = $request->limitedNumber;
        $item_id = $request->id;
        $item_update = $this->item->getById($item_id);
        $activity = $item_update->activities->first();

        $this->clearActivityCache($activity);

        $this->item->updateItem($item_update, array('title' => $input_title, 'pic_urls' => $input_pics, 'price' => $input_price,
            'is_positive' => false, 'country_id' => $request->country, 'buy_per_user' => $limited_number),
            null, array(array('sku_spec' => 'Normal', 'sku_inventory' => $input_number, 'sku_price' => $input_price)),
            null, array('description' => $input_des));
        $order_info['market_price'] = $request->marketPrice;
        $order_info['operator_id'] = $request->editor;
        $order_info['seller_id'] = $request->seller;
        $order_info['postage'] = $request->postage;
        $meta = [];
        $meta['activity_meta'] = $order_info;
        $meta['tag_meta'] = $request->tag_ids;
        $item_update->attributes = json_encode($meta);
        $item_update->save();
        return response()->json($item_id);
    }

    /**
     * 删除商品
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteItem(Request $request)
    {
        $item_id = $request->itemId;
        $item = $this->item->getById($item_id);
        $item->detail_passive->is_available = false;
        $item->detail_passive->save();
        $this->item->deleteItem($item);
        $activity = $this->activity->getById($request->activityId);
        $order = json_decode($activity->item_order);
        $position = array_search($item_id, $order);
        array_splice($order, $position, 1);
        $activity->item_order = json_encode($order);
        $activity->save();
        $this->clearActivityCache($activity);
        return response()->json(1);
    }

    /**
     * 更新活动标题及日期
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateActivityTitle(Request $request)
    {
        $activity_id = $request->activityId;
        $activity = $this->activity->getById($activity_id);
        $title = $request->activityTitle;
        $start_time = strtotime($request->startTime) + 36000;
        $due_time = strtotime($request->endTime) + 36000;
        $activity_start_time = date('Y-m-d H:i:s', $start_time);
        $activity_due_time = date('Y-m-d H:i:s', $due_time);
        $updating = Activity::where('activity_start_time', $activity_start_time)->first();
        if ($activity->activity_type == 1) {
            if ($updating && $updating->activity_id != $activity_id) {
                abort(419);
            }
        }
        $this->activity->updateActivity($activity, array('activity_title' => $title, 'activity_start_time' => $activity_start_time,
            'activity_due_time' => $activity_due_time));
        $this->clearActivityCache($activity);
        return back();
    }

    /**
     * 更新活动信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateActivityInfo(Request $request)
    {
        $activity = $this->activity->getById($request->activityId);
        $this->clearActivityCache($activity);
        $forward_info['forward_title'] = $request->title;
        $forward_info['forward_description'] = $request->description;
        $forward_info['forward_pic_url'] = $request->forward_url;
        $pic_urls = $request->pic_urls;
        if (count($request->order) > 0) {
            $order = json_encode($request->order);
        } else {
            $order = $request->order;
        }
        if ($request->type == 1) {
            $activity_info['pic_url'] = $request->activity_pic_url;
            $activity_info['url'] = $request->activity_url;
            $this->activity->updateActivity($activity, array('pic_urls' => $pic_urls, 'forward_info' => json_encode($forward_info),
                'activity_info' => json_encode($activity_info), 'item_order' => $order));
            return response()->json(1);
        } else {
            $this->activity->updateActivity($activity, array('pic_urls' => $pic_urls, 'forward_info' => json_encode($forward_info),
                'item_order' => $order));
            return response()->json(1);
        }

    }

    /**
     * 发布活动
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function toPublish(Request $request)
    {
        $activity = $this->activity->getById($request->activityId);
        $activity->is_available = true;
        $activity->save();
        return redirect(url('operator/allActivities'));
    }

    /**
     * 删除活动
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteActivity(Request $request)
    {
        $activity = $this->activity->getById($request->activityId);
        $this->activity->deleteActivity($activity);
        return back();
    }

    public function refresh(Request $request)
    {
        $activity = $this->activity->getById($request->activityId);
        $this->clearActivityCache($activity);
        return back();
    }


    /**
     * 查询活动
     * @param Request $request
     * @return $this
     */
    public function activitySearch(Request $request)
    {
        $time_activity = strtotime($request->activitiesTime) + 36000;
        $date_activity = date('Y-m-d H:i:s', $time_activity);
        $activities_all = Activity::where('activity_start_time', '<=', $date_activity)->where('activity_due_time', '>=', $date_activity)
            ->orderBy('activity_start_time', 'desc')->orderBy('activity_due_time', 'desc')->get();
        $page = $request->page;
        $activity = $activities_all->forPage($page, 20);
        $activities = new LengthAwarePaginator($activity, count($activities_all), 20, null, array('path' => LengthAwarePaginator::resolveCurrentPath()));
        return view('operation.activitiesManagement.operatingActivities')->with(['activities' => $activities]);
    }

    /**
     * 生成金币商品
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createGoldItem(Request $request)
    {
        $input_title = $request->title;
        $input_pics = $request->pic_url;
        $input_des = $request->description;
        $input_number = $request->inventory;
        $publisher_id = Auth::user()->employee->employee_id;
        $newItem = $this->item->create(array('title' => $input_title, 'pic_urls' => [$input_pics], 'price' => 0,
            'is_positive' => false, 'is_on_shelf' => false, 'country_id' => $request->country, 'item_type' => 3),
            $publisher_id, null, false, array(array('sku_spec' => 'Normal',
                'sku_inventory' => $input_number, 'sku_price' => 0)), null, array('description' => $input_des));
        $order_info['market_price'] = $request->marketPrice;
        $order_info['operator_id'] = $request->editor;
        $order_info['seller_id'] = $request->seller;
        $gold_item = new TaskItem;
        $gold_item->item_id = $newItem->item_id;
        $gold_item->coins = $request->gold;
        $gold_item->save();
        $meta = [];
        $meta['activity_meta'] = $order_info;
        $newItem->attributes = json_encode($meta);
        $newItem->save();
        return response()->json($newItem->item_id);
    }

    /**
     * 更新金币商品
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateGoldItem(Request $request)
    {
        $input_title = $request->title;
        $input_pics = $request->pic_url;
        $input_des = $request->description;
        $input_number = $request->inventory;
        $item_id = $request->id;
        $item_update = $this->item->getById($item_id);
        $this->item->updateItem($item_update, array('title' => $input_title, 'pic_urls' => [$input_pics],
            'is_positive' => false, 'country_id' => $request->country),
            null, array(array('sku_spec' => 'Normal', 'sku_inventory' => $input_number)),
            null, array('description' => $input_des));
        $order_info['market_price'] = $request->marketPrice;
        $order_info['operator_id'] = $request->editor;
        $order_info['seller_id'] = $request->seller;
        $gold_item = TaskItem::where('item_id', $item_id)->first();
        $gold_item->coins = $request->gold;
        $gold_item->save();
        $meta = [];
        $meta['activity_meta'] = $order_info;
        $item_update->attributes = json_encode($meta);
        $item_update->save();
        return response()->json($item_id);
    }

    /**
     * 删除金币商品
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteGoldItem(Request $request)
    {
        $item_id = $request->itemId;
        $item = $this->item->getById($item_id);
        $item->detail_passive->is_available = false;
        $item->detail_passive->save();
        $this->item->deleteItem($item);
        $gold_item = TaskItem::where('item_id', $item_id)->first();
        $gold_item->delete();
        return response()->json(1);
    }

    /**
     * 发布金币商品
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function publishGoldItem(Request $request)
    {
        $item_id = $request->itemId;
        $item = $this->item->getById($item_id);
        $item->is_on_shelf = true;
        $item->save();
        return response()->json(1);
    }

    /**
     * 取消发布金币商品
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelPublishGoldItem(Request $request)
    {
        $item_id = $request->itemId;
        $item = $this->item->getById($item_id);
        $item->is_on_shelf = false;
        $item->save();
        return response()->json(1);
    }

    /**
     * 获取金币商品
     * @return $this
     */
    public function getGoldItem()
    {
        $items = Item::where('item_type', 3)->get();
        return view('operation.activitiesManagement.goldActivity')->with(['items' => $items]);
    }

    /**
     * 生成福袋商品
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createLuckyBagItem(Request $request)
    {
        $input_title = $request->title;
        $input_pics = $request->pic_url;
        $input_des = $request->description;
        $input_number = $request->inventory;
        $price = $request->price;
        $publisher_id = Auth::user()->employee->employee_id;
        $newItem = $this->item->create(array('title' => $input_title, 'pic_urls' => [$input_pics], 'price' => $price,
            'is_positive' => false, 'is_on_shelf' => false, 'country_id' => $request->country, 'item_type' => 4, 'buy_per_user' => 1),
            $publisher_id, null, false, array(array('sku_spec' => 'Normal',
                'sku_inventory' => $input_number, 'sku_price' => $price)), null, array('description' => $input_des));
        $order_info['market_price'] = $request->marketPrice;
        $order_info['operator_id'] = $request->editor;
        $order_info['postage'] = $request->postage;
        $order_info['seller_id'] = $request->seller;
        $meta = [];
        $meta['activity_meta'] = $order_info;
        $newItem->attributes = json_encode($meta);
        $newItem->save();
        return response()->json($newItem->item_id);
    }

    /**
     * 更新福袋商品
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateLuckyBagItem(Request $request)
    {
        $input_title = $request->title;
        $input_pics = $request->pic_url;
        $input_des = $request->description;
        $input_number = $request->inventory;
        $item_id = $request->id;
        $price = $request->price;
        $item_update = $this->item->getById($item_id);
        $this->item->updateItem($item_update, array('title' => $input_title, 'pic_urls' => [$input_pics], 'price' => $price,
            'is_positive' => false, 'country_id' => $request->country),
            null, array(array('sku_spec' => 'Normal', 'sku_inventory' => $input_number, 'sku_price' => $price)),
            null, array('description' => $input_des));
        $order_info['market_price'] = $request->marketPrice;
        $order_info['operator_id'] = $request->editor;
        $order_info['postage'] = $request->postage;
        $order_info['seller_id'] = $request->seller;
        $meta = [];
        $meta['activity_meta'] = $order_info;
        $item_update->attributes = json_encode($meta);
        $item_update->save();
        return response()->json($item_id);
    }

    /**
     * 删除福袋商品
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteLuckyBagItem(Request $request)
    {
        $item_id = $request->itemId;
        $item = $this->item->getById($item_id);
        $item->detail_passive->is_available = false;
        $item->detail_passive->save();
        $this->item->deleteItem($item);
        return response()->json(1);
    }

    /**
     * 发布福袋商品
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function publishLuckyBagItem(Request $request)
    {
        $item_id = $request->itemId;
        $item = $this->item->getById($item_id);
        $item->is_on_shelf = true;
        $item->save();
        return response()->json(1);
    }

    /**
     * 取消发布福袋商品
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelPublishLuckyBagItem(Request $request)
    {
        $item_id = $request->itemId;
        $item = $this->item->getById($item_id);
        $item->is_on_shelf = false;
        $item->save();
        return response()->json(1);
    }

    /**
     * 获取福袋商品
     * @return $this
     */
    public function getLuckyBagItem()
    {
        $items = Item::where('item_type', 4)->get();
        return view('operation.activitiesManagement.luckyBagActivity')->with(['items' => $items]);
    }

    public function clearActivityCache(Activity $activity)
    {
        Cache::forget('PeriodActivity:' . $activity->activity_id);
        Cache::forget('ActivityInfo:' . $activity->activity_id);
    }

    /**
     * 上架团购商品
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function publishGroupItem(Request $request)
    {
        $item_id = $request->itemId;
        $item = $this->item->getById($item_id);
        if ($item->skus->first()->sku_inventory != 0) {
            $item->is_on_shelf = true;
            $item->save();
        }
        $activity = $item->activities->first();
        $this->clearActivityCache($activity);
        return response()->json($this->requestSucceed());
    }

    /**
     * 下架团购商品
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelGroupItem(Request $request)
    {
        $item_id = $request->itemId;
        $item = $this->item->getById($item_id);
        $item->is_on_shelf = false;
        $item->save();
        $activity = $item->activities->first();
        $this->clearActivityCache($activity);
        return response()->json($this->requestSucceed());
    }

    public function getShortUrl(Request $request, $item_id)
    {
        $item = Item::find($item_id);
        $type = $request->input('type');
        if (!$item->shorten_urls) {
            $url = new Url(config('wx.appId'), config('wx.appSecret'));
            switch($type) {
                case 'theme':
                    $shortUrl = $url->short('http://www.yeyetech.net/app/wx?#/activity/'. $request->activity_id .'#item'.$item_id);
                    break;
                case 'period':
                default:
                    $shortUrl = $url->short('http://www.yeyetech.net/app/wx?#/periodActivity#item'.$item_id);
            }
            $item->shorten_urls = $shortUrl;
            $item->save();
        } else {
            $shortUrl =  $item->shorten_urls;
        }

        return response()->json([
           'shortUrl' => $shortUrl
        ]);
    }

}