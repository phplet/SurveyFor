@extends('layouts.master')

@section('body_content')

<div class="container topety">
  <div class="row">
  <div class="col-md-12">
      <!--breadcrumbs start -->
      <ul class="breadcrumb">
          <li class="active">Home</li>
          <li>{{ link_to('survey/create', 'Create Survey') }}</li>
      </ul>
      <!--breadcrumbs end -->
    </div>
    <div class="col-md-3">
      <div class="panel" id="profile-nav">
      {{ link_to('survey/create', 'Create New', array('class'=>'btn btn-primary btn-lg', 'role'=>'button', 'style'=>'width: 92%;margin: 4%;')) }}
        <div class="panel-body no-padding">
          <ul class="nav nav-pills nav-stacked mail-nav no-rad">
              <li><a href="#">All <span class="badge pull-right">{{ $surveys_count_all }}</span></a></li>
              <li><a href="#">Draft <span class="badge pull-right">{{ $surveys_count_notpublished }}</span></a></li>
              <li><a href="#">Published <span class="badge pull-right">{{ $surveys_count_published }}</span></a></li>
              <li><a href="#" style="margin-bottom: 2px;">Unpublished <span class="badge pull-right">{{ $surveys_count_unpublished }}</span></a></li>
          </ul>
      </div>
    </div>
    </div>
    <div class="col-md-9">
      <div class="panel panel-default survey-border min-set" id="recent-survey">
         <div class="panel-heading">Survey List</div>
         <div class="panel-body">
             @if(Session::has('message') && Session::has('class'))
              <div class="alert {{Session::get('class')}} alert-dismissable">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              {{Session::get('message')}}
              </div>
             @endif
             @foreach($surveys_list as $surveys_list)
              <div class="well">
                <h4><a target="_blank" href="/survey/settings/{{ $surveys_list->survey_id }}">{{ $surveys_list->title }}</a></h4>
                <p>{{ $surveys_list->description }}</p>
                <hr class="hr" /> 
                <div class="btn-group">
                  <button type="button" class="btn btn-default"><a href="/survey/view/{{ $surveys_list->survey_id }}" target="_blank">View in Browser</a></button>
                  @if ($surveys_list->status === 1)
                      <button type="button" class="btn btn-default"><a href="/survey/unpublish/{{ $surveys_list->survey_id }}">Unpublish</a></button>
                  @elseif ($surveys_list->status === 0)
                      <button type="button" class="btn btn-default"><a href="/survey/publish/{{ $surveys_list->survey_id }}">publish</a></button>
                  @else
                  @endif
                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    More Actions
                    <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a href="#">Question<span class="badge pull-right">{{$survey_count_question = DB::table('questions')->where('the_survey_id', '=', $surveys_list->survey_id)->count()}}</span></a></li>
                    @if ($surveys_list->status ===1 || $surveys_list->status ===2)
                      <li><a href="#">Taken<span class="badge pull-right">6</span></a></li>
                    @endif
                    <li class="divider"></li>
                      <li><a href="/survey/delete/{{ $surveys_list->survey_id }}">Delete Survey</a></li>
                    @if ($surveys_list->status === 0)
                      <li><a href="/survey/add_question/{{ $surveys_list->survey_id }}">Add Question</a></li>
                    @endif
                  </ul>
                </div>
              </div>
            @endforeach
        </div>
      </div>
    </div>
  </div>
</div>
@stop

@section('footer')
@stop