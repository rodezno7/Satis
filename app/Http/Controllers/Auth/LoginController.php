<?php

namespace App\Http\Controllers\Auth;

use App\Binnacle;
use App\Business;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use App\Utils\BusinessUtil;
use App\Utils\Util;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * All Utils instance.
     *
     */
    protected $businessUtil;
    protected $util;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(BusinessUtil $businessUtil, Util $util)
    {
        $this->middleware('guest')->except('logout');
        $this->businessUtil = $businessUtil;
        $this->util = $util;
    }

    /**
     * Change authentication from email to username
     *
     * @return void
     */
    public function username()
    {
        return 'username';
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        $business = Business::where('is_active', 1)->pluck('name', 'id');
        return view('auth.login')->with(compact('business'));
    }

    public function postLogin(Request $request){
        $credenciales = $this->validate(request(), [
            'username' => 'required',
            'password' => 'required'
        ]);

        if(Auth::attempt($credenciales)){
            $user = User::where('username', $request->input('username'))->first();
            $user->deleteSessionsFromOtherDevices();
            $action = 'login';

            $this->util->registerBinnacle($user->id, 'login', null, null, null);

            return redirect()->route('home');
        }

        return redirect()->back()->withInput($request->only('username'))->withErrors([
            'username' => 'Las credenciales proporcionadas no son válidas.',
        ]);
    }


    public function logout()
    {
        request()->session()->invalidate();
        request()->session()->flush();
        \Auth::logout();
        return redirect('/');
    }

    /**
     * The user has been authenticated.
     * Check if the business is active or not.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        if (!$user->business->is_active){
            return redirect('/login')
            ->with(
              'status',
              ['success' => 0, 'msg' => __('lang_v1.business_inactive')]
          );
        } elseif (($user->status == 'inactive') || ($user->status == 'terminated') || (!in_array($request->input('business_id'), $user->permittedBusiness()))){
            \Auth::logout();
            return redirect('/login')
            ->with(
              'status',
              ['success' => 0, 'msg' => __('lang_v1.user_inactive')]
          );
        }
    }

    protected function redirectTo()
    {
        $user = \Auth::user();
        if (!$user->can('dashboard.data') && $user->can('sell.create')) {
            return '/pos/create';
        }

        return '/home';
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password', 'business_id');
    }
}
