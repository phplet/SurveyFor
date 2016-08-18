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
    'as'=>'default', 'uses' =>'HomeController@index'
));

Route::get('survey/thank-you/{id}', array(
    'uses'=>'SurveyController@thankyou'
));

Route::group(['middleware' => 'auth'], function () {

    Route::get('survey/create', array(
        'as'=>'createsurvey',
        'uses'=>'SurveyController@create'
    ));

    Route::post('survey/create', array(
        'uses'=>'SurveyController@insert'
    ));

    Route::get('survey/publish/{id}', array(
        'uses'=>'SurveyController@publish'
    ));

    Route::get('survey/unpublish/{id}', array(
        'uses'=>'SurveyController@unpublish'
    ));

    Route::get('survey/delete/{id}', array(
        'uses'=>'SurveyController@delete'
    ));

    Route::post('survey/add_question/{id}', array(
        'uses'=>'SurveyController@insert_question'
    ));

    Route::get('survey/add_question/{id}', array(
        'as'=>'add_question',
        'uses'=>'SurveyController@add_question'
    ));

    Route::get('users/profile/', array(
        'as'=>'profile',
        'uses'=>'UserController@profile'
    ));

    Route::get('survey/settings/{id}', array(
        'uses'=>'SurveyController@settings'
    ));
});

Route::post('survey/view/{id}', array(
    'uses'=>'SurveyController@save_survey'
));

Route::get('survey/view/{id}', array(
    'uses'=>'SurveyController@view_survey'
));
