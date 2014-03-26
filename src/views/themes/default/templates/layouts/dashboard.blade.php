@extends($view_prefix . '.layouts.master')
@section('styles')
	@parent
@stop
@section('content')
	<h1>{{ Lang::get('l-press::headers.dashboard') }}</h1>
	<div class='message no-js'>{{ Lang::get('l-press::messages.noJS') }}</div>
@stop
@section('footer_scripts')
	@parent
	{{ HTML::asset('js', 'compiled/dashboard/ready.js') }}
@stop
