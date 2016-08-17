<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model {
	protected $table = "answers";
	protected $fillable = array('answer');

    public function survey()
    {
        return $this->belongsTo('App\Survey');
    }

}
