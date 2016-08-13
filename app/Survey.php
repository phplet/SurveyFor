<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model {

	protected $table = "surveys";
    protected $primaryKey = "survey_id";
	protected $fillable = array('user_id','title', 'description', 'status');

    public function questions()
    {
        return $this->hasMany('App\Question', 'question_id', 'survey_id');
    }

}
