<?php


namespace App\Http\Controllers\Operator;

use App\Http\ApiControllers\Controller;
use App\Repositories\ItemTag\ItemTagRepositoryInterface;
use Illuminate\Http\Request;
use App\Utils\Json\ResponseTrait;
use Auth;


class OperatorItemTagController extends Controller
{
    private $itemTag;
    use ResponseTrait;

    public function __construct(ItemTagRepositoryInterface $itemTag)
    {
        $this->itemTag = $itemTag;
        $this->middleware('operator');
    }

    public function store(Request $request)
    {
        $data = $this->filterRawData($request);
        $data['operator_id'] = Auth::user()->employee->employee_id;

        $created = $this->itemTag->create($data);
        if ($created) {
            $ret = [
                "id" => $created->item_tag_id,
                "status_code" => 200,
                "message" => "OK"
            ];
        } else {
            $ret = $this->requestFailed(400, "标签创建失败,请重试");
        }
        return $this->response->array($ret);
    }

    public function update(Request $request, $itemTag_id)
    {
        $target = $this->itemTag->getById($itemTag_id);

        $data = $this->filterRawData($request);
        $updated = $this->itemTag->update($target, $data);
        if ($updated) {
            $ret = $this->requestSucceed();
        } else {
            $ret = $this->requestFailed(400, "标签更新失败,请重试");
        }
        return $this->response->array($ret);


    }

    public function invalid($itemTag_id)
    {
        $target = $this->itemTag->getById($itemTag_id);
        $invalided = $this->itemTag->invalid($target);
        if ($invalided) {
            $ret = $this->requestSucceed();
        } else {
            $ret = $this->requestFailed(400, "标签更新失败,请重试");
        }
        return $this->response->array($ret);
    }

    public function valid($itemTag_id)
    {
        $target = $this->itemTag->getById($itemTag_id);
        $valid = $this->itemTag->valid($target);
        if ($valid) {
            $ret = $this->requestSucceed();
        } else {
            $ret = $this->requestFailed(400, "标签更新失败,请重试");
        }
        return $this->response->array($ret);

    }

    public function hide($itemTag_id)
    {
        $target = $this->itemTag->getById($itemTag_id);
        $hide = $this->itemTag->delete($target);
        if ($hide) {
            $ret = $this->requestSucceed();
        } else {
            $ret = $this->requestFailed(400, "标签隐藏失败,请重试");
        }
        return $this->response->array($ret);
    }

    private function filterRawData(Request $raw)
    {
        $filtered =   $raw->only('tag_name', 'tag_description', 'priority');
        $filtered['tag_attributes'] = json_encode(
            [
                'style' => $raw->get('style')
            ]
        );
        return $filtered;
    }

}