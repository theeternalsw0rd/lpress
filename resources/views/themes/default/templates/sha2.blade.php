@extends($view_prefix . '.layouts.master')
@section('styles')
	@parent
@stop
@section('content')
	<h1>{{ $title }}</h1>
	<div class='error'>
		<p>{{ Lang::get('l-press::errors.xpSha2') }}</p>
	</div>

@stop
@section('footer_scripts')
	@parent
@stop
