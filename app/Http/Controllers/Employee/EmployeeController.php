<?php

namespace App\Http\Controllers\Employee;

use App\Models\Employee;
use App\Models\User;
use App\Repositories\Employee\EmployeeRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Seller\SellerRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination;

class EmployeeController extends Controller
{

    private $employee, $user, $seller;

    function __construct(EmployeeRepositoryInterface $employee, UserRepositoryInterface $user, SellerRepositoryInterface $seller)
    {
        $this->employee = $employee;
        $this->user = $user;
        $this->seller = $seller;
        $this->middleware('wechatauth', ['only' => ['index', 'createEmployee']]);
        $this->middleware('operator', ['except' => ['index', 'createEmployee', 'authMail']]);
        $this->middleware('super', ['except' => ['index', 'createEmployee', 'authMail']]);
    }

    public function index()
    {
        return view('Employee.operatorRegister');
    }

    public function authMail(Request $request)
    {
        $mail = $request->email;
        if (count(User::where('email', $mail)->first()) == 0) {
            return response()->json(['status' => true]);
        } else {
            return response()->json(['status' => false]);
        }
    }

    public function createEmployee(Request $request)
    {
        $mail = $request->email;
        $mobile = $request->mobile;
        $name = $request->real_name;
        $user = Auth::user();
        $this->user->updateInfo($user, ['mobile' => $mobile, 'email' => $mail, 'wx_number' => $request->wx_number
            , 'password' => bcrypt($request->password), 'secure_password' => bcrypt($request->secure_password)]);
        $this->employee->createEmployee(['hlj_id' => $user->hlj_id, 'real_name' => $name, 'identity_card_no' => $request->ID,
            'birthday' => $request->birthday, 'name_pinyin' => pinyin($name), 'name_abbreviation' => letter($name)]);
        $this->seller->createSellerInfo(['seller_type' => $request->type, 'is_available' => 0, 'country_id' => 11, 'real_name' => $name,
            'name_pinyin' => pinyin($name), 'name_abbreviation' => letter($name)], $user->hlj_id);
        if(!$user->buyer) {
            $this->user->createBuyerAccount($user);
        }
        return view('Employee.registerSuccess');


    }

    public function getAllEmployee(Request $request)
    {

        $employeesAll = Employee::get();
        $page = $request->page;
        $employee = $employeesAll->forPage($page, 15);
        $employees = new LengthAwarePaginator($employee, count($employeesAll), 15, null, array('path' => Paginator::resolveCurrentPath()));
        return view('Employee.staffManagement')->with(['employees' => $employees]);

    }

    public function getEmployeeDetail(Request $request)
    {

        $employee_id = $request->id;
        $employee = $this->employee->getById($employee_id);
        return view('Employee.staffManagementDetail')->with(['employee' => $employee]);

    }

    public function activateEmployee(Request $request)
    {
        $employee_id = $request->id;
        $employee = $this->employee->getById($employee_id);
        $employee->is_available = true;
        $employee->type = $request->type;
        $employee->save();
        return back();
    }

    public function closeEmployee(Request $request)
    {
        $employee_id = $request->id;
        $employee = $this->employee->getById($employee_id);
        $employee->is_available = false;
        $employee->save();
        return back();
    }

    public function updateEmployeeLevel(Request $request)
    {
        $employee_id = $request->id;
        $employee = $this->employee->getById($employee_id);
        $employee->op_level = $request->op_level;
        $employee->save();
        return back();
    }

    public function freeSeller(Request $request)
    {
        $seller_id = $request->id;
        $seller = $this->seller->getById($seller_id);
        $seller->is_available = true;
        $seller->save();
        return back();
    }

    public function arrestSeller(Request $request)
    {
        $seller_id = $request->id;
        $seller = $this->seller->getById($seller_id);
        $seller->is_available = false;
        $seller->save();
        return back();
    }


}
