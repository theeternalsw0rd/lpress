@extends($view_prefix . '.layouts.dashboard')
@section('styles')
	@parent
@stop
@section('content')
	@parent
	<h2>{{ $model_basename }}: {{ $model->label }}</h2>
	{$ $name = $extra_column['name'] $}
	{$ $count = count($extra_column['ids']) $}
	@for($i=0;$i<$count;$i++)
		<h3>{{ Lang::get('l-press::labels.site') }}{{ Lang::get('l-press::labels.label_separator') }} {{ $extra_column['labels'][$i] }}</h3>
		<h4>{{ $pivot_label }}</h4>
		{{ Form::pivotables($model, $pivot_name, $pivot, $pivot_basename, array('name' => $name, 'value' => $extra_column['ids'][$i])) }}
	@endfor
@stop
@section('footer_scripts')
	@parent
@stop

