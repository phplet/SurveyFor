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
            "published" => $group_by_status->get(1)->count(),
            "unpublished" => $group_by_status->get(2)->count(),
            "notpublished" => $group_by_status->get(0)->count()
        ]);

        return view('users.profile', compact('surveys', 'counts'));
	}

}
