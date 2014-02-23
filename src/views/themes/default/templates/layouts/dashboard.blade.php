@extends($view_prefix . '.layouts.master')
@section('styles')
	@parent
@stop
@section('content')
	<h1>LPress Dashboard</h1>
	<div class='message no-js'>You really should enable javascript. This dashboard relies heavily upon it and you cannot do much efficiently without it.</div>
@stop
@section('footer_scripts')
	@parent
	{{ HTML::asset('js', 'compiled/dashboard/ready.js') }}
@stop
