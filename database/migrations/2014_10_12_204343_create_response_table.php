<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResponseTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('answers', function($table){
			$table->increments('id');
			$table->integer('survey_id')->unsigned();
			$table->string('respondent')->nullable();
			$table->string('answer');
			$table->foreign('survey_id')->references('id')->on('surveys')->onDelete('cascade');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('answers');
	}
}
