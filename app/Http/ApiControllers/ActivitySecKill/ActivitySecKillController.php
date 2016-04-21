<?php


namespace App\Http\ApiControllers\ActivitySecKill;

use App\Utils\Json\ResponseTrait;
use App\Http\ApiControllers\Controller;
use App\Transforms\SecKillTransformer;
use Carbon\Carbon;
use League\Fractal;
use League\Fractal\Manager;
use Cache;
use DB;

class ActivitySecKillController extends Controller
{
    use ResponseTrait;
    private $fractal;

    public function __construct()
    {
        $this->fractal = new Manager();
    }

    public function index($activity_id, $user_id)
    {
        $user = DB::table('users')->where('hlj_id', $user_id)->first();

        // 缓存所有标志
        $ret = ['data' => []];
        $secKills = DB::table('seckills')
            ->where('activity_id', $activity_id)
            ->where('is_available', 1)
            ->orderBy('start_time', 'asc')->get();
        foreach ($secKills as $secKill) {
            $cache_key = 'SecKill:' . $secKill->id . ':Cache';
            $transformed = Cache::get($cache_key);
            if (!$transformed) {
                $resource = new Fractal\Resource\Item($secKill, new SecKillTransformer);
                $transformed = $this->fractal->createData($resource)->toArray()['data'];
            }
            $remindKey = 'User:' . $user->openid . ':SecKill:' . $secKill->id . ':Remind';
            $transformed['reminded'] = boolval(Cache::get($remindKey));
            array_push($ret['data'], $transformed);
        };

        return $this->response->array($ret);

    }
}