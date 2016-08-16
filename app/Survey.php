<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model {

	protected $table = "surveys";
	protected $fillable = array('title', 'description', 'status');

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function questions()
    {
        return $this->hasMany('App\Question');
    }

}
