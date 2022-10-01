<?php

namespace App\Http\Controllers\Auth;

use App\Business;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

use App\Utils\BusinessUtil;

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
    public function __construct(BusinessUtil $businessUtil)
    {
        $this->middleware('guest')->except('logout');
        $this->businessUtil = $businessUtil;
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

    public function logout()
    {
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
