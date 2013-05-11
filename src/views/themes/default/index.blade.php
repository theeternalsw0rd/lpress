@extends($view_prefix . '.layouts.master')
@section('content')
	<h1>Hello {{ $domain }}</h1>
	{{-- Example url usage --}}
	{{ HTML::url('http://www.google.com', 'Google', array('class' => 'google')) }}
@stop
