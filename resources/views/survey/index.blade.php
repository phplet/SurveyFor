@extends('layouts.master')

@section('content')

    <div class="container topety">
        <div class="row">
            <div class="col-md-3">
                <div class="panel" id="profile-nav">
                    {{ link_to('survey/create', 'Create New', array('class'=>'btn btn-primary btn-lg', 'role'=>'button', 'style'=>'width: 92%;margin: 4%;')) }}
                    <div class="panel-body no-padding">
                        <ul class="nav nav-pills nav-stacked mail-nav no-rad">
                            <li><a href="#">All <span class="badge pull-right">{{ $counts->get('all') }}</span></a></li>
                            <li><a href="#">Draft <span class="badge pull-right">{{ $counts->get('notpublished') }}</span></a></li>
                            <li><a href="#">Published <span class="badge pull-right">{{ $counts->get('published') }}</span></a></li>
                            <li><a href="#" style="margin-bottom: 2px;">Unpublished <span class="badge pull-right">{{ $counts->get('unpublished') }}</span></a></li>
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
                                <button type="button" class="close" data-dismiss="alert"
                                        aria-hidden="true">&times;</button>
                                {{Session::get('message')}}
                            </div>
                        @endif
                        @foreach($surveys as $survey)
                            <div class="well">
                                <h4><a target="_blank"
                                       href="/survey/settings/{{ $survey->id }}">{{ $survey->title }}</a></h4>
                                <p>{{ $survey->description }}</p>
                                <hr class="hr"/>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default"><a
                                                href="/survey/view/{{ $survey->id }}" target="_blank">Preview</a></button>
                                    @if ($survey->status === 1)
                                        <button type="button" class="btn btn-default"><a
                                                    href="/survey/{{ $survey->id }}/un-publish">Un-Publish</a>
                                        </button>
                                    @elseif ($survey->status === 0)
                                        <button type="button" class="btn btn-default"><a
                                                    href="/survey/{{ $survey->id }}/publish">Publish</a></button>
                                    @else
                                    @endif
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">More Actions<span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="#">Question
                                                <span class="badge pull-right">{{$survey->questions->count()}}</span>
                                            </a>
                                        </li>
                                        @if ($survey->status > 0)
                                            <li><a href="#">Taken<span class="badge pull-right">0</span></a></li>
                                        @endif
                                        <li class="divider"></li>
                                        <li>
                                            {{Form::open(array('url' => 'survey/'.$survey->id, 'method' => 'post'))}}
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="submit" value="Delete Survey" style="margin: 0px 14px;background: transparent;border: none;">
                                            {{Form::close()}}
                                        </li>
                                        @if ($survey->status === 0)
                                            <li><a href="/survey/add_question/{{ $survey->id }}">Add Question</a>
                                            </li>
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