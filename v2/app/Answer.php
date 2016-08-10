<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model {
	protected $table = "answers";
	protected $fillable = array('answer_survey_id','answer');

}
