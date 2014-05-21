@extends($view_prefix . '.layouts.dashboard')
@section('styles')
	@parent
@stop
@section('content')
	@parent
	<h2>{{ $model_basename }}: {{ $model->label }}</h2>
	<h3>{{ $pivot_name }}</h3>
	{{ Form::pivot_form($model, $pivot, Request::url()) }}
@stop
@section('footer_scripts')
	@parent
@stop
