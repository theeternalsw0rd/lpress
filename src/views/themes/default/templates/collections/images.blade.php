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
						<img src='/{{ $path }}/{{ $record->slug }}' />
						@foreach ($record->values as $value)
							@if ($value->field->slug == 'file-description')
								<span class='caption'>{{ $value->current_revision->contents }}</span>{{ ''; break }}
							@endif
						@endforeach
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
