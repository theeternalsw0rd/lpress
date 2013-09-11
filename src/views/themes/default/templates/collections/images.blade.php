@extends($view_prefix . '.layouts.master')
@section('styles')
	@parent
@stop
@section('content')
	<h1>{{ $label }}</h1>
	@if (count($record_type->records) > 0)
		<div id='gallery'>
			@foreach ($record_type->records as $record)
				<a class='gallery' href='/{{ $path }}/{{ $record->slug }}'><img src='/{{ $path }}/{{ $record->slug }}' /></a>
			@endforeach
		</div>
	@else
		<div class='message'>
			<p>No records found in this collection.</p>
		</div>
	@endif
@stop
@section('footer_scripts')
	@parent
@stop
