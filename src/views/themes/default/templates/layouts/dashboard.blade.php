@extends($view_prefix . '.layouts.master')
@section('styles')
	@parent
@stop
@section('content')
	@parent
	<h1>{{ Lang::get('l-press::headers.dashboard') }}</h1>
@stop
@section('footer_scripts')
	@parent
	{{ HTML::asset('js', 'compiled/dashboard/ready.js') }}
@stop
