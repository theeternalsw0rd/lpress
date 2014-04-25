@extends($view_prefix . '.layouts.master')
@section('styles')
	@parent
@stop
@section('content')
	@parent
	@if (count($record_type->children) > 0)
		<h1>{{ Lang::get('l-press::headers.collection', array('label' => $label)) }}</h1>
		<div id='type-children' class='dropdown'>
			{$ $descendents = $record_type->getDescendents() $}
			<ul class='dropdown-menu types'>
				<li class='hide'>{{ Lang::get('l-press::headers.subcollections') }}</li>
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
	@if (count($records) > 0)
		<ul id='gallery'>
			@foreach ($records as $record)
				{$ $current_path = $record->getPath($record_type, $path) $}
				<li>
					<a class='gallery' title='{{ $record->label }}' href='/{{ $current_path }}/{{ $record->slug }}?v{{ strtotime($record->updated_at) }}'>
						<img src='/{{ $current_path }}/{{ $record->slug }}?v{{ strtotime($record->updated_at) }}' alt='{{ HTML::image_alt($record) }}' />
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
