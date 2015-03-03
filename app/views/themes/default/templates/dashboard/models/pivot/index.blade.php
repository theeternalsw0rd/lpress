@extends($view_prefix . '.layouts.dashboard')
@section('styles')
	@parent
@stop
@section('content')
	@parent
	<h2>{{ $model_basename }}: {{ $model->label }}</h2>
	<h3>{{ $pivot_label }}</h3>
	{{ Form::pivotables($model, $pivot_name, $pivot, $pivot_basename) }}
@stop
@section('footer_scripts')
	@parent
@stop

