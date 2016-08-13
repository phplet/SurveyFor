<?php

namespace App\Http\Controllers;

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
			Survey::create(array(
				'user_id' => Auth:: user()->id,
				'title' => $request->input('title'),
				'description' => $request->input('description')
			));
            return redirect()->route('profile');
		}
	}

	public function publish($id)
	{
		DB::table('surveys')
            ->where('survey_id', $id)
            ->update(array('status' => 1));

		return Redirect::route('profile')
			->with('message', 'Your Survey has been sucessfully published')
			->with('class', 'alert-success');
	}

	public function unpublish($id)
	{
		DB::table('surveys')
            ->where('survey_id', $id)
            ->update(array('status' => 2));

		return Redirect::route('profile')
			->with('message', 'Your Survey has been sucessfully Unpublished')
			->with('class', 'alert-success');
	}

	public function delete($id)
	{

		DB::table('surveys')->where('survey_id', '=', $id)->delete();
		return Redirect::route('profile')
			->with('message', 'Your Survey has been sucessfully deleted')
			->with('class', 'alert-success');
	}

	public function add_question()
	{
        return view('survey.addquestion', ['title' => 'Add question to your survey']);
	}

	public function insert_question(Request $request, $id)
	{
		$validator = Question::validate($request->all());
		if ($validator->fails()) {
			$request->flashOnly('question');
			return redirect('survey/add_question/'.$id)
				->withErrors($validator);
		}
		else{
			Question::create(array(
				'the_survey_id' => $id,
				'question' => $request->get('question'),
				'question_type' => $request->get('question_type'),
				'option_name' => json_encode($request->get('option_name'),JSON_FORCE_OBJECT)
			));
			return redirect('survey/add_question/'.$id)
				->with('message', 'Your question has been added successfully. Add another one or go back home')
				->with('class', 'alert-success');
		}
	}

	public function view_survey($id)
	{

		$survey = DB::table('surveys')
			->where('survey_id', '=', $id)
			->where('status', '=', 1)->first();
		if (count($survey) != 0) {
			$question = DB::table('questions')->where('the_survey_id', '=', $survey->survey_id)->paginate(2);
			if ( Input::get('page', 1) > $question->getLastPage() )
			{
				if (Auth::check())
				{
				    return Redirect::to('users/profile')
						->with('message', '1. incorrect link. 2. Check to be sure there are questions for this survey.')
						->with('class', 'alert-danger')
						->with('title', 'Welcome to SurveyFor');
				}else{
			    	App::abort(404);
				}
			}else{
				return View::make('survey.view')
					->with('title', $survey->title)
					->with('survey', $survey)
					->with('question', $question);
			}
		}
		else{
			return View::make('survey.view')
				->with('title', "Survey Not Found")
				->with('survey', $survey);
		}

	}

	public function thankyou($id){
		return View::make('survey.thank-you',  array(
			'title' => 'Thank you for taking the survey | Powered by surveyFor'
		));

	}

	public function save_survey($id)
	{
		$page = Input::get('page');
		$finish = Input::get('finish');
		$submitted_data = json_encode(Input::all(),JSON_FORCE_OBJECT);
		$validator = Question::validate_two(Input::all());
		$messages = $validator->messages();
		if ($validator->fails()) {
			foreach (Input::all() as $key => $value) {
				if (is_array($value)) {
					$current_key = $key;
				}
			}
			if ($finish==0) {
				if (empty($current_key)) {
					return Redirect::to('survey/view/'.$id.'?page='.$page)
					->withInput()
					->withErrors($validator);
				}else{
					return Redirect::to('survey/view/'.$id.'?page='.$page)
					->withErrors($validator);
				}
				
			}else{
				if (empty($current_key)) {
					return Redirect::to('survey/view/'.$id.'?page='.$page)
					->withInput()
					->withErrors($validator);
				}else{
					return Redirect::to('survey/view/'.$id.'?page='.$page)
					->withErrors($validator);
				}
				
			}
		}else{
			$previous_response = DB::table('answers')
				->where('answer_survey_id', '=', $id)->first();
			if (count($previous_response) != 0) {
				//$response_value = array();
				$response_value = json_decode($previous_response->answer, true);
				$merge = array_merge($response_value, Input::except('_token','page','finish'));
				DB::table('answers')->where('answer_survey_id', '=', $id)->update(array('answer' => json_encode($merge,JSON_FORCE_OBJECT)));
			}else{
				Answer::create(array(
					'answer_survey_id' => $id,
					'answer' => json_encode(Input::except('_token','page','finish'),JSON_FORCE_OBJECT)
				));
			}
			if ($finish==0) {
				return Redirect::to('survey/view/'.$id.'?page='.($page+1));

			}else{
				return Redirect::to('survey/thank-you/'.$id);
			}
		}
		
	}

	public function settings($id)
	{
		$survey = Survey::find($id);
		$question = DB::table('questions')
			->where('the_survey_id', '=', $id)->get();
		$answer = DB::table('answers')
			->where('answer_survey_id', '=', $id)->get();
		return view('survey.settings')
			->with(['title' => 'Settings', 'question' => $question, 'answer' => $answer, 'survey' => $survey]);
		
	}

}
