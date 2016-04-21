<?php

namespace App\Http\Controllers\Operator;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Auth\ThrottlesLogins;

class OperatorAuthController extends Controller
{
    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    // 登录后跳转待领取
    protected $redirectTo = '/operator/waitAccept';
    protected $redirectAfterLogout = 'operator/login';


    /**
     * 运营登录方法
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function postLogin(Request $request)
    {
        $request->session()->flush();
        $mobileOrEmail = $request->input('mobileOrEmail');
        $credentials = null;
        if (preg_match('/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/', $mobileOrEmail)) {

            $this->validate($request, [
                'mobileOrEmail' => 'required|email', 'password' => 'required',
            ]);
            $credentials = $this->getCredentialsForEmail($request);
        } elseif (preg_match('/^1\d{10}/', $mobileOrEmail)) {
            $this->validate($request, [
                'mobileOrEmail' => 'required|digits:11', 'password' => 'required',
            ]);
            $credentials = $this->getCredentialsForMobile($request);
        } else {
            // 返回注册页，这个是没有登录成功的情况
            return redirect(url('operator/login'))
                ->withInput($request->only('mobileOrEmail', 'remember'))
                ->withErrors([
                    'mobileOrEmail' => $this->getFailedLoginMessage(),
                ]);

        }
        // 尝试登录
        if (Auth::attempt($credentials, $request->has('remember'))) {
            $user = Auth::user();
            $employee = $user->employee;
            // 如果是运营人员
            if (!empty($employee)) {
                // 所有操作权限大于3的红领巾员工都可以登录
                Session::put('role', 'op_' . $employee->type);
                Session::put('op_level', $employee->op_level);
                return redirect()->intended($this->redirectPath());
            } else {
                // 其它用户登录了运营后台，也许是测试吧或者是黑客啥的。。。
                $request->session()->flush(); // 把他从Session干掉
                abort(401); // 告诉他他不是管理员
            }
        } else {
            // 返回注册页，这个是没有登录成功的情况
            return redirect(url('operator/login'))
                ->withInput($request->only('mobileOrEmail', 'remember'))
                ->withErrors([
                    'mobileOrEmail' => $this->getFailedLoginMessage(),
                ]);

        }

    }

    /**
     * 若用户使用邮箱登陆，则使用该条件
     *
     * @param Request $request
     * @return array
     */
    protected function getCredentialsForEmail(Request $request)
    {
        $temp = $request->only('mobileOrEmail', 'password');
        $emailCredential['email'] = $temp['mobileOrEmail'];
        $emailCredential['password'] = $temp['password'];
        return $emailCredential;
    }

    /**
     * 若用户使用手机登陆，则使用该条件
     *
     * @param Request $request
     * @return array
     */
    protected function getCredentialsForMobile(Request $request)
    {
        $temp = $request->only('mobileOrEmail', 'password');
        $mobileCredential['mobile'] = $temp['mobileOrEmail'];
        $mobileCredential['password'] = $temp['password'];
        return $mobileCredential;

    }

    public function getLogout()
    {

        Session::forget('role');
        Session::forget('op_level');

        Auth::logout();

        return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : '/');
    }

    public function getLogin()
    {
        return view('operation.login');
    }
}
