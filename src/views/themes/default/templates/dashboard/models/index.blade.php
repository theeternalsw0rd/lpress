@extends($view_prefix . '.layouts.dashboard')
@section('styles')
	@parent
@stop
@section('content')
	@parent
	<h2>{{ $title }} {{ HTML::new_model_link($new_model) }}</h2>
	{{ HTML::collection_editor($collection) }}
	{{ $collection->links() }}
@stop
@section('footer_scripts')
	@parent
@stop
