@extends($view_prefix . '.layouts.dashboard')
@section('styles')
	@parent
@stop
@section('content')
	@parent
	<h2>
		{{ $title }}
		<span class='small'>
			{{ HTML::new_model_link($new_model) }}&nbsp;
			{{ HTML::trash_bin_link($new_model) }}
		</span>
	</h2>
	{{ HTML::collection_editor($collection) }}
	{{ $collection->links() }}
@stop
@section('footer_scripts')
	@parent
@stop
