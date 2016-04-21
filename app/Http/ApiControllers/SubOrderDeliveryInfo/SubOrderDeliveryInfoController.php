<?php


namespace App\Http\ApiControllers\SubOrderDeliveryInfo;

use App\Http\ApiControllers\Controller;
use App\Models\DeliveryCompany;
use App\Models\SubOrder;
use App\Utils\Curl\CurlRequester;
use App\Utils\Json\ResponseTrait;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;


class SubOrderDeliveryInfoController extends Controller
{
    use ResponseTrait;

    public function show($sub_order_id)
    {
        $subOrder = SubOrder::find($sub_order_id);
        $ret = []; // 承载返回物流信息包
        $withData = [];

        if ($delivery_info = $subOrder->deliveryInfo) {
            $ret['phases'] = [
                array(
                    'name' => $delivery_info->deliveryCompany ? $delivery_info->deliveryCompany->company_name : $delivery_info->delivery_company_info,
                    'number' => $delivery_info->delivery_order_number
                )
            ];

            if ($delivery_info->is_second_phase) {
                $ret['is_two_phases'] = true;
                // First Phase
                $first_package = [];
                $second_package = [];
                if ($delivery_info->delivery_company_info) {
                    // 一段物流没有接口查询方法
                    if ($delivery_info->delivery_company_info == '人肉') {
                        $first_package['track_log'] = [
                            [
                                'time' => $subOrder->delivery_time->toDateTimeString(),
                                'context' => '商品将由买手人肉带回后再分发',
                                'ftime' => $subOrder->delivery_time->toDateTimeString()
                            ]
                        ];
                    } else {
                        $first_package['track_log'] = [
                            [
                                'time' => $subOrder->delivery_time->toDateTimeString(),
                                'context' => '请到物流公司官网查询物流信息',
                                'ftime' => $subOrder->delivery_time->toDateTimeString()
                            ]
                        ];
                    }
                    $first_package['status'] = 201;
                    $first_package['state'] = 401; // 401请到官网查询
                } else {
                    $queryUrl = $subOrder->deliveryInfo->delivery_related_url;
                    $split = explode('?', $queryUrl);
                    if (count($split) == 2) {
                        $first_package = $this->queryInfo($split[1]);
                    }
                }

                // Second Phase
                if ($delivery_info->second_phase_info) {

                    // 一段已经签收
                    if ($first_package['state'] == 3) {
                        array_pop($first_package['track_log']);
                        $deletedLogistic = array_pop($first_package['track_log']);
                        array_push($first_package['track_log'], [
                            'time' => $deletedLogistic['time'],
                            'context' => '红领巾分拨中心分拣打包中',
                            'ftime' => $deletedLogistic['ftime']
                        ]);
                    }

                    // 一段只能在官网查询
                    if ($first_package['state'] == 401) {
                        array_pop($first_package['track_log']);
                        array_push($first_package['track_log'], [
                            'time' => $subOrder->updated_at->toDateTimeString(),
                            'context' => '红领巾分拨中心分拣打包中',
                            'ftime' => $subOrder->updated_at->toDateTimeString()
                        ]);
                    }

                    $second_info = json_decode($delivery_info->second_phase_info);

                    if ($second_info->delivery_company_info) {
                        array_push($ret['phases'],
                            array(
                                'name' => $second_info->delivery_company_info,
                                'number' => $second_info->delivery_order_number
                            )
                        );
                        $second_package['track_log'] = [
                            [
                                'time' => date('Y-m-d H:i:s'),
                                'context' => '商品将通过非快递方式直接交付',
                                'ftime' => date('Y-m-d H:i:s')
                            ]
                        ];
                        $second_package['status'] = 401;
                        $second_package['state'] = null;

                    } else {
                        $secondQueryUrl = $second_info->delivery_related_url;
                        $split = explode('?', $secondQueryUrl);
                        if (count($split) == 2) {
                            $second_package = $this->queryInfo($split[1]);
                        }

                        // 推入第二段物流公司信息包
                        array_push($ret['phases'],
                            array(
                                'name' => DeliveryCompany::find($second_info->delivery_company_id)->company_name,
                                'number' => $second_info->delivery_order_number
                            )
                        );
                    }
                } else {
                    if ($first_package['state'] == 3) {
                        array_pop($first_package['track_log']);
                        $deletedLogistic = array_pop($first_package['track_log']);
                        array_push($first_package['track_log'], [
                            'time' => $deletedLogistic['time'],
                            'context' => '红领巾分拨中心分拣打包中',
                            'ftime' => $deletedLogistic['ftime']
                        ]);
                    }
                    $second_package['track_log'] = [];
                    $second_package['status'] = 201;
                    $second_package['state'] = null;
                }

                // 二段物流状态
                if ($first_package['state'] != 3 && $second_package['status'] == 201) {
                    $ret['message'] = '快件正前往红领巾分拨中心';
                }

                if ($first_package['state'] == 3 || ($delivery_info->second_phase_info && $second_package['status'] == 201)) {
                    $ret['message'] = '红领巾分拨中心分拣打包中';
                }

                if ($second_package['status'] == 200 && $second_package['state'] != 3) {
                    $snapshot = json_decode($subOrder->snapshot->paid_snapshot);
                    $ret['message'] = '快件正前往目的地' . $snapshot->address->province->name . $snapshot->address->city->name;
                }

                if ($second_package['status'] == 401) {
                    $ret['message'] = '商品将通过非快递方式直接交付';
                }

                if ($second_package['state'] == 3 || $subOrder->sub_order_state == 301) {
                    $ret['message'] = '已签收';
                }

                // 合并
                $ret['track_log'] = array_merge($first_package['track_log'], $second_package['track_log']);
                $withData['data'] = $ret;
                return $this->response->array($withData);
            } else {
                // 只有一段物流
                $ret['is_two_phases'] = false;
                if ($delivery_info->delivery_company_info) {
                    // 一段物流没有接口查询方法
                    $ret['status'] = 201;
                    $ret['state'] = 401; // 401请到官网查询

                    // message
                    if ($subOrder->sub_order_state != 301) {
                        $ret['message'] = '请到物流公司官网查询物流信息';
                    } else {
                        $ret['message'] = '已签收';
                    }

                    $ret['track_log'] = [
                        [
                            'time' => $subOrder->delivery_time->toDateTimeString(),
                            'context' => '请到物流公司官网查询物流信息',
                            'ftime' => $subOrder->delivery_time->toDateTimeString()
                        ]
                    ];
                } else {
                    $queryUrl = $subOrder->deliveryInfo->delivery_related_url;
                    $split = explode('?', $queryUrl);
                    if (count($split) == 2) {
                        $response = $this->queryInfo($split[1]);

                        // message
                        if ($response['status'] == 201) {
                            $ret['message'] = '买手已发货,请耐心等待物流信息更新';
                        }

                        if ($response['status'] == 200 && $response['state'] != 3) {
                            $snapshot = json_decode($subOrder->snapshot->paid_snapshot);
                            $ret['message'] = '快件正前往目的地' . $snapshot->address->province->name . $snapshot->address->city->name;
                        }

                        if ($response['state'] == 3 || $subOrder->sub_order_state == 301) {
                            $ret['message'] = '已签收';
                        }

                        $ret['track_log'] = $response['track_log'];
                    }
                }

                $withData['data'] = $ret;
                return $this->response->array($withData);

            }
        } else {
            if ($subOrder->sub_order_state == 301) {
                $ret['message'] = '已签收';
            } else {
                $ret['message'] = '未填写物流信息,请联系红领巾客服';
            }
            $ret['phases'] = [
                array(
                    'name' => '未填写物流信息',
                    'number' => '无'
                )
            ];
            $ret['track_log'] = [];
            $withData['data'] = $ret;
            return $this->response->array($withData);
        }
    }

