<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::auth();

Route::pattern('id', '[0-9]+');

Route::get('/', array(
    'as'=>'default', 'uses'=>'SurveyController@index'
));

Route::post('survey/add_question/{id}', array(
    'uses'=>'SurveyController@insert_question'
));

Route::get('survey/add_question/{id}', array(
    'as'=>'add_question',
    'uses'=>'SurveyController@add_question'
));

Route::post('survey/view/{id}', array(
    'uses'=>'SurveyController@save_survey'
));

Route::get('survey/view/{id}', array(
    'uses'=>'SurveyController@view_survey'
));

Route::get('survey/{survey}/publish', array(
    'uses'=>'SurveyController@publish'
));

Route::get('survey/{survey}/un-publish', array(
    'uses'=>'SurveyController@unPublish'
));

Route::get('survey/{survey}/thank-you', array(
    'uses'=>'SurveyController@completed'
));

Route::get('survey/{survey}/results', array(
    'uses'=>'SurveyController@results'
));

Route::resource('survey', 'SurveyController');
Route::resource('survey.question', 'QuestionController');