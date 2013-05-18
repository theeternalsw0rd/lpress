@extends($view_prefix . '.layouts.master')
@section('styles')
	@parent
	{{ HTML::asset('css', 'frontend/master.css') }}
@stop
@section('content')
	<h1>Hello {{ $domain }}</h1>
	{{-- Example url usage --}}
	{{ HTML::url('http://www.google.com', 'Google', array('class' => 'google')) }}
@stop
@section('footer_scripts')
	@parent
	{{ HTML::asset('js', 'frontend/main.js') }}
	{{ HTML::asset('js', 'frontend/ready/test.js') }}
@stop
