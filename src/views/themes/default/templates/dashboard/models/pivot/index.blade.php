@extends($view_prefix . '.layouts.dashboard')
@section('styles')
	@parent
@stop
@section('content')
	@parent
	<h2>{{ $model_basename }}: {{ $model->label }}</h2>
	<h3>{{ $pivot_name }}</h3>
	{{ HTML::pivot_editor($model, $pivot, Request::url()) }}
@stop
@section('footer_scripts')
	@parent
@stop

