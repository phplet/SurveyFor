<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends BaseController {

	public function profile()
	{	
		$survey_list = DB::table('surveys')->where('user_id', '=', Auth::user()->id)->get();
		$survey_count_all = DB::table('surveys')->count();
		$survey_count_notpublished = DB::table('surveys')->where('status', '=', 0)->count();
		$survey_count_published = DB::table('surveys')->where('status', '=', 1)->count();
		$survey_count_unpublished = DB::table('surveys')->where('status', '=', 2)->count();
        return view('users.profile', compact('survey_list', 'survey_count_all', 'survey_count_notpublished', 'survey_count_published', 'survey_count_unpublished'));
	}

}
