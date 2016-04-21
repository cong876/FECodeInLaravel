<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankCardBin extends Model
{
    //
    protected $hidden = ['id', 'card_bin', 'bank_id','bin_digits', 'card_digits'];

    public static function getCardInfo($cardNumber)
    {
        if (!empty($info = BankCardBin::where('card_bin', substr($cardNumber, 0, 6))->get())) {
            if(count($info) == 1) {
                // 仅有一个结果
                if(strlen($cardNumber) == $info->first()->card_digits) {
                    return $info->first();
                }
            }
            else {
                // 相同数字打头，位数不同
                foreach($info as $element) {
                    if(strlen($cardNumber) == $element->card_digits) {
                        return $element;
                    }
                }
            }
        }
        if (!empty($info = BankCardBin::where('card_bin', substr($cardNumber, 0, 9))->get())) {
            if(count($info) == 1) {
                // 仅有一个结果
                if(strlen($cardNumber) == $info->first()->card_digits) {
                    return $info->first();
                }
            }
            else {
                // 相同数字打头，位数不同
                foreach($info as $element) {
                    if(strlen($cardNumber) == $element->card_digits) {
                        return $element;
                    }
                }
            }
        }
        if (!empty($info = BankCardBin::where('card_bin', substr($cardNumber, 0, 8))->get())) {
            if(count($info) == 1) {
                // 仅有一个结果
                if(strlen($cardNumber) == $info->first()->card_digits) {
                    return $info->first();
                }
            }
            else {
                // 相同数字打头，位数不同
                foreach($info as $element) {
                    if(strlen($cardNumber) == $element->card_digits) {
                        return $element;
                    }
                }
            }
        }
        if (!empty($info = BankCardBin::where('card_bin', substr($cardNumber, 0, 7))->get())) {
            if(count($info) == 1) {
                // 仅有一个结果
                if(strlen($cardNumber) == $info->first()->card_digits) {
                    return $info->first();
                }
            }
            else {
                // 相同数字打头，位数不同
                foreach($info as $element) {
                    if(strlen($cardNumber) == $element->card_digits) {
                        return $element;
                    }
                }
            }
        }
        if (!empty($info = BankCardBin::where('card_bin', substr($cardNumber, 0, 5))->get())) {
            if(count($info) == 1) {
                // 仅有一个结果
                if(strlen($cardNumber) == $info->first()->card_digits) {
                    return $info->first();
                }
            }
            else {
                // 相同数字打头，位数不同
                foreach($info as $element) {
                    if(strlen($cardNumber) == $element->card_digits) {
                        return $element;
                    }
                }
            }
        }
        if (!empty($info = BankCardBin::where('card_bin', substr($cardNumber, 0, 10))->get())) {
            if(count($info) == 1) {
                // 仅有一个结果
                if(strlen($cardNumber) == $info->first()->card_digits) {
                    return $info->first();
                }
            }
            else {
                // 相同数字打头，位数不同
                foreach($info as $element) {
                    if(strlen($cardNumber) == $element->card_digits) {
                        return $element;
                    }
                }
            }
        }
        if (!empty($info = BankCardBin::where('card_bin', substr($cardNumber, 0, 4))->get())) {
            if(count($info) == 1) {
                // 仅有一个结果
                if(strlen($cardNumber) == $info->first()->card_digits) {
                    return $info->first();
                }
            }
            else {
                // 相同数字打头，位数不同
                foreach($info as $element) {
                    if(strlen($cardNumber) == $element->card_digits) {
                        return $element;
                    }
                }
            }
        }
        if (!empty($info = BankCardBin::where('card_bin', substr($cardNumber, 0, 3))->get())) {
            if(count($info) == 1) {
                // 仅有一个结果
                if(strlen($cardNumber) == $info->first()->card_digits) {
                    return $info->first();
                }
            }
            else {
                // 相同数字打头，位数不同
                foreach($info as $element) {
                    if(strlen($cardNumber) == $element->card_digits) {
                        return $element;
                    }
                }
            }
        }
        if (!empty($info = BankCardBin::where('card_bin', substr($cardNumber, 0, 2))->get())) {
            if(count($info) == 1) {
                // 仅有一个结果
                if(strlen($cardNumber) == $info->first()->card_digits) {
                    return $info->first();
                }
            }
            else {
                // 相同数字打头，位数不同
                foreach($info as $element) {
                    if(strlen($cardNumber) == $element->card_digits) {
                        return $element;
                    }
                }
            }
        }
    }
}
