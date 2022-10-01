<?php

namespace App\Http\Controllers;

use DB;
use App\User;
use App\System;
use App\Contact;
use App\Business;

use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use App\Mail\NotifyUserCreated;

use Spatie\Permission\Models\Role;

use Illuminate\Support\Facades\Mail;
use App\Notifications\NewNotification;
use Yajra\DataTables\Facades\DataTables;

class ManageUserController extends Controller
{
    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        if (!auth()->user()->can('user.view') && !auth()->user()->can('user.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $roles_q = DB::table('roles')
        ->select(DB::raw("left(roles.name,LOCATE('#', roles.name) - 1) as rol, id"))
        ->where('business_id', $business_id)
        ->orderBy('roles.name', 'asc');

        if ( !auth()->user()->can('su.su') ) {

            $roles_q->where('is_default', '!=', 1);

        }

        $roles = $roles_q->pluck('rol', 'id');
        

        $username_ext = $this->getUsernameExtension();

        // Gets business
        $business = Business::where('is_active', 1)->get();

        return view('manage_user.index', compact('roles', 'username_ext', 'business'));
    }

    public function getUsersData() {

        if (!auth()->user()->can('user.view') && !auth()->user()->can('user.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $user_id = request()->session()->get('user.id');
        $users = DB::table('users')
        ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
        ->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
        ->select('users.id', 'users.username', 'users.first_name', 'users.last_name', 'users.email', DB::raw("CONCAT(COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, '')) as full_name, left(roles.name,LOCATE('#', roles.name) - 1) as rol"))
        ->where('users.business_id', $business_id)
        ->where('users.id', '!=', $user_id)
        ->whereNull('deleted_at')
        ->get();
        return DataTables::of($users)->toJson();
    }

    public function getUsersDatos($business_id) {
        
        return User::leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
        ->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
        ->select('users.id', 'users.username', 'users.first_name', 'users.last_name', 'users.email', DB::raw("CONCAT(COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, '')) as full_name, left(roles.name,LOCATE('#', roles.name) - 1) as rol"))
        ->where('users.business_id', $business_id)
        ->where('users.is_cmmsn_agnt', 0)
        ->whereNull('deleted_at')
        ->get();
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        return redirect('/home');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        if (!auth()->user()->can('user.create')) {
            abort(403, 'Unauthorized action.');
        }

        if($request->ajax()) {

            $pass = $request->input('password');
            $validateData = $request->validate
            (                
                [

                    'first_name' => 'required',
                    'email' => 'required|email',
                    'username' => 'required',
                    'role' => 'required',
                    'password' => 'required',
                    'confirm_password' => 'required|in:'.$pass,
                    'business' => 'required',
                ]

            );
            try {

                $password_mode = $request->input('password_mode');
                $user_details = $request->only(['first_name', 'last_name', 'username', 'email', 'password']);
                $user_details['status'] = 'pending';
                $user_details['language'] = 'es';

                if($password_mode == 'generated') {

                    $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
                    $password = "";
                    for($i = 0; $i < 9; $i ++) {
                        $password .= substr($str,rand(0, 61), 1);
                    }
                    $user_details['password'] = bcrypt($password);
                } else {
                    $password = $request->input('password');
                    $user_details['password'] = bcrypt($user_details['password']);
                }

                $ref_count = $this->moduleUtil->setAndGetReferenceCount('username');
                
                if(blank($user_details['username'])) {
                    $user_details['username'] = $this->moduleUtil->generateReferenceNumber('username', $ref_count);
                }

                $username_ext = $this->getUsernameExtension();
                
                if (!empty($username_ext)) {
                    $user_details['username'] .= $username_ext;
                }

                $business = $request->input('business');

                DB::beginTransaction();
                if (!empty($business)) {
                    $user = null;
                    foreach ($business as $b_id) {
                        //Check if subscribed or not, then check for users quota
                        if (!$this->moduleUtil->isSubscribed($b_id)) {
                            return $this->moduleUtil->expiredResponse();
                        } elseif (!$this->moduleUtil->isQuotaAvailable('users', $b_id)) {
                            return $this->moduleUtil->quotaExpiredResponse('users', $b_id, action('ManageUserController@index'));
                        }

                        $user_details['business_id'] = $b_id;;

                        //Create the user
                        $user = User::create($user_details);
                        $role_id = $request->input('role');
                        $role = Role::findOrFail($role_id);
                        $user->assignRole($role->name);

                    }
                    /** send mail notification */
                    Mail::to($user->email)->send(new NotifyUserCreated($user, $password, 'new_user'));
                }
                DB::commit();
                return response()->json([
                    "message" => 'Success'
                ]);
            } catch(\Exception $e) {
                DB::rollBack();

                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                return response()->json([
                    "message" => 'Error'
                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('user.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $user = DB::table('users')
        ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
        ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
        ->select('users.*', DB::raw("CONCAT(COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, '')) as full_name, left(roles.name,LOCATE('#', roles.name) - 1) as rol"))
        ->where('users.business_id', $business_id)
        ->where('users.id', $id)
        ->first();
        return response()->json($user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        if (!auth()->user()->can('user.update')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $user = DB::table('users')
        ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
        ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
        ->select('users.*', 'roles.id as rol_id', DB::raw("CONCAT(COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, '')) as full_name, left(roles.name,LOCATE('#', roles.name) - 1) as rol"))
        ->where('users.business_id', $business_id)
        ->where('users.id', $id)
        ->first();

        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('user.update')) {
            abort(403, 'Unauthorized action.');
        }
        if($request->ajax())
        {
            $validateData = $request->validate
            (                
                [

                    'first_name' => 'required',
                    'email' => 'required|email',
                    'role' => 'required',
                ],
                [
                ]
            );
            try
            {
                $password_mode = $request->input('password_mode');
                $user_data = $request->only(['first_name', 'last_name', 'email']);
                $user_data['status'] = $request->input('is_active');
                $business_id = request()->session()->get('user.business_id');

                if (!empty($request->input('password')))
                {
                    if($password_mode == 'generated')
                    {
                        $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
                        $password = "";
                        for($i = 0; $i < 9; $i ++){
                            $password .= substr($str,rand(0, 61), 1);
                        }
                        $user_data['password'] = bcrypt($password);
                    }
                    else
                    {
                        $password = $request->input('password');
                        $user_data['password'] = bcrypt($password);
                    }
                }
                $user = User::where('business_id', $business_id)->findOrFail($id);
                $user->update($user_data);
                $role_id = $request->input('role');
                $user_role = $user->roles->first();
                if ($user_role->id != $role_id) {
                    $user->removeRole($user_role->name);
                    $role = Role::findOrFail($role_id);
                    $user->assignRole($role->name);
                }
                if(!empty($request->input('password'))){
                    $user->notify(new NewNotification($password));
                }
                return response()->json([
                    "message" => 'Success'
                ]);
            }
            catch(\Exception $e)
            {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                return response()->json([
                    "message" => 'Error'
                ]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('user.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()){
            try
            {
                $business_id = request()->session()->get('user.business_id');
                User::where('business_id', $business_id)
                ->where('id', $id)->delete();
                return response()->json([
                    "mensaje" => 'Success'
                ]);
            }
            catch(\Exception $e)
            {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                return response()->json([
                    "mensaje" => 'Error'
                ]);
            }
        }
    }
    
    public function changePassword(Request $request)
    {
        if (!auth()->user()->can('user.update')) {
            abort(403, 'Unauthorized action.');
        }
        if($request->ajax())
        {
            $pass = $request->input('password');
            $validateData = $request->validate
            (                
                [
                    'password' => 'required',
                    'confirm_password' => 'required|in:'.$pass,
                ],
                [
                    'confirm_password.required' => 'Confirmar contraseña',
                    'confirm_password.in' => 'Contraseñas son diferentes',
                ]
            );
            $id = $request->input('user_id');
            try
            {
                $password_mode = $request->input('password_mode');
                $user_data['status'] = 'pending';
                $business_id = request()->session()->get('user.business_id');
                if (!empty($request->input('password')))
                {
                    if($password_mode == 'generated')
                    {
                        $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
                        $new_password = "";
                        for($i = 0; $i < 9; $i ++){
                            $new_password .= substr($str, rand(0, 61), 1);
                        }
                        $user_data['password'] = bcrypt($new_password);
                    }
                    else
                    {
                        $new_password = $request->input('password');
                        $user_data['password'] = bcrypt($new_password);
                    }
                }
                $user = User::where('business_id', $business_id)->findOrFail($id);
                $user->update($user_data);
                if (!empty($request->input('password'))) {
                    Mail::to($user->email)->send(new NotifyUserCreated($user, $new_password, 'reset_pass'));
                }
                return response()->json([
                    "message" => 'Success'
                ]);
            }
            catch(\Exception $e)
            {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                return response()->json([
                    "message" => 'Error'
                ]);
            }
        }
    }
    private function getUsernameExtension()
    {
        $extension = !empty(System::getProperty('enable_business_based_username')) ? '-' .str_pad(session()->get('business.id'), 2, 0, STR_PAD_LEFT) : null;
        return $extension;
    }
}