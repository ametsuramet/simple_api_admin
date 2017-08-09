<?php

namespace Amet\SimpleAdminAPI\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
* Login Controller
*/
class LoginController extends Controller
{
	protected $redirect;




	protected function getLogin()
	{
		return view('simple_admin_api::login');
	}

	protected function getRegistration()
	{
		return view('simple_admin_api::registration');
	}

	protected function getForgot()
	{
		return view('simple_admin_api::forgot');
	}


	protected function getLogout()
	{
		\Auth::logout();
		\Session::flush();

		return redirect(env('APP_ADMIN_PREFIX','simple_admin').'/login');
	}

	protected function postLogin(Request $request)
	{
		$remember = $request->has('rememberme') ? true : false;
		if (\Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
			if (!$this->redirect) {
				$this->redirect = env('APP_ADMIN_PREFIX','simple_admin').'/dashboard';
			}
            return redirect()->intended($this->redirect);
        } else {
        	return back()->withErrors('Login Failed, please check your credential');
        }
	}

	protected function postRegistration(Request $request)
	{
		
	    $rules = [
	        'name' => 'required',
	        'email' => 'required|email|unique:users,email',
	        'password' => 'required|min:6|confirmed',
        	'password_confirmation' => 'required|min:6'
	    ];

	     $validator = \Validator::make($request->all(), $rules);
    

	    if ($validator->passes()) {
	    	$input = $request->all();
	    	$input['password'] = bcrypt($request->password);
	        \App\User::create($input);
	        return redirect(env('APP_ADMIN_PREFIX','simple_admin').'/login');
	    }
		    
		return back()->withErrors($validator)->withInput();

	}
	
}