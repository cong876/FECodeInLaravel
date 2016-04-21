<?php


namespace App\Utils\TipTransformers;


class TaoShiJieTransformer extends BaseTipTransformer
{

    public $prefix;

    function __construct()
    {
        $this->prefix = 'TSJ';
    }

    public function transform(array $rawData)
    {
        $transformed = [];
        $rawTips = json_decode(json_encode($rawData["rst"]["shareList"]));
        foreach ($rawTips as $rawTip) {
            $uid = md5($this->prefix . $rawTip->id);
            if ($this->notExist($uid)) {
                array_push($transformed, [
                    'description' => trim($this->userTextEncode($rawTip->review_detail)),
                    'uid' => $uid,
                    'img_urls' => json_encode($rawTip->imgs),
                    'likes' => $rawTip->like_count,
                    'pub_time' => $rawTip->create_time,
                    'user_info' => json_encode(array('nickname' => $rawTip->user_name,
                        'image' => $rawTip->user_head))
                ]);
            }
        }
        return $transformed;
    }
}