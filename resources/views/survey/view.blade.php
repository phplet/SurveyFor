@extends('layouts.master')

@section('content')
<div class="container topety">
  <div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
          @if(count($survey) == 0)
            <div class="panel-heading">
              <h3 class="panel-title">{{$title}}</h3>
            </div>
            <div class="panel-body">
                
            </div>
          @else
            <div class="panel-heading">
              <div class="row">
                <div class="col-md-9">
                    <h3 class="panel-title">{{$survey->title}}</h3>
                </div>
                <div class="col-md-3">
                  <h3 class="like-panel">Page {{$question->currentPage()}} of {{$question->lastPage()}}</h3>
                </div>
              </div>
            </div>
            <div class="panel-body">
              @if(Session::has('message') && Session::has('class'))
                <div class="alert {{Session::get('class')}} alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                {{Session::get('message')}}
                </div>
              @endif 
              {{Form::open()}}
                @foreach ($question as $questions)
                  <div class="well">
                    <div class="form-group">
                      {{Form::label('question'.$questions->id, $questions->question)}}
                        @if ($questions->question_type === "text")
                          {{ Form::text('question'.$questions->id, null, array('class' => 'form-control')) }}
                        @elseif ($questions->question_type === "textarea")
                          {{ Form::textarea('question'.$questions->id, null, array('class' => 'form-control')) }}
                        @elseif ($questions->question_type === "checkbox")
                          <?php $theoption = json_decode($questions->option_name); ?>
                          {{ Form::hidden('question'.$questions->id) }}
                          @foreach ($theoption as $key => $value)
                            <div class="checkbox">
                              <label>
                                {{ Form::checkbox('question'.$questions->id.'[]', $value) }}
                                {{$value}}
                              </label>
                            </div>
                          @endforeach                        
                        @else
                          <?php $theoption = json_decode($questions->option_name); ?>
                          {{ Form::hidden('question'.$questions->id) }}
                          @foreach ($theoption as $key => $value)
                            <div class="radio">
                              <label>
                                {{ Form::radio('question'.$questions->id, $value) }}
                                {{$value}}
                              </label>
                            </div>
                          @endforeach
                        @endif
                    </div>
                    @if($errors->has())
                    <p class="text-danger"><em>{{ $errors->first('question'.$questions->id, ':message') }}</em></p>
                    @endif
                  </div>
                @endforeach
                @if($question->currentPage() == $question->lastPage())
                  {{ Form::hidden('finish','1') }}
                  {{ Form::submit('Finish', array('class' => 'btn btn-default')) }}
                @else
                  {{ Form::hidden('finish','0') }}
                  {{ Form::submit('Save', array('class' => 'btn btn-default')) }}
                @endif
                {{ Form::hidden('page',$question->currentPage()) }}
              {{Form::close()}}
          @endif
        </div>
    </div>
  </div>
  </div>
</div>
@stop