    private function queryInfo($query)
    {
        // 使用缓存策略

        $response = Cache::get('LogisticInfo:' . $query, function () use ($query) {
            $requester = new CurlRequester();
            $meatHost = ['http://hljmeat1.duapp.com', 'http://hljmeat2.duapp.com'];
            shuffle($meatHost);
            $requester->setUrl($meatHost[0] . '/gocha.php?' . $query);
            $requester->setMethod("GET");
            $response = $requester->executeAndReturnPhpArray();
            if (!empty($response['state']) && $response['state'] == 3) {
                Cache::forever('LogisticInfo:' . $query, $response);
            } else {
                $expiresAt = Carbon::now()->addHours(3);
                Cache::put('LogisticInfo:' . $query, $response, $expiresAt);
            }
            return $response;
        });


        if ($response['status'] == 201 || $response['status'] == 403 || $response['status'] == 400) {
            // 查询无结果或者异常
            $package = [];
            $package['track_log'] = [];
            $package['status'] = 201;
            $package['state'] = 400;

        } else {
            // 能获得查询结果
            $package = [];
            $ret = array_sort($response['data'], function ($value) {
                return $value['ftime'];
            });
            $package['track_log'] = array_values($ret);
            if ($response['state'] == 3) {
                $last = end($package['track_log']);
                array_push($package['track_log'], [
                    'time' => $last['time'],
                    'context' => '快件已签收，感谢使用红领巾小助手',
                    'ftime' => $last['ftime']
                ]);
            }
            $package['status'] = 200;
            $package['state'] = $response['state'];
        }
        return $package;
    }
}