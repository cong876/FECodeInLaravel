<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Utils\TipTransformers\TaoShiJieTransformer;
use App\Utils\TipTransformers\YangMaTouNoteTransformers;
use Illuminate\Http\Request;
use App\Utils\TipTransformers\XiaoHongShuTransformer;

class TipCollectController extends Controller
{

    public function store(Request $request)
    {
        $type = $request->type;
        $data = $request->data;
        $ret = null;
        switch ($type) {
            case '小红书':
                $ret = XiaoHongShuTransformer::store($data, '小红书');
                break;
            case '淘世界':
                $ret = TaoShiJieTransformer::store($data, '淘世界');
                break;
            case '洋码头笔记':
                $ret = YangMaTouNoteTransformers::store($data, '洋码头笔记');
        }

        return response()->json($ret);
    }

}