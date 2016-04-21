<?php


namespace App\Http\ApiControllers\UserReceivingAddress;

use App\Utils\Json\ResponseTrait;
use App\Http\ApiControllers\Controller;
use Illuminate\Http\Request;
use App\Transforms\ReceivingAddressTransformer;
use App\Models\ReceivingAddress;
use App\Repositories\ReceivingAddress\ReceivingAddressRepositoryInterface;

class UserReceivingAddressController extends Controller
{
    use ResponseTrait;

    private $receivingAddress;

    function __construct(ReceivingAddressRepositoryInterface $receivingAddress)
    {
        $this->receivingAddress = $receivingAddress;
    }

    public function index($user)
    {
        $addresses = ReceivingAddress::where('hlj_id', $user)
            ->available()
            ->get()
            ->sortByDesc('is_default');
        return $this->response->collection($addresses, new ReceivingAddressTransformer);
    }


    public function show($user, $address)
    {
        $address = ReceivingAddress::find($address);
        if ($address->hlj_id != $user) {
            $ret = $this->requestFailed(400, "用户ID与地址用户不匹配");
            return $this->response->array($ret);
        }

        return $this->response->item($address, new ReceivingAddressTransformer);

    }

    public function store(Request $request, $user)
    {
        $data = [
            'receiver_name' => $request->get('receiver_name'),
            'receiver_mobile' => $request->get('receiver_mobile'),
            'receiver_zip_code' => $request->get('zip_code'),
            'street_address' => $request->get('street_address'),
            'first_class_area' => $request->get('province'),
            'second_class_area' => $request->get('city'),
            'third_class_area' => $request->get('county')
        ];

        $new_address = $this->receivingAddress->create($data, $user);

        if ($request->get('is_default')) {
            $this->receivingAddress->setAddressToDefault($new_address, $user);
        }
        $ret = [
            'status_code' => 200,
            'id' => $new_address->receiving_addresses_id
        ];
        return $this->response->array($ret);
    }

    public function update(Request $request, $user, $address)
    {
        $address = ReceivingAddress::find($address);
        if ($address->hlj_id != $user) {
            $ret = $this->requestFailed(400, "用户ID与地址用户不匹配");
            return $this->response->array($ret);
        }

        $data = [
            'receiver_name' => $request->get('receiver_name'),
            'receiver_mobile' => $request->get('receiver_mobile'),
            'receiver_zip_code' => $request->get('zip_code'),
            'street_address' => $request->get('street_address'),
            'first_class_area' => $request->get('province'),
            'second_class_area' => $request->get('city'),
            'third_class_area' => $request->get('county')
        ];

        $updated = $this->receivingAddress->update($address, $data);
        if ($request->get('is_default')) {
            $this->receivingAddress->setAddressToDefault($address, $user);
        }

        if ($updated) {
            $ret = $this->requestSucceed();
            return $this->response->array($ret);
        }
    }

    public function delete($user, $address)
    {
        $address = ReceivingAddress::find($address);
        if ($address->hlj_id != $user) {
            $ret = $this->requestFailed(400, "用户ID与地址用户不匹配");
            return $this->response->array($ret);
        }

        if ($address->is_default) {
            $this->receivingAddress->delete($address);
            if ($candidate = ReceivingAddress::where('hlj_id', $user)->available()->first()) {
                $candidate->is_default = true;
                $candidate->save();
            }
        } else {
            $this->receivingAddress->delete($address);
        }

        $ret = $this->requestSucceed();
        return $this->response->array($ret);


    }
}