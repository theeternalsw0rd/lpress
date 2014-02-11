@extends($view_prefix . '.layouts.master')
@section('styles')
	@parent
@stop
@section('content')
	<h1>LPress Dashboard</h1>
@stop
@section('footer_scripts')
	@parent
	{{ HTML::asset('js', 'compiled/dashboard/ready.js') }}
@stop
