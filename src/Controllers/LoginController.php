<?php

namespace Amet\SimpleAdminAPI\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
* Login Controller
*/
class LoginController extends Controller
{
	protected $redirect = '/simple_admin/dashboard';




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

		return redirect('simple_admin/login');
	}

	protected function postLogin(Request $request)
	{
		$remember = $request->has('rememberme') ? true : false;
		if (\Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect()->intended($this->redirect);
        } else {
        	return back();
        }
	}
	
}