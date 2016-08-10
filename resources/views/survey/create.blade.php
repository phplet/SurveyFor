@extends('layouts.master')

@section('body_content')

<div class="container topety">
  <div class="row">
    <div class="col-md-12">
      <!--breadcrumbs start -->
      <ul class="breadcrumb">
          <li><a href="/">Home</a></li>
          <li class="active">Create Survey</li>
      </ul>
      <!--breadcrumbs end -->
    </div>
    <div class="col-md-6">
      <h1>Create Survey</h1>
      {{Form::open(array('url' => 'survey/create', 'method' => 'post'));}}
        <div class="form-group">
          <label for="title">Survey Title</label>
          {{ Form::text('title', null, array('class'=>'form-control', 'id'=>'title', 'placeholder'=>'Survey Title')) }}
          @if($errors->has())
          <p class="text-danger"><em>{{ $errors->first('title', ':message') }}</em></p>
          @endif
        </div>
        <div class="form-group">
          <label for="description">Description</label>
          {{ Form::textarea('description', null, array('class'=>'form-control', 'id'=>'description', 'placeholder'=>'Enter Description')) }}
           @if($errors->has())
          <p class="text-danger"><em>{{ $errors->first('description', ':message') }}</em></p>
          @endif
        </div>
        <div class="form-group">
          <input type="submit" class="form-control" id="submit_button">
        </div>
      {{Form::close();}}
    </div>
  </div>
</div>
@stop