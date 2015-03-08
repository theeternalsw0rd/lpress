@extends($view_prefix . '.layouts.dashboard')
@section('styles')
	@parent
@stop
@section('content')
	@parent
	<h2>
		{{ $title }}
	</h2>
	{!! HTML::collection_editor($collection, 'trash') !!}
	{!! $collection->render() !!}
@stop
@section('footer_scripts')
	@parent
@stop
