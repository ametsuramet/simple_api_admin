<?php

namespace Amet\SimpleAdminAPI\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
* Login Controller
*/
class DashboardController extends Controller
{
	public function index()
	{
		return view('simple_admin_api::templates.dashboard');
	}
}