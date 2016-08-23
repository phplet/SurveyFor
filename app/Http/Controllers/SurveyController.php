<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Question;
use App\Survey;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Validator;
use Illuminate\Http\Request;

class SurveyController extends BaseController {

    /**
     * Instantiate a new SurveyController instance.
     *
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => [
            'save_survey',
            'view_survey',
            'thankyou'
        ]]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $surveys = Survey::where("user_id", "=", Auth::user()->id)->with("questions")->get();
        $group_by_status = $surveys->groupBy("status");
        $counts = collect([
            "all" => $surveys->count(),
            "published" => ($group_by_status->get(1)) ? $group_by_status->get(1)->count() : 0,
            "unpublished" => ($group_by_status->get(2)) ? $group_by_status->get(2)->count() : 0,
            "notpublished" => ($group_by_status->get(0)) ? $group_by_status->get(0)->count() : 0
        ]);

        return view("survey.index", compact("surveys", "counts"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view("survey.create", ["title" => "Create new Survey"]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            "title" => "required|max:60",
            "description" => "required|max:300"
        ]);

        if ($validator->fails()) {
            return redirect()->route("survey.create")
                ->withErrors($validator)
                ->withInput();
        }
        else{
            Auth::user()->surveys()->save(new Survey([
                "title" => $request->input("title"),
                "description" => $request->input("description")
            ]));
            return redirect()->route("survey.index");
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param $survey
     * @return \Illuminate\Http\Response
     */
    public function destroy($survey)
    {
        //
        if(Survey::destroy($survey)) {
            return redirect()->route("survey.index")
                ->with([
                    "message" => "Your Survey has been successfully deleted.",
                    "class" => "alert-success"
                ]);
        }else {
            return redirect()->route("survey.index")
                ->with([
                    "message" => "An error occurred. Survey not deleted.",
                    "class" => "alert-error"
                ]);
        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function add_question()
	{
        return view("survey.addquestion", ["title" => "Add question to your survey."]);
	}

    /**
     * @param Request $request
     * @param $id
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function insert_question(Request $request, $id)
	{
        $survey = Survey::find($id);
		$validator = Question::createQuestionValidator($request->all());
		if ($validator->fails()) {
			$request->flashOnly("question");
			return redirect("survey/add_question/".$id)
				->withErrors($validator);
		}
		else{
            $survey->questions()->save(new Question([
                "question" => $request->get("question"),
                "question_type" => $request->get("question_type"),
                "option_name" => json_encode($request->get("option_name"),JSON_FORCE_OBJECT)
            ]));
			return redirect('survey/add_question/'.$id)
				->with([
				    "message" => "Your question has been added successfully. Add another one or go back home",
                    "class" => "alert-success"
                ]);
		}
	}

    /**
     * @param Request $request
     * @param $id
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function view_survey(Request $request, $id)
	{
	    $status_compare = "=";
	    if(Auth::check())
	        $status_compare = "!=";

        $survey = Survey::where('id', '=', $id)
            ->where('status', $status_compare, 1)->first();
		if (count($survey) != 0) {
            $question = $survey->questions()->paginate(1);
			if ( $request->get('page', 1) > $question->lastPage() )
			{
				if (Auth::check())
				{
				    return redirect()->route('surveys')
						->with([
                            'title' => 'Welcome to SurveyFor',
						    'message' => '1. incorrect link. 2. Check to be sure there are questions for this survey.',
                            'class', 'alert-danger'
                        ]);
				}else{
			    	App::abort(404);
				}
			}else{
			    if (!$request->cookie('survey_for_view_user')) {
                    Cookie::queue(Cookie::make('survey_for_view_user', md5(uniqid(rand(), true)), 1440));
                }
				return view('survey.view')
					->with([
					    "title" => $survey->title,
                        "survey" => $survey,
                        "question" =>$question
                    ]);
			}
		}
		else{
			return view('survey.view')
				->with([
				    "title" => "Survey Not Found",
                    "survey" => $survey
                ]);
		}

	}

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function save_survey(Request $request, $id)
	{
		$page = $request->get('page');
		$finish = $request->get('finish');
		$validator = Question::answerQuestionValidator($request->all());
		if ($validator->fails()) {
			foreach ($request->all() as $key => $value) {
				if (is_array($value)) {
					$current_key = $key;
				}
			}
			if ($finish==0) {
				if (empty($current_key)) {
					return redirect('survey/view/'.$id.'?page='.$page)
					->withInput()
					->withErrors($validator);
				}else{
					return redirect('survey/view/'.$id.'?page='.$page)
					->withErrors($validator);
				}
				
			}else{
				if (empty($current_key)) {
					return redirect('survey/view/'.$id.'?page='.$page)
					->withInput()
					->withErrors($validator);
				}else{
					return redirect('survey/view/'.$id.'?page='.$page)
					->withErrors($validator);
				}
				
			}
		}else{
		    $survey = Survey::find($id);

            // Only saves published surveys
            if ($survey->status == 1) {
                $previous_response = $survey->answers()->where('respondent', $request->cookie('survey_for_view_user'))->first();
                $new_response = $request->except('_token', 'page', 'finish');
                if (count($previous_response) != 0) {
                    $response_value = json_decode($previous_response->answer, true);
                    $updated_response = array_merge($response_value, $new_response);
                    $previous_response->answer = json_encode($updated_response, JSON_FORCE_OBJECT);
                    $previous_response->save();
                } else {
                    $survey->answers()->save(new Answer([
                        'answer' => json_encode($new_response, JSON_FORCE_OBJECT),
                        'respondent' => $request->cookie('survey_for_view_user')
                    ]));
                }
            }

			if ($finish==0) {
				return redirect('survey/view/'.$id.'?page='.($page+1));

			}else{
                Cookie::queue(Cookie::forget('survey_for_view_user'));
				return redirect('survey/'.$id.'/thank-you');
			}
		}
		
	}


    /**
     * @param $survey
     * @return \Illuminate\Http\RedirectResponse
     */
    public function publish($survey)
    {
        $survey = Survey::find($survey);
        $survey->status = 1;

        if ($survey->save()) {
            return redirect()->route("survey.index")
                ->with([
                    "message" => "Your Survey has been successfully published.",
                    "class" => "alert-success"
                ]);
        }else {
            return redirect()->route("survey.index")
                ->with([
                    "message" => "An error occurred. Survey not published.",
                    "class" => "alert-error"
                ]);
        }
    }

    /**
     * @param $survey
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unPublish($survey)
    {
        $survey = Survey::find($survey);
        $survey->status = 2;

        if ($survey->save()) {
            return redirect()->route("survey.index")
                ->with([
                    "message" => "Your Survey has been successfully Unpublished.",
                    "class" => "alert-success"
                ]);
        }else {
            return redirect()->route("survey.index")
                ->with([
                    "message" => "An error occurred. Survey not unpublished.",
                    "class" => "alert-error"
                ]);
        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function completed(){
        return view("survey.thank-you",  ["title" => "Thank you for taking the survey | Powered by surveyFor"]);
    }


    /**
     * @param Survey $survey
     * @return $this
     */
    public function results(Survey $survey)
    {
        return view('survey.results')
            ->with(['title' => $survey->title . ' Results', 'survey' => $survey]);
    }

}
