@extends($view_prefix . '.layouts.dashboard')
@section('styles')
	@parent
@stop
@section('content')
	@parent
	<h2>{{ $model_basename }}: {{ $model->label }}</h2>
	{!! Form::model_form($model, Request::url()) !!}
@stop
@section('footer_scripts')
	@parent
@stop
