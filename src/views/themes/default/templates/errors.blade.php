@extends($view_prefix . '.layouts.master')
@section('styles')
	@parent
@stop
@section('content')
	<h1>HttpError: {{ $status_code }}</h1>
	<div class='error'>{{ $message }}</div>
@stop
@section('footer_scripts')
	@parent
@stop
