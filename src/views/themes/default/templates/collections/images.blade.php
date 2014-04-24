@extends($view_prefix . '.layouts.master')
@section('styles')
	@parent
@stop
@section('content')
	@parent
	@if (count($record_type->children) > 0)
		<h1>{{ Lang::get('l-press::headers.collection', array('label' => $label)) }}</h1>
		<div id='type-children' class='dropdown'>
			<ul class='dropdown-menu types'>
				<li class='hide'>{{ Lang::get('l-press::headers.subcollection') }}</li>
				@foreach ($record_type->children as $child)
					<li>
						<a title='{{ $child->label}}' href='/{{ $path }}/{{ $child->slug }}'>{{ $child->label }}</a>
					</li>
				@endforeach
			</ul>
		</div>
	@else
		<h1>{{ $label }}</h1>
	@endif
	@if (count($record_type->records) > 0)
		<ul id='gallery'>
			@foreach ($record_type->records as $record)
				<li>
					<a class='gallery' title='{{ $record->label }}' href='/{{ $path }}/{{ $record->slug }}?v{{ strtotime($record->updated_at) }}'>
						<img src='/{{ $path }}/{{ $record->slug }}?v{{ strtotime($record->updated_at) }}' alt='{{ HTML::image_alt($record) }}' />
						<span class='caption'>{{ $record->label }}</span>
					</a>
				</li>
			@endforeach
		</ul>
	@else
		<div class='message'>
			<p>{{ Lang::get('l-press::messages.empty_collection') }}</p>
		</div>
	@endif
@stop
