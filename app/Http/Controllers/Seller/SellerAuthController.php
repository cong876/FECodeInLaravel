<?php

namespace App\Http\Controllers\Seller;

use Illuminate\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Auth\ThrottlesLogins;

class SellerAuthController extends Controller
{
    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    protected $redirectTo = '/seller/management';
    protected $redirectAfterLogout = 'seller/login';


    private $guard;
    public function __construct(Guard $guard)
    {
        $this->guard = $guard;
        $this->middleware('guest', ['except' => 'getLogout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @param  User   $user
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data, User $user)
    {
        return Validator::make($data, [
            'mobile' => 'required|digits:11|max:255|unique:users,mobile,'. $user['hlj_id']. ',hlj_id',
            'email' => 'email|max:255|unique:users,email,'. $user['hlj_id'] .',hlj_id',
            'wx_number' => 'max:255',
            'secure_password' => 'required|confirmed|min:6',
        ]);
    }
    /**
     * 在用户记录中增加手机号、邮箱、微信号、安全密码，其中邮箱、微信号选填
     *
     * @param  array  $data
     * @return User
     */
    protected function updateUser(array $data)
    {
        return \Event::fire(new UserUpdateUserInfo(Auth::user(), $data));

    }

    /**
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function postLogin(Request $request)
    {
        $request->session()->flush();
        $mobileOrEmail = $request->input('mobileOrEmail');
        $credentials = null;
        if (preg_match('/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/', $mobileOrEmail))
        {

            $this->validate($request, [
                'mobileOrEmail' => 'required|email', 'password' => 'required',
            ]);
            $credentials = $this->getCredentialsForEmail($request);
        }

        elseif (preg_match('/^1\d{10}/', $mobileOrEmail))
        {
            $this->validate($request, [
                'mobileOrEmail' => 'required|digits:11', 'password' => 'required',
            ]);
            $credentials = $this->getCredentialsForMobile($request);
        }
        else {
            return redirect($this->loginPath())
                ->withInput($request->only('mobileOrEmail', 'remember'))
                ->withErrors([
                    'mobileOrEmail' => $this->getFailedLoginMessage(),
                ]);

        }
        if (Auth::attempt($credentials, $request->has('remember'))) {
            $user = Auth::user();
            $seller = $user->seller;
            if(!empty($seller)) {

                if($seller->type == 1) {
                    Session::put('sellerRole', $seller->type);
                }
            }

            return redirect()->intended($this->redirectPath());
        }

    }


    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postUpdate(Request $request)
    {
        $validator = $this->validator($request->all(), Auth::user());
        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        if($this->updateUser($request->only('mobile','email','wx_number','secure_password')))
        {

            return response()->json(["state" => 'ok']);
        };

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
    public function getUpdate()
    {
        return view('auth.update');
    }

    public function getLogout()
    {

        Session::forget('sellerRole');

        Auth::logout();

        return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : '/');
    }

    public function getLogin()
    {
        return view('sellerOrder.login');
    }
}