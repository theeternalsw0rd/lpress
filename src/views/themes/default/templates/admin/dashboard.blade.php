@extends($view_prefix . '.layouts.master')
@section('styles')
	@parent
@stop
@section('content')
	<h1>Welcome, {{ $user->username }} {{ HTML::linkRoute('lpress-logout', 'Logout') }}</h1>
@stop
@section('footer_scripts')
	@parent
@stop
