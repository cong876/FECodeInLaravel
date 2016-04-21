<?php


namespace App\Utils\TipTransformers;


class XiaoHongShuTransformer extends BaseTipTransformer
{
    public $prefix;

    function __construct()
    {
        $this->prefix = 'XHS';
    }

    public function transform(array $rawData)
    {
        $transformed = [];
        $rawTips = json_decode(json_encode($rawData["data"]));
        foreach ($rawTips as $rawTip) {
            $uid = md5($this->prefix . $rawTip->id);
            if ($this->notExist($uid)) {
                $publisher = $rawTip->user;
                array_push($transformed, [
                    'description' => $this->userTextEncode($rawTip->desc),
                    'uid' => $uid,
                    'img_urls' => json_encode([$rawTip->images_list[0]->url]),
                    'likes' => $rawTip->likes,
                    'pub_time' => $rawTip->time,
                    'user_info' => json_encode(array('nickname' => $publisher->nickname,
                        'image' => $publisher->images))
                ]);
            }
        }

        return $transformed;
    }
}