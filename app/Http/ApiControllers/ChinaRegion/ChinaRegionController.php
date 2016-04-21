<?php

namespace App\Http\ApiControllers\ChinaRegion;

use App\Http\ApiControllers\Controller;
use App\Utils\Json\ResponseTrait;
use App\Transforms\ChinaRegionTransformer;
use DB;

class ChinaRegionController extends Controller
{
    use ResponseTrait;

    public function index($level = 1)
    {
        $regions = DB::table('china_regions')->where('level', $level)->get();
        return $this->response->collection(collect($regions), new ChinaRegionTransformer);
    }

    public function show($code)
    {
        $region = DB::table('china_regions')->where('code', $code)->first();
        return $this->response->item($region, new ChinaRegionTransformer);
    }

    public function showSubRegions($code)
    {
        if ( $parent = DB::table('china_regions')->where('code', $code)->first()) {
            if ($subRegions = DB::table('china_regions')->where('parent_id', $parent->china_region_id)->get()) {
                return $this->response->collection(collect($subRegions), new ChinaRegionTransformer);
            } else {
                return $this->response->item($parent, new ChinaRegionTransformer);
            }
        }

    }

}