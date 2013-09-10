@extends($view_prefix . '.layouts.master')
@section('styles')
	@parent
@stop
@section('content')
	<h1>{{ $label }}</h1>
	@if (count($record_type->records) > 0)
		@foreach ($record_type->records as $record)
			@include($view_prefix . '.subsections.record', array('record' => $record))
		@endforeach
	@else
		<div class='message'>
			<p>No records found in this collection.</p>
		</div>
	@endif
@stop
@section('footer_scripts')
	@parent
@stop
