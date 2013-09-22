@extends($view_prefix . '.layouts.master')
@section('styles')
	@parent
@stop
@section('content')
	<h1>{{ $label }}</h1>
	@if (count($record_type->records) > 0)
		<ul id='gallery'>
			@foreach ($record_type->records as $record)
				<li>
					<a class='gallery' title='{{ $record->label }}' href='/{{ $path }}/{{ $record->slug }}'>
						<img src='/{{ $path }}/{{ $record->slug }}?v{{ strtotime($record->updated_at) }}' alt='{{ HTML::imageAlt($record) }}' />
						<span class='caption'>{{ $record->label }}</span>{{ ''; break }}
					</a>
				</li>
			@endforeach
		</ul>
	@else
		<div class='message'>
			<p>No records found in this collection.</p>
		</div>
	@endif
@stop
@section('footer_scripts')
	@parent
@stop
