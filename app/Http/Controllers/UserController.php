<?php

namespace App\Http\Controllers;

use App\Survey;
use Illuminate\Support\Facades\Auth;

class UserController extends BaseController {

	public function profile()
	{
        $surveys = Survey::where('user_id', '=', Auth::user()->id)->with('questions')->get();
        $group_by_status = $surveys->groupBy('status');
        $counts = collect([
            "all" => $surveys->count(),
            "published" => ($group_by_status->get(1)) ? $group_by_status->get(1)->count() : 0,
            "unpublished" => ($group_by_status->get(2)) ? $group_by_status->get(2)->count() : 0,
            "notpublished" => ($group_by_status->get(0)) ? $group_by_status->get(0)->count() : 0
        ]);

        return view('users.profile', compact('surveys', 'counts'));
	}

}
