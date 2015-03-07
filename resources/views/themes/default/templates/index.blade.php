@extends($view_prefix . '.layouts.master')
@section('styles')
	@parent
@stop
@section('content')
	@parent
	<h1>Hello {{ $domain }}</h1>
	{{-- Example url usage --}}
	{!! HTML::url('http://www.google.com', 'Google', array('class' => 'google')) !!}
@stop
@section('footer_scripts')
	@parent
@stop
