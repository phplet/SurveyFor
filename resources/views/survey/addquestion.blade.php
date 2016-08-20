@extends('layouts.master')

@section('content')

<div class="container topety">
  <div class="row">
    <div class="col-md-6">
      @if($errors->has())
        @foreach ($errors->all() as $error)
          <p class="text-danger"><em>{{ $error }}</em></p>  
        @endforeach
      @endif
      <h1>Add Question</h1>
      {{Form::open()}}
        <div class="form-group">
        @if(Session::has('message') && Session::has('class'))
          <div class="alert {{Session::get('class')}} alert-dismissable">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          {{Session::get('message')}}
          </div>
        @endif
          <label for="question">Question</label>
          {{ Form::text('question', null, array('class'=>'form-control', 'id'=>'question', 'placeholder'=>'Question')) }}
        </div>
        <div class="form-group">
          <label for="question_type">Question Type</label>
            {{ Form::select('question_type', [
             'select' => 'Select one below',
             'text' => 'Text',
             'textarea' => 'Textarea',
             'radio' => 'Radio Button',
             'checkbox' => 'Checkbox'],
             null, 
             ['class' => 'form-control', 'id' => 'question_type',]
            )}}
        </div>
        <div class="form-group form-g">
        </div>
        <div class="form-group">
          <input type="submit" class="form-control" id="submit_button">
        </div>
      {{Form::close()}}
    </div>
  </div>
</div>
@stop
