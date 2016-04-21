<?php


namespace App\Utils\TipTransformers;


class YangMaTouNoteTransformers extends BaseTipTransformer
{

    public $prefix;

    function __construct()
    {
        $this->prefix = 'YMTNOTE';
    }
    public function transform(array $rawData)
    {
        $transformed = [];
        $rawTips = json_decode(json_encode($rawData["Result"]));
        foreach ($rawTips as $rawTip) {
            $uid = md5($this->prefix . $rawTip->NoteId);
            if ($this->notExist($uid)) {
                array_push($transformed, [
                    'description' => $this->userTextEncode($rawTip->Content),
                    'uid' => $uid,
                    'img_urls' => json_encode($rawTip->Pics),
                    'user_info' => json_encode(array('nickname' => $rawTip->UserName,
                        'image' => $rawTip->UserLogo))
                ]);
            }
        }

        return $transformed;
    }
}