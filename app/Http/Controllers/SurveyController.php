<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Question;
use App\Survey;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Http\Request;

class SurveyController extends BaseController {

	public function create()
	{
        return view('survey.create', ['title' => 'Create new Survey']);
	}

	public function insert(Request $request)
	{

        $validator = Validator::make($request->all(), [
            'title' => 'required|max:60',
            'description' => 'required|max:300'
        ]);

		if ($validator->fails()) {
		    return redirect()->route('createsurvey')
				->withErrors($validator)
				->withInput();
		}
		else{
		    Auth::user()->surveys()->save(new Survey([
                'title' => $request->input('title'),
                'description' => $request->input('description')
            ]));
            return redirect()->route('profile');
		}
	}

	public function publish($id)
	{
		$survey = Survey::find($id);
        $survey->status = 1;

        if ($survey->save()) {
            return redirect()->route('profile')
                ->with('message', 'Your Survey has been successfully published')
                ->with('class', 'alert-success');
        }else {
            return redirect()->route('profile')
                ->with('message', 'An error occurred. Survey not published.')
                ->with('class', 'alert-error');
        }
	}

	public function unpublish($id)
	{
        $survey = Survey::find($id);
        $survey->status = 2;

        if ($survey->save()) {
            return redirect()->route('profile')
                ->with('message', 'Your Survey has been successfully Unpublished')
                ->with('class', 'alert-success');
        }else {
            return redirect()->route('profile')
                ->with('message', 'An error occurred. Survey not unpublished.')
                ->with('class', 'alert-error');
        }
	}

	public function delete($id)
	{
        if(Survey::destroy($id)) {
            return redirect()->route('profile')
                ->with('message', 'Your Survey has been successfully deleted')
                ->with('class', 'alert-success');
        }else {
            return redirect()->route('profile')
                ->with('message', 'An error occurred. Survey not deleted.')
                ->with('class', 'alert-error');
        }
	}

	public function add_question()
	{
        return view('survey.addquestion', ['title' => 'Add question to your survey']);
	}

	public function insert_question(Request $request, $id)
	{
        $survey = Survey::find($id);
		$validator = Question::validate($request->all());
		if ($validator->fails()) {
			$request->flashOnly('question');
			return redirect('survey/add_question/'.$id)
				->withErrors($validator);
		}
		else{
            $survey->questions()->save(new Question([
                'question' => $request->get('question'),
                'question_type' => $request->get('question_type'),
                'option_name' => json_encode($request->get('option_name'),JSON_FORCE_OBJECT)
            ]));
			return redirect('survey/add_question/'.$id)
				->with('message', 'Your question has been added successfully. Add another one or go back home')
				->with('class', 'alert-success');
		}
	}

	public function view_survey(Request $request, $id)
	{
        $survey = Survey::where('id', '=', $id)
            ->where('status', '=', 1)->first();
		if (count($survey) != 0) {
            $question = $survey->questions()->paginate(1);
			if ( $request->get('page', 1) > $question->lastPage() )
			{
				if (Auth::check())
				{
				    return redirect('users/profile')
						->with('message', '1. incorrect link. 2. Check to be sure there are questions for this survey.')
						->with('class', 'alert-danger')
						->with('title', 'Welcome to SurveyFor');
				}else{
			    	App::abort(404);
				}
			}else{
				return view('survey.view')
					->with('title', $survey->title)
					->with('survey', $survey)
					->with('question', $question);
			}
		}
		else{
			return view('survey.view')
				->with('title', "Survey Not Found")
				->with('survey', $survey);
		}

	}

	public function thankyou($id){
		return view('survey.thank-you',  array(
			'title' => 'Thank you for taking the survey | Powered by surveyFor'
		));

	}

	public function save_survey(Request $request, $id)
	{
		$page = $request->get('page');
		$finish = $request->get('finish');
		$validator = Question::validate_two($request->all());
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
			$previous_response = $survey->answers()->first();
            $new_response = $request->except('_token','page','finish');
			if (count($previous_response) != 0) {
				$response_value = json_decode($previous_response->answer, true);
				$updated_response = array_merge($response_value, $new_response);
                $previous_response->answer = json_encode($updated_response,JSON_FORCE_OBJECT);
                $previous_response->save();
			}else{
			    $survey->answers()->save(new Answer(['answer' => json_encode($new_response,JSON_FORCE_OBJECT)]));
			}
			if ($finish==0) {
				return redirect('survey/view/'.$id.'?page='.($page+1));

			}else{
				return redirect('survey/thank-you/'.$id);
			}
		}
		
	}

	public function settings($id)
	{
		$survey = Survey::find($id);
		return view('survey.settings')
			->with(['title' => 'Settings', 'survey' => $survey]);
		
	}

}
